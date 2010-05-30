/**
 * Play saved data
 *
 * @since 0.1
 *
 * @todo highlight elements when clicked
 *
 * This script expects the following variables to be defined:
 * ajaxurl URL to the logging server
 * time_Ymd Timestamp of the visit (system time)
 * time_His Timestamp of the visit (system time)
 * sha1 Sha1sum of the IP of the visitor
 * @todo effectcolor Color for hover, focus, etc events
 */
effectcolor = '#ff0000';
jQuery(document).ready(function($){

	function browserinfo( browser ) {
		$.each(browser, function(i, val) {
			if(i=='version') {
				version = val;
			}
			if(val===true) {
				browser = i;
			}
		});
		$('#visitor-movies-infowin').append(browser + ' ' + version + '<br>');
	}

	/**
	 * Initialize the window
	 *
	 * @since 0.1
	 */
	function initialize() {
		/* make sure the info windows aren't in a postion:foo container */
		$('#visitor-movies-frontend-container').appendTo('body');
		$('#visitor-movies-infowin').append('<strong>You</strong><br>');
		browserinfo( $.browser );
		$.get(ajaxurl,
			{
				action: 'visitor_movies_log_get',
				sha1: sha1,
				Ymd: Ymd,
				His: His,
			}
			, function( response ) {
				playback( JSON.parse( response ) );
			}
		);
	}

	/**
	 * Visualize a mouse click
	 *
	 * @since 0.1
	 */
	function clickMouse(x,y) {
		x = parseInt(x) - 20;
		y = parseInt(y) - 16;
		$('#click').css( 'left', x + 'px' );
		$('#click').css( 'top', y + 'px' );
		$('#click').fadeIn(250).fadeOut(750);
	}
	
	/**
	 * Visualize a click on an element specified by name
	 *
	 * @todo We log the content but don't use it here?
	 *
	 * @since 0.2
	 */
	function click_by_name(name) {
		$('[name='+name+']').effect('highlight',{color:effectcolor},500);
	}
	
	/**
	 * Visualize a hover on an element specified by ID
	 *
	 * @todo Document class addition
	 * @todo precise matching
	 *
	 * @since 0.1
	 */
	function mouseEnterElementId(id) {
		selector = '#'+id;
		$(selector).addClass('visitor-movies-hover sfhover');
	}
	
	/**
	 * Finish a hover by Id
	 *
	 * @since 0.1
	 */
	function mouseLeaveElementId(id) {
		selector = '#'+id;
		$(selector).removeClass('visitor-movies-hover sfhover');
	}

	/**
	 * Visualize a hover on an element specified by content
	 *
	 * @todo Document class addition
	 * @todo we give the parent element the class as well. see suckerfish.
	 *
	 * @since 0.1
	 */
	//function mouseEnterElementContent(selector) {
	//	alert( JSON.stringify(selector) );
	//	$(selector).addClass('visitor-movies-hover sfhover');
	//	$(selector).parent().addClass('sfhover'); // fixme
	//	$(selector).fadeOut( 100 );
	//	//$("contains('kontaktieren Sie mich')").fadeOut( 500 );
	//}

	/**
	 * Finish a hover by content
	 *
	 * @since 0.1
	 */
	function mouseLeaveElementContent(content) {
		selector = 'a:contains(\'' + content + '\')';
		$(selector).removeClass('visitor-movies-hover sfhover');
		$(selector).parent().removeClass('sfhover'); //fixme
	}

	/**
	 * Visualize mous movement
	 *
	 * @since 0.1
	 */
	function positionCursor(x,y) {
		x = parseInt(x) - 20; // icon offset @todo configurable
		y = parseInt(y) - 16; // icon offset @todo configurable
		$('#cursor').css( 'left', x + 'px');
		$('#cursor').css( 'top', y + 'px');
	}
	
	/**
	 * Data entry, element by name
	 *
	 * @since 0.2
	 */
	function data_by_name(name,content) {
		el = $('[name='+name+']');
		el.attr('value',content);
		if(el[0].tagName == 'SELECT') {
			el.effect('highlight',{color:effectcolor},3000);
		}
	}

	function checkbox(name,value,checked) {
		el = $('[name='+name+']');
		$.each(el, function() {
			if($(this).attr('value') == value) {
				switch(checked) {
					case true:
						$(this).attr('checked','checked');
						break;
					default:
						$(this).removeAttr('checked');
				}
			}
		});
	}

	/**
	 * Show data entry input
	 *
	 * @since 0.1
	 * @deprecated 0.2
	 */
	function fillInput(name,content) {
		$("input[name='" + name + "']").attr('value', content);
	}
	
	/**
	 * Show data entry textarea
	 *
	 * @since 0.1
	 * @deprecated 0.2
	 */
	function fillTextArea(name,content) {
		$("textarea[name='" + name + "']").attr('value', content);
	}
	
	/**
	 * Scroll the window
	 *
	 * @since 0.1
	 */
	function scrollWindow(offset) {
		window.scrollTo(0, offset); // todo x-scrolling
	}
	
	/**
	 * Resize the window
	 *
	 * @since 0.1
	 * @todo window sizes don't seem to be accurate (logbar)
	 */
	function resizeWindow(width,height) {
		window.innerWidth = width;
		window.innerHeight = height;
	}

	/**
	 * Display some information
	 *
	 * @since 0.1
	 */
	function infoWin(fields) {
		$('#visitor-movies-infowin').append(fields[2] + ' : ' + fields[3] + '<br>');
	}
	
	/**
	 * Gets an event and fire the visualization
	 *
	 * @param array An event, format timestamp, event type, info, info [, ...]
	 * @since 0.1
	 */
	function dispatcher(ev) {
		// print current timestamp
		$('#visitor-movies-time-elapsed').html( ev['timestamp'] / 1000 );

		eventType = ev['eventType'];

		// print event info
		$('#visitor-movies-actionwin').html( eventType ); // @todo more info?

		switch( eventType ) {
			case 'p':
				positionCursor( ev['data']['x'], ev['data']['y'] );
				break;
			case 'n':
				infowin( ev['data']['msg'] );
				break;
			case 's':
				scrollWindow( ev['data']['scrollTop'] );
				break;
			case 'x':
				resizeWindow( ev['data']['width'], ev['data']['height'] );
				break;
			case 'c':
				clickMouse( ev['data']['x'], ev['data']['y'] );
				break;
			case 'mei':
				mouseEnterElementId( ev['data']['id'] );
				break;
			case 'moi':
				mouseLeaveElementId( ev['data']['id'] );
				break;
			case 'dn':
				data_by_name( ev['data']['name'], ev['data']['content'] );
				break;
			case 'cn':
				click_by_name( ev['data']['name'] );
				break;
			case 'cb':
				checkbox( ev['data']['name'], ev['data']['value'], ev['data']['checked'] );
				break;
			default:
				alert( 'dispatcher error: unknown event ' + eventType );
				break;
		}
		//else if(eventType == 'ce') {
		//	clickElement(fields[2]);
		//}
		//else if(eventType == 'mec') {
		//	mouseEnterElementContent(fields[2]);
		//}
		//else if(eventType == 'mlc') {
		//	mouseLeaveElementContent(fields[2]);
		//}
	}

	/**
	 * The playback function
	 *
	 * @since 0.1
	 */
	function playback( thelog ) {
		$('#visitor-movies-infowin').append('<strong>Visitor</strong><br>');
		browserinfo( thelog.config.browser );

		var my_events = new Array;
		var my_skips = new Array;
		var max_gap = 5000;
		var skip_ahead = 0;
		var last_at = 0;
		var fire_at = 0;

		$.each(thelog.events, function(index, ev) {
			now = ev['timestamp'];
			fire_at = now - skip_ahead;

			gap = now - last_at;
			if(gap > max_gap) {
				skip_ahead = skip_ahead + gap;
				fire_at = now - skip_ahead;
				message = 'I just skipped ' + parseInt(gap / 1000) + ' seconds';
				my_skips[index] = window.setTimeout(
					'flash("' + message +'", 3000)', fire_at
				);
			}

			my_events[index] = window.setTimeout( function() {
				dispatcher(ev)
			}, fire_at );

			$('#visitor-movies-actionwin').html( 'adding ' + index );
			$('#visitor-movies-time-total').html( now / 1000 );
			last_at = now;
		})
		exit = window.setTimeout( function() {
			$('#cursor').fadeOut( 1000 );
			$('#visitor-movies-thanks').fadeIn( 1000 );
		}, last_at - skip_ahead + 5000 );
	}

	initialize();
})

/**
 * Display a special message
 *
 * @since 0.1
 */
function flash(msg, time) {
	if ( time <= 1000 ) {
		time = 3000;
	}
	jQuery('#visitor-movies-flashwin').fadeIn( 100 );
	jQuery('#visitor-movies-flashwin').html( msg );
	jQuery('#visitor-movies-flashwin').fadeOut( time - 100 );
}

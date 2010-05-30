/**
 *
 * @since 0.1
 * @depends jquery, json2
 *
 * @todo log clicks on elements by name or content for playback
 * @todo log form submits (if still needed)
 * @todo vertical scroll
 * @todo copy & paste text, select
 * @todo when entering a div, save it's size, display warning during playback if
 * the difference is too big! or do something with percentages
 * @todo check for user stylesheets, if possible (?)
 * @todo log which button clicked
 *
 * This script expects the following variables to be defined:
 * ajaxurl URL to the logging server
 *
 */
jQuery(document).ready(function($){

	/**
	 * Config class
	 *
	 * @since 0.2
	 * @return none
	 */
	function VisitorMoviesConfig() {
		this.version = '0.2.0.2';
		this.format = function(i) {
			if(i<10) {
				return '0' + String(i);
			}
			return String(i);
		}
		this.url = location.href;
		this.Ymd_js = start.getFullYear()
			+ this.format( start.getMonth() + 1 )
			+ this.format( start.getDate() );
		this.His_js = this.format( start.getHours() )
			+ this.format( start.getMinutes() )
			+ this.format( start.getSeconds() );
		this.Ymd = Ymd;
		this.His = His;
		this.referrer = document.referrer;
		this.browser = $.browser;
	}

	/**
	 * Event class
	 *
	 * @since 0.2
	 * @return none
	 * @fixme this is visitor's local time..................
	 */
	function VisitorMoviesEvent(eventType, data) {
		this['timestamp']	= new Date().getTime() - start_time;
		this['eventType']	= eventType;
		this['data']		= data;
	}

	/**
	 * Log class
	 *
	 * @since 0.2
	 * @return none
	 */
	function VisitorMoviesLog() {
		this.config = new VisitorMoviesConfig();
		this.events = [];
	}

	/**
	 * Submit the log to the server
	 *
	 * @since 0.1
	 * @return none
	 */
	function submit() {
		$.post(ajaxurl,
			{
				action: 'visitor_movies_log',
				thelog: JSON.stringify( thelog )
			}
		);
	}

	/**
	 * This function resets the timeout to one second and submits
	 * the data when the timeout is reached.
	 * This means data is submitted after one second of inactivity.
	 *
	 * @since 0.1
	 * @return none
	 */
	function log(ev) {
		thelog.events.push( ev );
		window.clearTimeout(timeout);
		timeout = window.setTimeout( function() {
			submit();
			thelog.events = []; // clear the log
		}, 1000);
	}

	/**
	 * Log scroll position
	 *
	 * @since 0.1
	 * @return none
	 */
	function log_scroll() {
		var data = {
			scrollTop	:	$(window).scrollTop()
		};
		var ev = new VisitorMoviesEvent( 's', data );
		log(ev);
	}

	/**
	 * Log window resize
	 *
	 * @since 0.1
	 * @return none
	 */
	function log_resize() {
		var data = {
			width	:	$(window).width(),
			height	:	$(window).height()
		};
		var ev = new VisitorMoviesEvent( 'x', data );
		log (ev);
	}

	/**
	 * Log event and specify element by id.
	 *
	 * @since 0.1
	 * @return success
	 */
	function log_by_id(el,eventType) {
		var id = el.attr('id');

		if (typeof(id) !== 'undefined' && id != '') {
			var data = {
				id:	id
			};
			var ev = new VisitorMoviesEvent( eventType + 'i', data );
			log(ev);
			return true;
		}
		return false;
	}

	/**
	 * Log event and specify element by content.
	 *
	 * @since 0.1
	 * @return success
	 * @deprecated 0.2
	 */
	function log_by_content(el,eventType) {
		var content	= el.html();

		if (typeof(content) !== 'undefined' && content != '') {
			var data = {
				content	: content
			};
			var ev = new VisitorMoviesEvent( eventType + 'c', data );
			log(ev);
			return true;
		}
		return false;
	}

	/**
	 * Log event and specify element by name. Log element's content.
	 *
	 * @since 0.2
	 * @return success
	 */
	 function log_by_name_input(el,eventType) {
	 	var name = el.attr('name');

		if (typeof(name) !== 'undefined' && name != '') {
			var data = {
				name	: name,
				content : el.attr('value')
			};
			var ev = new VisitorMoviesEvent( eventType + 'n', data );
			log(ev);
			return true;
		}
		return false;
	 }

	/**
	 * Log a checkbox change
	 *
	 * @since 0.2
	 * @return none
	 */
	 function log_by_name_checkbox(el) {
		var data = {
			name	: el.attr('name'),
			value	: el.attr('value'),
			checked	: el.attr('checked')
		};
		var ev = new VisitorMoviesEvent( 'cb', data );
		log(ev);
	 }

	/**
	 * Log a message
	 *
	 * @since 0.2
	 * @return none
	 */
	function log_message(msg) {
		var data = {
			msg: msg
		};
		var ev = new VisitorMoviesEvent( 'n', data );
		submit();
	}

	/**
	 * Initialize some variables
	 */
	var timeout;
	var start = new Date();
	var start_time = start.getTime();
	var browser = '';
	var thelog = new VisitorMoviesLog();

	/**
	 * Initial logging
	 */
	// doesn't seem to be necessary? check if all browsers do a resize
	log_resize();
	log_scroll();

	/**
	 * Attach log actions to recording-worthy events
	 */

	/* global events */
	$(window).mousemove(function(e){
		var data = {
			x	: e.pageX,
			y	: e.pageY
		}
		var ev = new VisitorMoviesEvent( 'p', data );
		log(ev)
	}); 

	$(window).click(function(e){
		var data = {
			x : e.pageX,
			y : e.pageY
		};
		var ev = new VisitorMoviesEvent( 'c', data );
		log(ev);
	}); 

	$(window).scroll( log_scroll ); // s
	
	$(window).resize( log_resize() ); // x

	$(window).unload( log_message('window unloaded') );

	/* forms */
	/* content */
	$('textarea').keydown( function() {
		log_by_name_input($(this),'d');
	});
	$('textarea').blur( function() {
		log_by_name_input($(this),'d');
	});

	$('input:text').keydown( function() {
		log_by_name_input($(this),'d');
	});
	$('input:text').blur( function() {
		log_by_name_input($(this),'d');
	});

	$('select').change( function() {
		log_by_name_input($(this),'d');
	});

	$('input:checkbox').change( function() {
		log_by_name_checkbox($(this));
	});

	$('input:radio').change( function() {
		log_by_name_checkbox($(this));
	});

	/* clicks, focus etc */
	$('textarea').focus( function() {
		log_by_name_input($(this),'c'); // treat focus as click, not data entry
	});
	$('input:text').focus( function() {
		log_by_name_input($(this),'c'); // treat as click
	});

	/* hover */
	$('a').mouseenter( function() {
		log_by_id($(this),'me');
	});
	$('a').mouseleave( function() {
		log_by_id($(this),'mo');
	});

})

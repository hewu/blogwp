/**
 * Package main JavaScript
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.6
 */

/**
 * Globals
 */
var mouseAbsX = 0, mouseAbsY = 0;

//alert(window.location.protocol + '//' + window.location.hostname + '/');

var ajaxURL = window.location.protocol + '//' + window.location.hostname + '/<?php

	echo str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('/'.basename(dirname(__FILE__)), '', dirname(__FILE__))) . '/';

?>ajax_ro.php';

var imgURL = window.location.protocol + '//' + window.location.hostname + '/<?php

	echo str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('/'.basename(dirname(__FILE__)), '', dirname(__FILE__))) . '/';

?>img/';

//alert(ajaxURL);
//alert(imgURL);
//alert('<?php echo basename(dirname(__FILE__)); ?>');

/**
 * Return an array with the page dimensions [width, height]
 */
function getPageDimensions() {
	var body = document.getElementsByTagName('body')[0];
	var bodyOffsetWidth = 0, bodyOffsetHeight = 0, bodyScrollWidth = 0, bodyScrollHeight = 0, this_pagedim = [0,0];

	if(typeof document.documentElement!='undefined' && typeof document.documentElement.scrollWidth!='undefined')
	{
		this_pagedim[0] = document.documentElement.scrollWidth;
		this_pagedim[1] = document.documentElement.scrollHeight;
	}
	bodyOffsetWidth  = body.offsetWidth;
	bodyOffsetHeight = body.offsetHeight;
	bodyScrollWidth  = body.scrollWidth;
	bodyScrollHeight = body.scrollHeight;

	if(bodyOffsetWidth>this_pagedim[0])  { this_pagedim[0] = bodyOffsetWidth; }
	if(bodyOffsetHeight>this_pagedim[1]) { this_pagedim[1] = bodyOffsetHeight; }
	if(bodyScrollWidth>this_pagedim[0])  { this_pagedim[0] = bodyScrollWidth; }
	if(bodyScrollHeight>this_pagedim[1]) { this_pagedim[1] = bodyScrollHeight; }

	return this_pagedim;
}

/**
 * Initialize the function to display the popup tag
 */
function pop(msg, link, secs) {
	if(!msg) { msg = 'pop(missing msg!)'; }
	if(msg=='*closePop')
	{
		var popWin = document.getElementById('myeasydb_popWin');
		if(popWin)
		{
			document.body.style.cursor = 'default';
			popWin.style.display = 'none';
		}
		return;
	}
	setTimeout('pop_show(\'' + msg + '\',\'' + link + '\',\'' + secs + '\')', 10);
}

/**
 * Display the popup tag
 */
function pop_show(msg, link, secs) {

	if(!secs) { secs = getCookie('pop_time'); }
	if(!secs || isNaN(secs)) { secs = 1; }

//alert('msg:'+msg+' link:'+link+' sec:'+secs);

	document.body.style.cursor = 'wait';
	var popWin = document.getElementById('myeasydb_popWin');
	if(popWin)
	{
		var this_pagedim = getPageDimensions();

		popWin.innerHTML = '<div style="margin-top:12.5%;">' + msg + '</div>';
		popWin.style.top    = '0px';
		popWin.style.left   = '0px';
		popWin.style.width  = this_pagedim[0] + 'px';
		popWin.style.height = this_pagedim[1] + 'px';
		popWin.style.display= 'block';
	}
	//
	//	redirect
	//
	if(link)
	{
		setTimeout("window.location='" + link + "';", secs * 1000);
	}
	else
	{
		setTimeout("pop('*closePop')", secs * 1000);
	}
}

/**
 * Toggle the display status of a functionality element
 * taking care of its related cookie to represent the same
 * status when reloading the page
 */
function el_display_toggler(elID, togIMG) {

//alert(togIMG.length);
//alert(location.href);
//alert(el.src);

	if(togIMG.length>0)
	{
		var el = document.getElementById(togIMG);

		if(el.src==imgURL + 'screen-options-right-up.gif')
		{
			document.getElementById(elID).style.display='none';
			el.src = imgURL + 'screen-options-right.gif';
			setCookie('myeasydb'+elID, 0);
		}
		else
		{
			document.getElementById(elID).style.display='block';
			el.src = imgURL + 'screen-options-right-up.gif';
			setCookie('myeasydb'+elID, 1);
		}
	}
}

/**
 * Sets the background for a number of elements
 */
function resetSelectedTdTables(t, prefix) {

	var el = '';
	for(i=0;i<t;i++)
	{
		el = document.getElementById(prefix+i);
		if(el)
		{
			el.style.background='transparent';
		}
	}
}


/**
 * return the value of the radio button that is checked
 * return an empty string if none are checked, or
 * there are no radio buttons
 */
function getCheckedValue(radioObj) {
	if(!radioObj) {
		return '';
	}
	var radioLength = radioObj.length;

	if(radioLength == undefined) {
		if(radioObj.checked) {
			return radioObj.value;
		}
		else {
			return '';
		}
	}

	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return '';
}
/**
 * set the radio button with the given value as being checked
 * do nothing if there are no radio buttons
 * if the given value does not exist, all the radio buttons
 * are reset to unchecked
 */
function setCheckedValue(radioObj, newValue) {
	if(!radioObj){
		return;
	}
	var radioLength = radioObj.length;
	if(radioLength == undefined) {
		radioObj.checked = (radioObj.value == newValue.toString());
		return;
	}
	for(var i = 0; i < radioLength; i++) {
		radioObj[i].checked = false;
		if(radioObj[i].value == newValue.toString()) {
			radioObj[i].checked = true;
		}
	}
}




/*===================
 *
 *	COOKIES
 *
 */

/**
 * Sets a Cookie with the given name and value.
 *
 * name       Name of the cookie
 * value      Value of the cookie
 * [expires]  Expiration date of the cookie (default: end of current session)
 * [path]     Path where the cookie is valid (default: path of calling document)
 * [domain]   Domain where the cookie is valid
 *              (default: domain of calling document)
 * [secure]   Boolean value indicating if the cookie transmission requires a
 *              secure transmission
 */
function setCookie(name, value, expires, path, domain, secure)
{
	var theDate = new Date(); if(expires) { theDate.addDays(expires); }
	document.cookie= name + "=" + escape(value) +
		((expires) ? "; expires=" + theDate.toGMTString() : "") +
		((path) ? "; path=" + path : "") +
		((domain) ? "; domain=" + domain : "") +
		((secure) ? "; secure" : "");
}
Date.prototype.addDays = function(d) { this.setDate( this.getDate() + d ); };
/**
 * Gets the value of the specified cookie.
 *
 * name  Name of the desired cookie.
 *
 * Returns a string containing value of specified cookie,
 *   or null if cookie does not exist.
 */
function getCookie(name)
{
	var dc = document.cookie;
	var prefix = name + "=";
	var begin = dc.indexOf("; " + prefix);
	if (begin == -1) { begin = dc.indexOf(prefix); if (begin != 0) return null; } else { begin += 2; }
	var end = document.cookie.indexOf(";", begin);
	if (end == -1) { end = dc.length; }
	return unescape(dc.substring(begin + prefix.length, end));
}
/**
 * Deletes the specified cookie.
 *
 * name      name of the cookie
 * [path]    path of the cookie (must be same as path used to create cookie)
 * [domain]  domain of the cookie (must be same as domain used to create cookie)
 */
function deleteCookie(name, path, domain)
{
	if (getCookie(name))
	{
		document.cookie = name + "=" +
			((path) ? "; path=" + path : "") +
			((domain) ? "; domain=" + domain : "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
	}
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function getScrollingPosition() {
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	var position=[0,0];

	if(typeof window.pageYOffset!='undefined')				{ position=[window.pageXOffset,window.pageYOffset]; }
	if(typeof document.documentElement.scrollTop!='undefined' &&
			  document.documentElement.scrollTop>0)			{ position=[document.documentElement.scrollLeft,document.documentElement.scrollTop]; }
	else if(typeof document.body.scrollTop!='undefined')	{ position=[document.body.scrollLeft,document.body.scrollTop]; }

//document.title=position;

	return position;
}
//~~~~~~~~~~~~~~~~~~~~~~~~
function mouseMove(e) {
//~~~~~~~~~~~~~~~~~~~~~~~~
	if(!e) { var e = window.event; }

	var scroll = Array();
	scroll = getScrollingPosition();

	//	get mouse coords
	//
	if(document.all)
	{
		//	browser is IE
		//
		mouseAbsX = event.clientX;
		mouseAbsY = event.clientY;
	}
	else
	{
		//	browser is NOT IE
		//
		mouseAbsX = e.pageX;
		mouseAbsY = e.pageY;
	}
	mouseAbsX = mouseAbsX + scroll[0];
	mouseAbsY = mouseAbsY - scroll[1];

//window.status='mouseAbsX:'+mouseAbsX+' mouseAbsY:'+mouseAbsY+' scroll[0]:'+scroll[0]+' scroll[1]:'+scroll[1];
}
document.onmousemove = mouseMove;

/**
 * Set the 'to' date equals to the 'from' date in filter forms
 * @since 0.0.6
 */
function set_date(fromID, toID) {

	var elf = document.getElementById(fromID);
	var elt = document.getElementById(toID);

	if(elf && elt)
	{
		elt.value = elf.value;
		if(elt.value=='')
		{
			//	found both fields but the 'from' date is not set yet
			//
			setTimeout("set_date('" + fromID + "','" + toID +"');", 250);
			return;
		}
		return;
	}

	//	one or both fields are not yet available in the dom
	//
	setTimeout("set_date('" + fromID + "','" + toID +"');", 250);
}

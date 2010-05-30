<?php
/*
Plugin Name: Club Manager
Plugin URI: http://wp-plugins.clark-lowes.info/
Description:Club Manager - custom database tables for managing a small club or society.
Version: 0.0.2
Author: Julian Clark-Lowes
Author URI: http://wp-plugins.clark-lowes.info/
*/
?>
<?php
/*  Copyright 2010 Julian Clark-Lowes (email : others@julian.clark-lowes.info)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php


if (!isset($pluginprefix))
	$pluginprefix = "";
$pluginprefix .= "club_manager/";
$plugintitle = "Club Mngr";

include "jcl_stop_press/jcl_stop_press.php";
include "jcl_news/jcl_news.php";
include "jcl_programme/jcl_programme.php";
include "jcl_recent_event/jcl_recent_event.php";



function club_manager(){

}

function club_manager_install(){
	jcl_stop_press_install();
	jcl_news_install();
	jcl_programme_install();
	jcl_recent_event_install();
	
}


function club_manager_uninstall(){

	jcl_stop_press_uninstall();
	jcl_news_uninstall();
	jcl_programme_uninstall();
	jcl_recent_event_uninstall();
	
}

register_activation_hook(__FILE__,'club_manager_install');
register_deactivation_hook(__FILE__,'club_manager_uninstall');

function club_manager_create_submenu($pluginprefix){
	global $plugintitle;
	if ($pluginprefix == "")
		$pluginprefix = "club_manager/";
	$pluginname = "club_manager";
	if (function_exists('add_menu_page')) {
		wp_enqueue_style("$pluginname",get_bloginfo('wpurl') . "/wp-content/plugins/$pluginprefix$pluginname.css",false," ");
		//get_bloginfo('wpurl') . "/wp-content/plugins/$pluginname/blank.png"
		add_menu_page("Club Manager", "Club Manager", 'edit_posts', $pluginprefix."jcl_programme/jcl_programme.php", '', plugins_url('$pluginname/blank.png'));
		if (function_exists('add_submenu_page')) {
			add_submenu_page($pluginprefix."jcl_programme/jcl_programme.php", "Programme", "Programme", 'edit_posts', $pluginprefix.'jcl_programme/jcl_programme.php');
			add_submenu_page($pluginprefix."jcl_programme/jcl_programme.php", "Stop Press", "Stop Press", 'edit_posts', $pluginprefix.'jcl_stop_press/jcl_stop_press.php');
			add_submenu_page($pluginprefix."jcl_programme/jcl_programme.php", "Recent Events", "Recent Events", 'edit_posts', $pluginprefix.'jcl_recent_event/jcl_recent_event.php');
			add_submenu_page($pluginprefix."jcl_programme/jcl_programme.php", "News", "News", 'edit_posts', $pluginprefix.'jcl_news/jcl_news.php');
		}
		
	}
}
if ($pluginprefix == "club_manager/"){
	add_action('admin_menu', 'club_manager_create_submenu');
	$pluginprefix = "";
}
?>
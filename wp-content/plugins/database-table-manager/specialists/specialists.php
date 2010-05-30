<?php
/*
Plugin Name: Specialists
Plugin URI: http://wp-plugins.clark-lowes.info/
Description:Specialists - custom database tables demonstrating the management of many to many link tables and lookup tables.
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
$pluginprefix .= "specialists/";
$plugintitle = "Specialists";


include "jcl_specialist/jcl_specialist.php";
include "jcl_specialism/jcl_specialism.php";
include "jcl_sp_links_sp/jcl_sp_links_sp.php";
include "jcl_lookup_regions/jcl_lookup_regions.php";


function specialists(){

}

function specialists_install(){
	
	jcl_specialist_install();
	jcl_specialism_install();
	jcl_lookup_regions_install();
	jcl_sp_links_sp_install();
	
}


function specialists_uninstall(){
	
	jcl_specialist_uninstall();
	jcl_specialism_uninstall();
	jcl_lookup_regions_uninstall();
	jcl_sp_links_sp_uninstall();
	
}

register_activation_hook(__FILE__,'specialists_install');
register_deactivation_hook(__FILE__,'specialists_uninstall');

function specialists_create_submenu($pluginprefix){
	global $plugintitle;
		if ($pluginprefix == "")
			$pluginprefix = "specialists/";
			
		$pluginname = "specialists";
		if (function_exists('add_menu_page')) {
		wp_enqueue_style("$pluginname",get_bloginfo('wpurl') . "/wp-content/plugins/$pluginprefix$pluginname.css",false," ");
		add_menu_page("Specialists", "Specialists", 'edit_posts', $pluginprefix."jcl_specialist/jcl_specialist.php", '', plugins_url('$pluginname/blank.png'));
		if (function_exists('add_submenu_page')) {
			add_submenu_page($pluginprefix."jcl_specialist/jcl_specialist.php", "Specialist", "Specialist", 'edit_posts', $pluginprefix.'jcl_specialist/jcl_specialist.php');
			add_submenu_page($pluginprefix."jcl_specialist/jcl_specialist.php", "Specialism", "Specialism", 'edit_posts', $pluginprefix.'jcl_specialism/jcl_specialism.php');
			add_submenu_page($pluginprefix."jcl_specialist/jcl_specialist.php", "Links", "Links", 'edit_posts', $pluginprefix.'jcl_sp_links_sp/jcl_sp_links_sp.php');
			add_submenu_page($pluginprefix."jcl_specialist/jcl_specialist.php", "Regions", "Regions", 'edit_posts', $pluginprefix.'jcl_lookup_regions/jcl_lookup_regions.php');
		}
	}
}
if ($pluginprefix == "specialists/"){
	add_action('admin_menu', 'specialists_create_submenu');
	$pluginprefix = "";
}
?>
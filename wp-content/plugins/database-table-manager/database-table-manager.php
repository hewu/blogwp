<?php
/*
Plugin Name: Database Table Manager
Plugin URI: http://wp-plugins.clark-lowes.info/
Description: Database Table Manager - custom database tables in wordpress.
Version: 0.0.2
Author: Julian Clark-Lowes
Author URI: http://wp-plugins.clark-lowes.info/
License: GPL2
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


$pluginprefix = "database-table-manager/";
$plugintitle = "DB Table Mngr";

include "club_manager/club_manager.php";

$pluginprefix = "database-table-manager/";
$plugintitle = "DB Table Mngr";

include "specialists/specialists.php";


function database_table_manager(){

}

function database_table_manager_install(){

	club_manager_install();
	specialists_install();

}


function database_table_manager_uninstall(){

	club_manager_uninstall();
	specialists_uninstall();

}

register_activation_hook(__FILE__,'database_table_manager_install');
register_deactivation_hook(__FILE__,'database_table_manager_uninstall');

function database_table_manager_create_submenu(){
	global $plugintitle;
	global $pluginprefix;
	if (function_exists('add_menu_page')) {

		club_manager_create_submenu("database-table-manager/club_manager/");
		specialists_create_submenu("database-table-manager/specialists/");
	
	}
}
add_action('admin_menu', 'database_table_manager_create_submenu');
?>
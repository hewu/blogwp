<?php
/*  Copyright 2010 Credit Card Finder Pty Ltd  (email: info@creditcardfinder.com.au)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include_once('../../../wp-config.php');
include_once('../../../wp-load.php');
include_once('../../../wp-includes/wp-db.php');

global $current_user;
global $wpdb;
get_currentuserinfo();

$q = strtolower($_GET["q"]);
if (!$q) return;

$type = mysql_real_escape_string($_GET["type"]);

$rows = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."ccf_labels WHERE user_id = '".$current_user->ID."' AND type = '".$type."' ORDER BY name ASC");

$items = array();

foreach($rows as $row) {
	$items[] = $row->{'name'};
}

foreach ($items as $item) {
	if (strpos(strtolower($item), $q) !== false) {
		echo ucwords("$item\n");
	}
}

?>
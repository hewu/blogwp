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

function ccf_create_table($name, $sql) {
	global $wpdb;
	
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix.$name."'") != $wpdb->prefix.$name) {
		$sql = "CREATE TABLE " . $wpdb->prefix.$name . " (
			$sql
		);";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

function ccf_personal_wealth_dbsetup() {
	ccf_create_table(
		"ccf_networth",
		"`ccf_networth_id` varchar(40) NOT NULL,
		 `user_id` int(8) NOT NULL,
		 `month_year` date NOT NULL,
		 `networth` float NOT NULL,
		 `modify_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		 `create_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		 PRIMARY KEY (`ccf_networth_id`)"
	);
	
	ccf_create_table(
		"ccf_labels",
		"`ccf_labels_id` int(8) NOT NULL AUTO_INCREMENT,
		 `user_id` int(8) NOT NULL,
		 `name` varchar(255) NOT NULL,
		 `type` varchar(20) NOT NULL,
		 `order` int(11) NOT NULL,
		 `modify_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		 `create_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		 PRIMARY KEY (`ccf_labels_id`)"
	);
	
	ccf_create_table(
		"ccf_assets",
		"`ccf_assets_id` int(8) NOT NULL AUTO_INCREMENT,
		 `ccf_networth_id` varchar(40) NOT NULL,
		 `ccf_labels_id` int(8) NOT NULL,
		 `value` float NOT NULL,
		 `private` tinyint(1) NOT NULL,
		 `modify_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		 `create_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		 PRIMARY KEY (`ccf_assets_id`)"
	);
	
	ccf_create_table(
		"ccf_liabilities",
		"`ccf_liabilities_id` int(8) NOT NULL AUTO_INCREMENT,
		 `ccf_networth_id` varchar(40) NOT NULL,
		 `ccf_labels_id` int(8) NOT NULL,
		 `value` float NOT NULL,
		 `private` tinyint(1) NOT NULL,
		 `modify_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		 `create_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		 PRIMARY KEY (ccf_liabilities_id)"
	);
}

function ccf_personal_wealth_defaults() {
	### Networth Graph ###
	# Main Title
	add_option('pw_main_title', 'My Net Worth');
	add_option('pw_main_title_font_name', 'Georgia');
	add_option('pw_main_title_font_color', '0xffffff');
	add_option('pw_main_title_font_size', '24');
	add_option('pw_main_title_bg_color', '0x5B9EDC');
	# Vertical
	add_option('pw_vert_line_color', '0x4A9586');
	add_option('pw_vert_font_name', 'Verdana');
	add_option('pw_vert_font_color', '0x00559F');
	add_option('pw_vert_font_size', '11');
	# Horizontal
	add_option('pw_horiz_line_color', '0x4A9586');
	add_option('pw_horiz_font_name', 'Verdana');
	add_option('pw_horiz_font_color', '0x00559F');
	add_option('pw_horiz_font_size', '10');
	# Graph
	add_option('pw_graph_do_not_fill', 'true');
	add_option('pw_graph_gradient_color_1', '0x00559F');
	add_option('pw_graph_gradient_color_2', '0x5B9EDC');
	add_option('pw_graph_no_line', 'true');
	add_option('pw_graph_line_color', '0x00559F');
	add_option('pw_graph_line_width', '3');
	# Data Points
	add_option('pw_graph_no_data_points', 'true');
	add_option('pw_data_point_color', '0x00559F');
}

function ccf_personal_wealth_activate() {
	ccf_personal_wealth_dbsetup();
	ccf_personal_wealth_defaults();
}
?>
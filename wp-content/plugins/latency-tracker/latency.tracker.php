<?php
/*
Plugin Name: Latency Tracker
Plugin URI: http://skfox.com/2008/10/09/latency-tracker-phpmysql-tracking-for-wordpress/
Description: Keeps track of the queries and time to load Wordpress. <a href="edit.php?page=latency.tracker">View your data</a>.
Version: 2.1
Author: Shaun Kester
Author URI: http://skfox.com
*/

/*  Copyright 2010  Shaun Kester  (email : shaun@skfox.com)

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

//Include FusionCharts functionality
include("FusionCharts/FusionCharts.php");


// Run on plugin activation
function lt_install () 
{
	global $wpdb;
	
	// Create the tracking table
	$table_name = lt_get_table_name();
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
		id mediumint(16) NOT NULL AUTO_INCREMENT,
		longdatetime datetime NOT NULL,
		qcount mediumint(16) NOT NULL,
		qtime float NOT NULL,
		qpage varchar(255) NOT NULL,
		useragent varchar(255) NOT NULL,
		UNIQUE KEY id (id)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	// Insert default plugin options
	$lt_options = array(
		'lt_graph_width' => '650',
		'lt_graph_height' => '450',
		'lt_recent_requests' => '50',
		'lt_max_records' => 500
	);
	add_option('plugin_latencytracker_settings', $lt_options);
	
	// Register the cron job to limit max records 
	wp_schedule_event(time(), 'hourly', 'lt_clear_max');
}

// Run on plugin deactivation
function lt_uninstall () 
{
	global $wpdb;
	
	//Drop the table that we created
	$table_name = lt_get_table_name();
	if($wpdb->get_var("show tables like '$table_name'") == $table_name) {
		$wpdb->query("DROP TABLE $table_name");
	}
	
	// Remove the plugin options
	delete_option('plugin_latencytracker_settings');
	
	// Remove the cron job to limit max records 
	wp_clear_scheduled_hook('lt_clear_max');
}

// This is the function that stores your tracking data to mySQL
// Bound to wp_footer() function. Won't work unless the theme calls wp_footer
function lt_store_timer_data()
{
	global $wpdb;
	$table_name = lt_get_table_name();
	$lt_data = array(
		'longdatetime' => date('Y-m-d H:i:s', time()),
		'qcount' => get_num_queries(),
		'qtime' => timer_stop(0,3),
		'qpage' => "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
		'useragent' => $_SERVER['HTTP_USER_AGENT']
	);	
	$wpdb->insert($table_name, $lt_data);
}

// Displays the panel at WP-Admin >> Manage >> Latency Tracker
function lt_manage_panel() 
{
	global $wpdb;
	$table_name = lt_get_table_name();
	$to_average = 0;

	// Run any optional commands
	if ( $_REQUEST['doClearOverage'] == 'yes' ) {
		do_action('lt_clear_max');
		$message = '<div id="message" class="updated fade"><p><strong>The records overage has been cleared</strong></p></div>';
	}
	
	// Get the options array
	$options = get_option('plugin_latencytracker_settings');
	$lt_recent_requests = $options['lt_recent_requests'];
	
	// Get the record count
	$record_count = lt_get_record_count();
	
	// Get most recent requests
	$recent_results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY longdatetime DESC LIMIT 0, $lt_recent_requests");

	// Get min, max, and average for qtime and qcount
	$statistic = $wpdb->get_row("SELECT max(qtime) as max_time,min(qtime) as min_time,avg(qtime) as avg_time,max(qcount) as max_queries,min(qcount) as min_queries,avg(qcount) as avg_queries FROM $table_name");

	// Start the page content
	echo '<div id="divLatencyTrackerContent" class="wrap">';
	echo '<h2>Latency Tracker</h2>';	
	echo $message;
	if($record_count == 0){		
		echo '<p>Not enough records yet, give it some time.</p>';		
		echo '</div>';
		return;	
	}
	echo '<p>This plugin tracks the number of queries and processing time for those queries for each hit to Wordpress.</p>';
	echo '<div class="tabmenu">';
	echo '<ul>';
	echo '<li><a class="selected" href="#tab1">Data</a></li> ';
	echo '<li><a href="#tab2">Graph</a></li>';
	echo '<li><a href="#tab3">Recent Requests</a></li>';
	echo '</ul>';
	echo '</div>';
	echo '<div class="tabmenuline"></div>';
	
	// Data tab
	echo '<div id="tab1">';
		// Queries Table
		echo '<h3>Queries (Database requests)</h3>';
		echo "<table class='widefat' cellpadding='1' cellspacing='0' border='1'>";
		echo "<thead><tr> <th>Min</th> <th>Max</th> <th>Average</th> </tr></thead>";
			echo "<tr>";
			echo "<td>".number_format($statistic->min_queries)."</td>";
			echo "<td>".number_format($statistic->max_queries)."</td>";
			echo "<td>".number_format($statistic->avg_queries)."</td>";
			echo "</tr>"; 
		echo "</table>";	
		
		// Time Table
		foreach ($recent_results as $recent_result) 
		{
			$to_average += number_format($recent_result->qtime,3);
		}
		$avg_last_x = number_format($to_average / $options['lt_recent_requests'],3);
		echo '<h3>Time (Seconds to process)</h3>';
		echo "<table class='widefat' cellpadding='1' cellspacing='0' border='1'>";
		echo "<thead><tr> <th>Min</th> <th>Max</th> <th>Average (Last $lt_recent_requests requests)</th> <th>Average (All time)</th> </tr></thead>";
			echo "<tr>";
			echo "<td>".number_format($statistic->min_time,3)."</td>";
			echo "<td>".number_format($statistic->max_time,3)."</td>";
			if ($record_count > $options['lt_recent_requests'])
				echo "<td>$avg_last_x</td>";
			else
				echo "<td>Not enough requests yet...</td>";
			echo "<td>".number_format($statistic->avg_time,3)."</td>";
			echo "</tr>"; 
		echo "</table>";	
	echo '</div>';
	
	// Graph tab
	echo '<div id="tab2">';
		$strXML = "<graph caption='Last $lt_recent_requests requests' subcaption='In seconds' lineColor='D54E21' xAxisName='Date' yAxisMinValue='0' yAxisName='Seconds' decimalPrecision='3' formatNumberScale='0' numberPrefix='' showNames='0' showValues='0' rotateNames='1' showAlternateHGridColor='1' AlternateHGridColor='F9F9F9' divLineColor='333333' divLineAlpha='20' alternateHGridAlpha='5'>";
			foreach ($recent_results as $recent_result) {
				$strXML .= "<set name='" . $recent_result->longdatetime . "' value='" . $recent_result->qtime . "' />";
			}
		$strXML .= "</graph>";
		echo renderChart(plugins_url('FusionCharts/FCF_Line.swf', __FILE__ ), "", $strXML, "TimeChart", $options['lt_graph_width'], $options['lt_graph_height'], false, false);	
	echo '</div>';
	
	// Recent requests tab
	echo '<div id="tab3">';
		// Recent Requests Table
		echo "<table cellpadding='1' cellspacing='0' border='1' id='tblRecentRequests' class='tablesorter'>";
		echo "<thead><tr><th>Date / Time</th><th>Page</th><th>Queries</th><th>Time</th></tr></thead>";
		echo '<tbody>';
		foreach ($recent_results as $recent_result) {
			$class = 'odd' == $class ? '' : 'odd';
			echo "<tr class='$class'>";
			echo "<td>".$recent_result->longdatetime."</td>";
			echo "<td><a href='".$recent_result->qpage."'>".$recent_result->qpage."</a><br>".$recent_result->useragent."</td>";
			echo "<td>".$recent_result->qcount."</td>";
			echo "<td>".$recent_result->qtime."</td>";
			echo "</tr>";
		} 
		echo '</tbody>';
		echo "</table>";	
	echo '</div>';
	
	//End the page content
	echo '</div>';
	if ($record_count > $options['lt_max_records'])
	{
		echo '<p style="color: red"><i>Records: '. $record_count .'</i></p>';
		echo '<form method="post">';                                            
		echo '<input type="hidden" name="doClearOverage" value="yes">';
		echo '<p class="submit"><input type="submit" class="button-primary" value="Clear records overage" /></p>';
		echo '</form>';			
	}
	else 
	{
		echo '<p><i>Records: '. $record_count .'</i></p>';
	}
	echo '<hr />';
}

// Displays the panel at WP-Admin >> Settings >> LT Settings
function lt_settings_panel() 
{
	$message = '';
	
	// Save the options
	if( isset($_POST['info_update']) ) 
	{
		check_admin_referer('lt_settings_panel_update_options');
		$new_options = $_POST['latencytracker'];
		update_option( 'plugin_latencytracker_settings', $new_options);
		$message = '<div id="message" class="updated fade"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
	}
	else
	{
		check_admin_referer();
	}
	
	// Get the options array
	$options = get_option('plugin_latencytracker_settings');	
	echo '<div class="wrap">';
	echo '<h2>Latency Tracker Settings</h2>';
	echo $message;
	echo '<form method="post">';
		wp_nonce_field('lt_settings_panel_update_options');
		echo '<table class="form-table">';
		echo '<tr valign="top">';
		echo '<th scope="row">Graph Width</th>';
		echo '<td><input type="text" name="latencytracker[lt_graph_width]" value="'. $options['lt_graph_width'] .'" /></td>';
		echo '</tr>';
		echo '<tr valign="top">';
		echo '<th scope="row">Graph Height</th>';
		echo '<td><input type="text" name="latencytracker[lt_graph_height]" value="'. $options['lt_graph_height'] .'" /></td>';
		echo '</tr>';
		echo '<tr valign="top">';
		echo '<th scope="row">Recent Requests</th>';
		echo '<td><input type="text" name="latencytracker[lt_recent_requests]" value="'. $options['lt_recent_requests'] .'" /></td>';
		echo '</tr>';
		echo '<tr valign="top">';
		echo '<th scope="row">Max Records</th>';
		echo '<td><input type="text" name="latencytracker[lt_max_records]" value="'. $options['lt_max_records'] .'" /></td>';
		echo '</tr>';					
		echo '</table>';
		echo '<p class="submit">';
		echo '<input type="submit" name="info_update" value="Save Changes" />';
		echo '</p>';
	echo '</form>';
	echo '</div>';
}

function lt_clear_max_run() 
{
	global $wpdb;
	
	$table_name = lt_get_table_name();
	
	// Get the options array
	$options = get_option('plugin_latencytracker_settings');
	
	// Get the record count 
	$record_count = lt_get_record_count();
	
	if ($record_count > $options['lt_max_records'])
	{
		// Delete the overage
		$record_overage = $record_count - $options['lt_max_records'];
		$query = "DELETE FROM " .$table_name ." ORDER BY ID ASC LIMIT ".$record_overage;
		$query_result = $wpdb->query($query);
	}	
}

function lt_get_table_name() 
{
	global $wpdb;
	return $wpdb->prefix."latencytracker";
}

function lt_get_record_count() 
{
	global $wpdb;
	$table_name = lt_get_table_name();	
	$record_count_query = $wpdb->get_row("SELECT count(*) AS record_count FROM $table_name");
	return $record_count_query->record_count;
}

// Event and Hook binding \\

// Add new admin panels
function lt_add_admin_panels() {
	// WP-Admin >> Tools >> Latency Tracker
	$page = add_management_page('index.php','Latency Tracker', 'Latency Tracker', 8,  basename(__FILE__), 'lt_manage_panel');
	
	// WP-Admin >> Settings >> LT Settings
	add_options_page('Latency Tracker', 'Latency Tracker', 8,  basename(__FILE__), 'lt_settings_panel');	

	// Load styles and scripts for our page, and our page only
	add_action('admin_print_styles-' . $page, 'lt_admin_styles');
	add_action('admin_print_scripts-' . $page, 'lt_admin_scripts');	
}

function lt_admin_styles() {
	wp_enqueue_style('lt_tabmenu');
	wp_enqueue_style('lt_tablesorter');
}

function lt_admin_scripts() {
	wp_enqueue_script('lt_idtabs');
	wp_enqueue_script('lt_FusionCharts');
	wp_enqueue_script('lt_tablesorter');
	wp_enqueue_script('lt_js');
}

function lt_admin_init() {
	wp_register_script('lt_idtabs', plugins_url( 'js/jquery.idtabs.js', __FILE__ ), array('jquery'));
	wp_register_script('lt_FusionCharts', plugins_url( 'FusionCharts/FusionCharts.js', __FILE__ ));
	wp_register_script('lt_tablesorter', plugins_url( 'js/jquery.tablesorter.min.js', __FILE__ ), array('jquery'));
	wp_register_script('lt_js', plugins_url( 'js/latency.tracker.js', __FILE__ ), array('jquery'));
	wp_register_style('lt_tabmenu', plugins_url( 'css/tabmenu.css', __FILE__ ));
	wp_register_style('lt_tablesorter', plugins_url( 'css/jquery.tablesorter.css', __FILE__ ));
}

// Add admin menu hook
add_action('admin_init', 'lt_admin_init');
add_action('admin_menu', 'lt_add_admin_panels');

// Bind to wp_footer() function to track latency and store results
add_action('wp_footer', 'lt_store_timer_data');

// Bind scheduled event to a function
add_action('lt_clear_max', 'lt_clear_max_run');

// On plugin activation
register_activation_hook(__FILE__,'lt_install');

// On plugin deactivation
register_deactivation_hook(__FILE__, 'lt_uninstall' );
?>
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
include_once('grid_view/items.php');
include_once('graph.php');

if (!class_exists("NetWorthCalculator_Shortcode")) {
	class NetWorthCalculator_Shortcode {
		
		private $month, $user, $view, $width, $height, $last_x_months;
		
		function NetWorthCalculator_Shortcode($month, $user, $view, $width, $height, $last_x_months) {
			# constructor
			$this->month = $month;
			$this->user = $user;
			$this->view = $view;
			$this->width = $width;
			$this->height = $height;
			$this->last_x_months = $last_x_months;
		}
		
		function view_networth($mmyyyy, $username) {
			global $wpdb;

			$date = strtotime('01-'.$mmyyyy);
			$date = getdate($date);
			$date = $date{'year'}.'-'.$date{'mon'}.'-01';
			$result = $wpdb->get_row(
				"SELECT ccf_networth_id, networth
				FROM ".$wpdb->prefix."ccf_networth n
				INNER JOIN ".$wpdb->prefix."users u
				ON u.ID = n.user_id
				WHERE u.user_login = '$username' AND n.month_year = '$date'");
			$date = strtotime($date);
			$date = getdate($date);
			$date = $date{'month'}.' '.$date{'year'};
			return @array($result->{'networth'}, $date);
		}
		
		function view_data() {
			$networth = $this->view_networth($this->month, $this->user);
			
			$out =  '<h2>Net worth for '.$networth[1].': $'.$networth[0].'</h2>';
			$out .= '<br/>';
			$out .= '<div class="clearfix">';
			$out .= '	<div style="float: left; width: 220px; margin-right: 10px">';
			$out .= '		<table id="assets"></table>';
			$out .= '	</div>';
			$out .= '	<div style="float: left; width: 220px">';
			$out .= '		<table id="liabilities"></table>';
			$out .= '	</div>';
			$out .= '	<div class="widget-credit"><span><a href="http://www.creditcardfinder.com.au/net-worth">Widget</a> by <a href="http://www.creditcardfinder.com.au">Credit Card Finder</a></span></div>';
			$out .= '</div>';
			
			$json_url = get_bloginfo('wpurl').'/wp-content/plugins/net-worth-calculator/json.php';
			$out .= grid_view_items($json_url, 'asset', 'assets', $this->month, $this->user);
			$out .= grid_view_items($json_url, 'liability', 'liabilities', $this->month, $this->user);
			
			return $out;
		}
		function view_graph() {
			$out = ccf_graph_display(1);
			$vars = array('show_div'=>true, 'user'=>$this->user, 'num_months'=>$this->last_x_months, 'this_month'=>$this->month);
			$out .= ccf_show_graph_swf('flashcontent', $vars, $this->width, $this->height);
			return $out;
		}
	}
}
?>

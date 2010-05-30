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

header("Content-type: text/plain");
#header("Content-Disposition: attachment; filename=file.csv");
header("Pragma: no-cache");
header("Expires: 0");
include_once('../../../wp-config.php');
include_once('../../../wp-load.php');
include_once('../../../wp-includes/wp-db.php');

ob_end_flush();

if (!class_exists("GenerateCSV")) {
	class GenerateCSV {
		
		private $month;
		private $user;
		private $current_user;
		private $last_x_months;
		
		function GenerateCSV() { # constructor
			global $current_user;
			get_currentuserinfo();
			$this->current_user = $current_user;
			$this->month = mysql_real_escape_string($_GET['month']); // MM-YYYY
			if($this->month == '') {
				$this->month = $this->get_current_month_year();
			}
			$userid = $this->get_userid(mysql_real_escape_string($_GET['user']));
			if($userid != '') {
				$this->user = $userid; // username
			} else {
				$this->user = $this->current_user->ID;
			}
			$this->last_x_months = mysql_real_escape_string($_GET['last']);
			if($this->last_x_months == '') {
				$this->last_x_months = 6;
			} else if($this->last_x_months < 2) {
				$this->last_x_months = 2;
			} else if($this->last_x_months > 12) {
				$this->last_x_months = 12;
			}
			
			$this->view();
		}
		
		function get_year($month_year) {
			$date = getdate(strtotime($month_year));
			return $date{'year'};
		}
		
		function get_month($month_year) {
			$date = getdate(strtotime($month_year));
			return $date{'mon'};
		}
		
		function get_date() {
			$date = getdate(strtotime('01-'.$this->month));
			return $date{'year'}.'-'.$date{'mon'}.'-'.$date{'mday'};
		}
		
		function get_current_month_year() {
			$current_date = getdate();
			return $current_date{'mon'}.'-'.$current_date{'year'};
		}
		
		function get_userid($username) {
			global $wpdb;
			$result = $wpdb->get_row("SELECT ID FROM ".$wpdb->prefix."users WHERE user_login = '$username'");
			return $result->{'ID'};
		}
		
		function view() {
			global $wpdb;
			
			# print csv header
			print '"val","year","month","lVal","lAssets","lLiab"'."\n";
			
			# get rows
			$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ccf_networth WHERE user_id = '".$this->user."' AND month_year <= '".$this->get_date()."' ORDER BY month_year DESC LIMIT ".$this->last_x_months);
			$rows = array_reverse($rows);
			$i = 0;
			foreach($rows as $row) {
				print $row->{'networth'}.',';
				print $this->get_year($row->{'month_year'}).',';
				print $this->get_month($row->{'month_year'}).',';
				
				$total_assets = $wpdb->get_col("SELECT SUM(value) FROM ".$wpdb->prefix."ccf_assets WHERE ccf_networth_id = '".$row->{'ccf_networth_id'}."'");
				$total_liabilities = $wpdb->get_col("SELECT SUM(value) FROM ".$wpdb->prefix."ccf_liabilities WHERE ccf_networth_id = '".$row->{'ccf_networth_id'}."'");
				if(!isset($total_assets[0])) {
					$total_assets[0] = 0;
				}
				if(!isset($total_liabilities[0])) {
					$total_liabilities[0] = 0;
				}
				print "\"$".$row->{'networth'}."\"".',';
				print "\"$$total_assets[0]\"".',';
				print "\"$$total_liabilities[0]\"\n";
				
				$i++;
				if($i > 5) {
					break;
				}
			}
		}
	}
} # End Class GenerateCSV

if (class_exists("GenerateCSV")) {
	$generateCSV = new GenerateCSV();
}
?>

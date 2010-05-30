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
include_once('json/months.php');
include_once('json/items.php');

ob_end_flush();

if (!class_exists("JSONParser")) {
	class JSONParser {
		
		private $months;
		private $assets;
		private $liabilities;
		
		function JSONParser() { # constructor
			global $current_user;
			global $wpdb;
			get_currentuserinfo();
			
			if(isset($_POST['month'])) {
				$opts['month'] = mysql_real_escape_string($_POST['month']);
			} else {
				$opts['month'] = mysql_real_escape_string($_GET['month']);
			}
			$opts['page'] = mysql_real_escape_string($_GET['page']);
			$opts['rows'] = mysql_real_escape_string($_GET['rows']);
			$opts['user'] = $current_user;
			$opts['timestamp'] = date( 'Y-m-d H:i:s', time() );
			
			$opts['mmyyyy'] = mysql_real_escape_string($_GET['mmyyyy']);
			$opts['username'] = mysql_real_escape_string($_GET['username']);
			
			$opts['oper'] = mysql_real_escape_string($_POST['oper']); # add, edit, del
			if(isset($_POST['rowid'])) {
				$opts['id'] = mysql_real_escape_string($_POST['rowid']); # _empty if new
			} else {
				$opts['id'] = mysql_real_escape_string($_POST['id']); # _empty if new
			}
			$opts['asset'] = mysql_real_escape_string($_POST['asset']);
			$opts['liability'] = mysql_real_escape_string($_POST['liability']);
			$opts['amount'] = mysql_real_escape_string($_POST['amount']);
			$opts['private'] = mysql_real_escape_string($_POST['private']);
			$opts['order'] = mysql_real_escape_string($_POST['order']);
			
			if (class_exists("CCF_Months")) {
				$this->months = new CCF_Months($opts);
			}
			
			if (class_exists("CCF_Items")) {
				$this->assets = new CCF_Items($opts, 'asset', $wpdb->prefix.'ccf_assets', 'ccf_assets_id');
				$this->liabilities = new CCF_Items($opts, 'liability', $wpdb->prefix.'ccf_liabilities', 'ccf_liabilities_id');
			}
			
			if(isset($_GET['view'])) {
				if(isset($_GET['months'])) 
					$this->months->view();
				if(isset($_GET['assets'])) {
					if(isset($_GET['order'])) 
						$this->assets->view_order();
					else 
						$this->assets->view();
				}
				if(isset($_GET['liabilities'])) {
					if(isset($_GET['order'])) 
						$this->liabilities->view_order();
					else 
						$this->liabilities->view();
				}
			} elseif(isset($_GET['modify']) && is_user_logged_in()) {
				if(isset($_GET['months'])) 
					$this->months->add_previous_month();
				if(isset($_GET['assets'])) {
					if(isset($_GET['order'])) 
						$this->assets->modify_order();
					else 
						$this->assets->modify();
				}
				if(isset($_GET['liabilities'])) {
					if(isset($_GET['order'])) 
						$this->liabilities->modify_order();
					else 
						$this->liabilities->modify();
				}
			} elseif(isset($_GET['view_post'])) {
				if(isset($_GET['assets'])) 
					$this->assets->view_post($opts['mmyyyy'], $opts['username']);
				if(isset($_GET['liabilities'])) 
					$this->liabilities->view_post($opts['mmyyyy'], $opts['username']);
			} else {
				die('failed');
			}
		}
	}
} # End Class JSONParser

if (class_exists("JSONParser")) {
	$jsonParser = new JSONParser();
}
?>

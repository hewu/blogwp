<?php
include_once('../../../wp-config.php');
include_once('../../../wp-load.php');
include_once('../../../wp-includes/wp-db.php');
include_once('items.php');

if (!class_exists("CCF_Months")) {
	class CCF_Months {
		
		private $month;
		private $page;
		private $rows;
		private $user;
		private $timestamp;
		private $oper;
		private $id;
		private $asset;
		private $liability;
		private $amount;
		private $private;
		
		function CCF_Months($args) { # constructor
			$this->month = $args{'month'};
			$this->page = $args{'page'};
			$this->rows = $args{'rows'};
			$this->user = $args{'user'};
			$this->timestamp = $args{'timestamp'};
			
			$this->oper = $args{'oper'};
			$this->id = $args{'id'};
			$this->asset = $args{'asset'};
			$this->liability = $args{'liability'};
			$this->amount = $args{'amount'};
			$this->private = $args{'private'};
		}
		
		function check_new_month() {
			global $wpdb;
			
			# check if the current month exists, if not, create it!
			$current_date = $this->format_current_date();
			$row = $wpdb->get_row("SELECT ccf_networth_id FROM ".$wpdb->prefix."ccf_networth WHERE user_id = '".$this->user->ID."' AND month_year = '$current_date'");
			
			if(!$row) {
				# get previous month_year
				$row = $wpdb->get_row("SELECT ccf_networth_id, month_year FROM ".$wpdb->prefix."ccf_networth WHERE user_id = '".$this->user->ID."' ORDER BY month_year DESC LIMIT 1");
				
				$monthid = $this->insert_month($current_date);
				
				if($row) {
					$this->add_default_labels($row->{'month_year'}, $monthid);
				}
				
			}
		}
		
		function insert_month($date) {
			global $wpdb;
			
			do {
				$hash = $this->generateHash();
				$row = $wpdb->get_row("SELECT ccf_networth_id FROM ".$wpdb->prefix."ccf_networth WHERE ccf_networth_id = '$hash'");
			} while($row);
			
			$wpdb->insert( $wpdb->prefix.'ccf_networth', 
				array( 	'ccf_networth_id' => $hash, 
						'user_id' => $this->user->ID, 
						'month_year' => $date,
						'networth' => 0,
						'create_ts' => $this->timestamp,
					 ), array( '%s', '%d', '%s', '%d', '%s' ) );
			
			$row = $wpdb->get_row("SELECT ccf_networth_id FROM ".$wpdb->prefix."ccf_networth WHERE ccf_networth_id = '$hash'");
			return $row->{'ccf_networth_id'};
		}
		
		function update_networth($ccf_networth_id) {
			global $wpdb;
			
			# check if the month exists and user owns it
			$row = $wpdb->get_row("SELECT ccf_networth_id FROM ".$wpdb->prefix."ccf_networth WHERE user_id = '".$this->user->ID."' AND ccf_networth_id = '$ccf_networth_id'");
			
			if($row) {
				$total_assets = $wpdb->get_col("SELECT SUM(value) FROM ".$wpdb->prefix."ccf_assets WHERE ccf_networth_id = '$ccf_networth_id'");
				$total_liabilities = $wpdb->get_col("SELECT SUM(value) FROM ".$wpdb->prefix."ccf_liabilities WHERE ccf_networth_id = '$ccf_networth_id'");
				$networth = (float)$total_assets[0] - (float)$total_liabilities[0];
				if(is_numeric($networth)) {
					$wpdb->update( $wpdb->prefix.'ccf_networth', 
						array( 'networth' => $networth ),
						array( 'ccf_networth_id' => $ccf_networth_id ),
						array( '%f' ),
						array( '%s' )
					);
				}
			}
		}
		
		function format_month_year($date = null) {
			if($date) $date = strtotime($date);
			$current_date = getdate($date);
			return $current_date{'month'}.' '.$current_date{'year'};
		}
		
		function format_date($month_year) {
			$date = strtotime('01 '.$month_year);
			$date = getdate($date);
			return $date{'year'}.'-'.$date{'mon'}.'-01';
		}
		
		function format_current_date() {
			$current_date = getdate();
			return $current_date{'year'}.'-'.$current_date{'mon'}.'-01';
		}
		
		function get_previous_month($month_year) {
			$date = getdate(strtotime($month_year));
			if($date{'mon'} > 1) {
				$mon = $date{'mon'} - 1;
				$year = $date{'year'};
			} else {
				$mon = 12;
				$year = $date{'year'} - 1;
			}
			return $this->format_month_year($year.'-'.$mon.'-01');
		}
		
		function add_default_labels($old_month, $new_monthid) {
			global $wpdb;
			
			$opts{'user'} = $this->user;
			$opts{'timestamp'} = $this->timestamp;
			$opts{'month'} = $new_monthid;
			
			$liabilities = new CCF_Items($opts, 'liability', $wpdb->prefix.'ccf_liabilities', 'ccf_liabilities_id');
			$assets = new CCF_Items($opts, 'asset', $wpdb->prefix.'ccf_assets', 'ccf_assets_id');
			
			$asset_names = $wpdb->get_results("
				SELECT l.name
				FROM ".$wpdb->prefix."ccf_assets a
				INNER JOIN ".$wpdb->prefix."ccf_labels l
				ON a.ccf_labels_id = l.ccf_labels_id
				INNER JOIN ".$wpdb->prefix."ccf_networth n
				ON a.ccf_networth_id = n.ccf_networth_id
				WHERE n.user_id = '".$this->user->ID."' AND n.month_year = '".$old_month."'");
			foreach($asset_names as $asset) {
				$assets->create($asset->{'name'});
			}
			$liability_names = $wpdb->get_results("
				SELECT l.name
				FROM ".$wpdb->prefix."ccf_liabilities a
				INNER JOIN ".$wpdb->prefix."ccf_labels l
				ON a.ccf_labels_id = l.ccf_labels_id
				INNER JOIN ".$wpdb->prefix."ccf_networth n
				ON a.ccf_networth_id = n.ccf_networth_id
				WHERE n.user_id = '".$this->user->ID."' AND n.month_year = '".$old_month."'");
			foreach($liability_names as $liability) {
				$liabilities->create($liability->{'name'});
			}
		}
		
		function add_previous_month() {
			global $wpdb;
			
			# confirm month is the previous one
			# check if the month exists and user owns it
			$row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."ccf_networth WHERE user_id = '".$this->user->ID."' ORDER BY month_year ASC");
			$previous_month = $this->get_previous_month($row->{'month_year'});
			
			if($this->month == $previous_month) {
				# add month to database
				$monthid = $this->insert_month($this->format_date($this->month));
				
				$this->add_default_labels($row->{'month_year'}, $monthid);
				
				# return new month id
				die("{success: true, message:'', monthid: '$monthid' }");
			}
			
			die("{success: false, message: 'This is not the previous month.'}");
		}
		
		function view() {
			global $wpdb;
			
			$this->check_new_month();
			
			# get all rows from db arranged by month_year
			
			$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ccf_networth WHERE user_id = '".$this->user->ID."' ORDER BY month_year DESC");
			
			$res->page = 1;
			$res->total = $rows % 10;
			$res->records = count($rows);
			$i = 0;
			foreach($rows as $row) {
				$res->rows[$i]['id'] = $row->{'ccf_networth_id'};
				$res->rows[$i]['cell'] = array(
					$row->{'ccf_networth_id'},
					$this->format_month_year($row->{'month_year'}),
					$row->{'networth'});
				$i++;
			}
			$res->rows[$i]['id'] = '_empty';
			$res->rows[$i]['cell'] = array(
				'_empty',
				$this->get_previous_month($row->{'month_year'}),
				'0');
			die(json_encode($res));
		}
		
		function generateHash() {
			$result = "";
			$charPool = '0123456789abcdefghijklmnopqrstuvwxyz';
			for($p = 0; $p<15; $p++) {
				$result .= $charPool[mt_rand(0,strlen($charPool)-1)];
			}
			return sha1(md5(sha1($result)));
		}
	}
} # End Class CCF_Months
?>

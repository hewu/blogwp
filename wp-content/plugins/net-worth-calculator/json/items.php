<?php
include_once('../../../wp-config.php');
include_once('../../../wp-load.php');
include_once('../../../wp-includes/wp-db.php');
include_once('months.php');

if (!class_exists("CCF_Items")) {
	class CCF_Items {
		
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
		private $type;
		private $order;
		
		function CCF_Items($args, $name, $table, $id) { # constructor
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
			$this->order = $args{'order'};
			
			$this->type->name = $name;
			$this->type->table = $table;
			$this->type->id = $id;
		}
		
		function create_label($name) {
			global $wpdb;
			
			# check if a label_id exists for the item, if not, create it!
			$query = "SELECT ccf_labels_id FROM ".$wpdb->prefix."ccf_labels WHERE user_id = '".$this->user->ID."' AND name = '$name' AND type = '".$this->type->name."'";
			$row = $wpdb->get_row($query);
			
			$order_query = "SELECT order FROM ".$wpdb->prefix."ccf_labels WHERE user_id = '".$this->user->ID."' AND type = '".$this->type->name."' ORDER BY `order` DESC";
			$order_row = $wpdb->get_row($order_query);

			$maxorder = 0;
			if(!$order_row) {
				$maxorder = 0;
			} else {
				$maxorder = $order_row->{'order'} + 1;
			}

			if(!$row) {
				$wpdb->insert( $wpdb->prefix.'ccf_labels',
					array(  'user_id' => $this->user->ID,
							'name' => $name,
							'type' => $this->type->name,
							'order' => $maxorder,
							'create_ts' => $this->timestamp,
						 ), array( '%d', '%s', '%s', '%d', '%s' ) );
			}
			$row = $wpdb->get_row($query);
			return $row->{'ccf_labels_id'};
		}
		
		function create($item = '') {
			global $wpdb;
			
			$name = $this->type->name;
			
			# If calling the create function with an item name, replace the current item name with it
			if($item != '') {
				$this->$name = $item;
			}
			
			if($this->$name == '') {
				die("{success:false, message:'You must enter a ".$this->type->name." first.'}");
			}
			
			# check if month belongs to the user
			$row = $wpdb->get_row("SELECT user_id FROM ".$wpdb->prefix."ccf_networth WHERE ccf_networth_id = '$this->month'");
			
			if(!$row) {
				die("{success:false, message:'Networth_id does not exist.'}");
			} else if($row->{'user_id'} != $this->user->ID) {
				die("{success:false, message:'Currently logged in user is not associated with this networth_id.'}");
			}
			
			if(strtolower($this->$name) == 'other') {
				die("{success:false, message:'This $name name is reserved.'}");
			}
			
			$ccf_labels_id = $this->create_label($this->$name);
			
			# check if the label already exists in the table, if not, add it!
			$query = "SELECT ".$this->type->id." FROM ".$this->type->table." WHERE ccf_networth_id = '$this->month' AND ccf_labels_id = '$ccf_labels_id'";
			$row = $wpdb->get_row($query);
			
			if(!$row) {
				$wpdb->insert( $this->type->table, 
					array(  'ccf_networth_id' => $this->month,
							'ccf_labels_id' => $ccf_labels_id,
							'value' => $this->amount,
							'private' => $this->private,
							'create_ts' => $this->timestamp,
						 ), array( '%s', '%d', '%f', '%d', '%s' ) );
				$row = $wpdb->get_row($query);
				
				# Update networth for current month
				$months = new CCF_Months(array('user' => $this->user));
				$months->update_networth($this->month);
				
				if($item == '') {
					die("{success:true, message: '', id:".$row->{$this->type->id}."}");
				} else {
					return;
				}
			}
			die("{success:false, message:'".ucfirst($this->type->name)." already exists in this month.'}");
		}
		
		function edit() {
			global $wpdb;
			
			# check if month belongs to the user
			$row = $wpdb->get_row("SELECT user_id FROM ".$wpdb->prefix."ccf_networth WHERE ccf_networth_id = '$this->month'");
			
			if(!$row) {
				die("{success:false, message:'Networth_id does not exist.'}");
			} else if($row->{'user_id'} != $this->user->ID) {
				die("{success:false, message:'Currently logged in user is not associated with this networth_id.'}");
			}
			
			# check if the item exists in the table
			$query = "SELECT ".$this->type->id." FROM ".$this->type->table." WHERE ccf_networth_id = '$this->month' AND ".$this->type->id." = '$this->id'";
			$row = $wpdb->get_row($query);
			
			if(!$row) {
				die("{success:false, message: '".ucfirst($this->type->name)." does not exist in this month.'}");
			}
			
			$name = $this->type->name;
			if($this->$name != '') {
				if(strtolower($this->$name) == 'other') {
					die("{success:false, message:'This $name name is reserved.'}");
				}
				$ccf_labels_id = $this->create_label($this->$name);
				
				# check if the label already exists in the table, if not, add it!
				$query = "SELECT ".$this->type->id." FROM ".$this->type->table." WHERE ccf_networth_id = '$this->month' AND ccf_labels_id = '$ccf_labels_id'";
				$row = $wpdb->get_row($query);

				if(!$row) {
					$wpdb->update( $this->type->table, 
						array( 'ccf_labels_id' => $ccf_labels_id ),
						array( $this->type->id => $this->id ),
						array( '%d' ),
						array( '%d' )
					);
					$row = $wpdb->get_row($query);
					die("{success:true, message: '', id:".$row->{$this->type->id}."}");
				}
				die("{success:false, message:'".ucfirst($this->type->name)." already exists in this month.'}");
			}
			
			if($this->amount != '') {
				if(is_numeric($this->amount)) {
					$wpdb->update( $this->type->table, 
						array( 'value' => $this->amount ),
						array( $this->type->id => $this->id ),
						array( '%d' ),
						array( '%d' )
					);
					
					# Update networth for current month
					$months = new CCF_Months(array('user' => $this->user));
					$months->update_networth($this->month);
					
					die("{success:true, message: '', id:".$row->{$this->type->id}."}");
					
				}
				die("{success:false, message: 'Amount was not numeric.'}");
			}
			
			if($this->private != '') {
				if($this->private == 0 || $this->private == 1) {
					$wpdb->update( $this->type->table, 
						array( 'private' => $this->private ),
						array( $this->type->id => $this->id ),
						array( '%d' ),
						array( '%d' )
					);
				}
				die("{success:true, message: '', id:".$row->{$this->type->id}."}");
			}
			
			die("{success:false, message: ''}");
		}
		
		function remove_label($id) {
			global $wpdb;
			
			$wpdb->query("DELETE FROM ".$wpdb->prefix."ccf_labels WHERE ccf_labels_id = '$id'");
		}
		
		function cleanup_unused_labels() {
			global $wpdb;
			
			$rows = $wpdb->get_results("
				SELECT i.".$this->type->id.", l.ccf_labels_id, l.type
				FROM ".$wpdb->prefix."ccf_labels l
				LEFT OUTER JOIN ".$this->type->table." i
				ON l.ccf_labels_id = i.ccf_labels_id
				WHERE l.user_id = '".$this->user->ID."' AND l.type = '".$this->type->name."'
			");
			
			foreach($rows as $row) {
				if($row->{$this->type->id} == null) {
					$this->remove_label($row->{'ccf_labels_id'});
				}
			}
		}
		
		function remove() {
			global $wpdb;
			
			# check if month belongs to the user
			$row = $wpdb->get_row("SELECT user_id FROM ".$wpdb->prefix."ccf_networth WHERE ccf_networth_id = '$this->month'");
			
			if(!$row) {
				die("Error: networth_id does not exist.");
			} else if($row->{'user_id'} != $this->user->ID) {
				die("Error: Currently logged in user is not associated with this networth_id.");
			}
			
			# check if the item exists in the table
			$query = "SELECT ".$this->type->id." FROM ".$this->type->table." WHERE ccf_networth_id = '$this->month' AND ".$this->type->id." = '$this->id'";
			$row = $wpdb->get_row($query);
			
			if(!$row) {
				die("Error: ".$this->type->name." does not exist in this month.");
			}
			
			$wpdb->query("DELETE FROM ".$this->type->table." WHERE ".$this->type->id." = '$this->id'");
			
			$row = $wpdb->get_row($query);
			if(!$row) {
				# Update networth for current month
				$months = new CCF_Months(array('user' => $this->user));
				$months->update_networth($this->month);
				
				$this->cleanup_unused_labels();
				
				die("Success: Removed row.");
			}
			die("Failed: ".$this->type->name." still exists.");
		}
		
		function modify() {
			if($this->id == '_empty') {
				$this->create();
			} else if($this->oper == 'edit') {
				$this->edit();
			} else if($this->oper == 'del') {
				$this->remove();
			}
		}
		
		function modify_order() {
			global $wpdb;
			
			# check if label belongs to the user
			$row = $wpdb->get_row("SELECT user_id FROM ".$wpdb->prefix."ccf_labels WHERE ccf_labels_id = '$this->id'");
			
			if(!$row) {
				die("{success:false, message:'Label does not exist.'}");
			} else if($row->{'user_id'} != $this->user->ID) {
				die("{success:false, message:'Currently logged in user is not associated with this label.'}");
			}
			
			$wpdb->update( $wpdb->prefix."ccf_labels", 
				array( 'order' => $this->order ),
				array( 'ccf_labels_id' => $this->id ),
				array( '%d' ),
				array( '%d' )
			);
					
			die("{success:true, message: '', id:".$this->id." order:".$this->order."}");
		}
		
		function view_order() {
			global $wpdb;
			
			
			$rows = $wpdb->get_results("SELECT ccf_labels_id, name, `order` FROM ".$wpdb->prefix."ccf_labels WHERE user_id = '".$this->user->ID."' AND type = '".$this->type->name."' ORDER BY `order` ASC");
			
			$res->page = 1;
			$res->total = $rows % 10;
			$res->records = count($rows);
			$i = 0;
			foreach($rows as $row) {
				$res->rows[$i]['id'] = $row->{'ccf_labels_id'};
				$res->rows[$i]['cell'] = array(
					$row->{'ccf_labels_id'},
					$row->{'name'},
					$row->{'order'});
				$i++;
			}
			die(json_encode($res));
		}
		
		function view() {
			global $wpdb;
			
			$months = new CCF_Months(array('user' => $this->user, 'timestamp' => $this->timestamp));
			
			# get all rows from db
			
			if($this->month == '') {
				$result = $wpdb->get_row(
					"SELECT ccf_networth_id
					FROM ".$wpdb->prefix."ccf_networth
					WHERE user_id = '".$this->user->ID."' AND month_year = '".$months->format_current_date()."'");
				$this->month = $result->{'ccf_networth_id'};
			}
			
			$result = $wpdb->get_row(
				"SELECT month_year FROM ".$wpdb->prefix."ccf_networth
				WHERE ccf_networth_id = '$this->month'");
			$month = $months->format_month_year($result->month_year);
			
			$rows = $wpdb->get_results(
				"SELECT a.".$this->type->id.", l.name, a.value, a.private
				FROM ".$this->type->table." a
				INNER JOIN ".$wpdb->prefix."ccf_labels l
				ON a.ccf_labels_id = l.ccf_labels_id
				WHERE a.ccf_networth_id = '$this->month' ORDER BY l.order ASC");
			
			$res->page = 1;
			$res->total = $rows % 10;
			$res->records = count($rows);
			$i = 0;
			foreach($rows as $row) {
				$res->rows[$i]['id'] = $row->{$this->type->id};
				$res->rows[$i]['cell'] = array(
					$row->{$this->type->id},
					'',
					$row->{'name'},
					$row->{'value'},
					$row->{'private'});
				$i++;
			}
			# blank row at bottom for new entries
			$res->rows[$i]['id'] = '_empty';
			$res->rows[$i]['cell'] = array('_empty', '', '', '', 0);
			# footer data
			$res->userdata[$this->type->name] = "Total:";
			$res->userdata['amount'] = $wpdb->get_col("SELECT SUM(value) FROM ".$this->type->table." WHERE ccf_networth_id = '$this->month'");
			$res->userdata['month'] = $month;
			$res->userdata['monthid'] = $this->month;
			$date = getdate(strtotime('01 '.$month));
			$res->userdata['mmyyyy'] = $date{'mon'}.'-'.$date{'year'};
			die(json_encode($res));
			
		}
		
		function view_post($mmyyyy, $username) {
			global $wpdb;
			
			$months = new CCF_Months(array('user' => $this->user, 'timestamp' => $this->timestamp));
			
			# get all rows from db
			
			$date = strtotime('01-'.$mmyyyy);
			$date = getdate($date);
			$date = $date{'year'}.'-'.$date{'mon'}.'-01';
			
			$result = $wpdb->get_row(
				"SELECT ccf_networth_id
				FROM ".$wpdb->prefix."ccf_networth n
				INNER JOIN ".$wpdb->prefix."users u
				ON u.ID = n.user_id
				WHERE u.user_login = '$username' AND n.month_year = '$date'");
			$this->month = $result->{'ccf_networth_id'};
			
			$month = $months->format_month_year($date);
			
			$rows = $wpdb->get_results(
				"SELECT a.".$this->type->id.", l.name, a.value, a.private
				FROM ".$this->type->table." a
				INNER JOIN ".$wpdb->prefix."ccf_labels l
				ON a.ccf_labels_id = l.ccf_labels_id
				WHERE a.ccf_networth_id = '$this->month' ORDER BY l.order ASC");
			
			$res->page = 1;
			$res->total = $rows % 10;
			$res->records = count($rows);
			$i = 0;
			$total_private = 0;
			foreach($rows as $row) {
				if($row->{'private'}) {
					$total_private += $row->value;
				} else {
					$res->rows[$i]['id'] = $row->{$this->type->id};
					$res->rows[$i]['cell'] = array(
						$row->{$this->type->id},
						$row->{'name'},
						$row->{'value'}
					);
					$i++;
				}
			}
			# private data
			if($total_private > 0) {
				$res->rows[$i]['id'] = 'private';
				$res->rows[$i]['cell'] = array(
					'private',
					'Private Data',
					$total_private
				);
			}
			
			# footer data
			$res->userdata[$this->type->name] = "Total:";
			$res->userdata['amount'] = $wpdb->get_col("SELECT SUM(value) FROM ".$this->type->table." WHERE ccf_networth_id = '$this->month'");
			$res->userdata['month'] = $month;
			$res->userdata['monthid'] = $this->month;
			die(json_encode($res));
			
		}
	}
} # End Class CCF_Items
?>

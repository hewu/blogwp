<?php
/*
Plugin Name: Cleverness To-Do List
Version: 2.0.4
Description: Manage to-do list items on a individual or group basis. Adds a page under the Tools menu, a dashboard widget, and a sidebar widget.
Author: C.M. Kendrick
Author URI: http://cleverness.org
Plugin URI: http://cleverness.org/plugins/to-do-list/
*/

/*
Based on the ToDo plugin by Abstract Dimensions with a patch by WordPress by Example.
*/

global $wp_version;

$exit_msg = __('To-Do List requires WordPress 2.8 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update.</a>', 'cleverness-to-do-list');

if (version_compare($wp_version, "2.8", "<")) {
	exit($exit_msg);
  	}

include 'cleverness-to-do-list-options.php';
include 'cleverness-to-do-list-dashboard-widget.php';
include 'cleverness-to-do-list-widget.php';
include 'cleverness-to-do-list-shortcode.php';

$cleverness_todo_option = get_option('cleverness_todo_settings');

$action = '';
if ( isset($_GET['action']) ) $action = $_GET['action'];
if ( isset($_POST['action']) ) $action = $_POST['action'];

switch($action) {

case 'setuptodo':
	cleverness_todo_install();
	break;

case 'addtodo':
	if ( $_POST['cleverness_todo_description'] != '' ) {
		$todotext = attribute_escape($_POST['cleverness_todo_description']);
		$priority = attribute_escape($_POST['cleverness_todo_priority']);
		$assign = attribute_escape($_POST['cleverness_todo_assign']);
		$deadline = attribute_escape($_POST['cleverness_todo_deadline']);
		$progress = attribute_escape($_POST['cleverness_todo_progress']);
		$add_nonce = $_REQUEST['_wpnonce'];
		if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['email_assigned'] == '1' && $cleverness_todo_option['assign'] == '0' )
			cleverness_todo_email_user($todotext, $priority, $assign, $deadline);
		$message = cleverness_todo_insert($todotext, $priority, $assign, $deadline, $progress, $add_nonce);
	} else {
		$message = __('To-Do cannot be blank.', 'cleverness-to-do-list');
	}
	break;

case 'updatetodo':
	$id = attribute_escape($_POST['id']);
	$todotext = attribute_escape($_POST['cleverness_todo_description']);
	$priority = attribute_escape($_POST['cleverness_todo_priority']);
	$assign = attribute_escape($_POST['cleverness_todo_assign']);
	$deadline = attribute_escape($_POST['cleverness_todo_deadline']);
	$progress = attribute_escape($_POST['cleverness_todo_progress']);
	$update_nonce = $_REQUEST['_wpnonce'];
	$message = cleverness_todo_update($id, $priority, $todotext, $assign, $deadline, $progress, $update_nonce);
	break;

case 'deletetodo':
	$id = attribute_escape($_GET['id']);
	$message = cleverness_todo_delete($id);
	break;

case 'completetodo':
	$id = attribute_escape($_GET['id']);
	$message = cleverness_todo_complete($id, '1');
	break;

case 'uncompletetodo':
	$id = attribute_escape($_GET['id']);
	$message = cleverness_todo_complete($id, '0');
	break;

case 'purgetodo':
	$message = cleverness_todo_purge();
	break;

} // end switch

/* Insert new to-do item into the database */
function cleverness_todo_insert($todotext, $priority, $assign, $deadline, $progress, $add_nonce) {
	global $wpdb, $userdata, $cleverness_todo_option;
	require_once (ABSPATH . WPINC . '/pluggable.php');
   	get_currentuserinfo();
	if (!wp_verify_nonce($add_nonce, 'todoadd') ) die('Security check');

	if ( $cleverness_todo_option['list_view'] == '0' || current_user_can($cleverness_todo_option['add_capability']) ) {
  	 	$table_name = $wpdb->prefix . 'todolist';
   		$results = $wpdb->insert( $table_name, array( 'author' => $userdata->ID, 'status' => 0, 'priority' => $priority, 'todotext' => $todotext, 'assign' => $assign, 'deadline' => $deadline, 'progress' => $progress ) );
		if ( $results ) $message = __('New To-Do item has been added.', 'cleverness-to-do-list');
		else {
			$message = __('There was a problem adding the item to the database.', 'cleverness-to-do-list');
			}
	} else {
		$message = __('You do not have sufficient privileges to do that.', 'cleverness-to-do-list');
		}
	return $message;
	}


/* Send an email to assigned user */
function cleverness_todo_email_user($todotext, $priority, $assign, $deadline) {
	global $wpdb, $userdata, $cleverness_todo_option;
	$priority_array = array(0 => $cleverness_todo_option['priority_0'] , 1 => $cleverness_todo_option['priority_1'], 2 => $cleverness_todo_option['priority_2']);
	require_once (ABSPATH . WPINC . '/pluggable.php');
   	get_currentuserinfo();

   	if ( current_user_can($cleverness_todo_option['assign_capability']) && $assign != '' && $assign != '-1' ) {
		$headers = 'From: '.html_entity_decode(get_bloginfo('name')).' <'.get_bloginfo('admin_email').'>' . "\r\n\\";
		$subject = html_entity_decode(get_bloginfo('name')).': '. __('A task has been assigned to you', 'cleverness-to-do-list');
		$assign_user = get_userdata($assign);
		$email = $assign_user->user_email;
		$email_message = __('Task:', 'cleverness-to-do-list').' '.$todotext."\r\n";
		$email_message .= __('Priority:', 'cleverness-to-do-list').' '.$priority_array[$priority]."\r\n";
		if ( $deadline != '' )
			$email_message .= __('Deadline:', 'cleverness-to-do-list').' '.$deadline."\r\n";
		$email_message .= "\r\n".__('Website:', 'cleverness-to-do-list').' '.get_bloginfo('url')."\r\n";
		$email_message .= __('Website Login:', 'cleverness-to-do-list').' '.wp_login_url()."\r\n";
  		wp_mail($email, $subject, $email_message, $headers);
	}
}

/* Update to-do list item */
function cleverness_todo_update($id, $priority, $todotext, $assign, $deadline, $progress, $updated_nonce) {
   	global $wpdb, $userdata, $cleverness_todo_option;
	require_once (ABSPATH . WPINC . '/pluggable.php');
   	get_currentuserinfo();
	if (!wp_verify_nonce($updated_nonce, 'todoupdate') ) die('Security check');

   	if ( $cleverness_todo_option['list_view'] == '0' || current_user_can($cleverness_todo_option['edit_capability']) ) {
		$table_name = $wpdb->prefix . 'todolist';
   		$results = $wpdb->update( $table_name, array( 'priority' => $priority, 'todotext' => $todotext, 'assign' => $assign, 'deadline' => $deadline, 'progress' => $progress ), array( 'id' => $id ) );
		if ( $results ) $message = __('To-Do item has been updated.', 'cleverness-to-do-list');
		else {
			$message = __('There was a problem editing the item.', 'cleverness-to-do-list');
			}
	} else {
		$message = __('You do not have sufficient privileges to do that.', 'cleverness-to-do-list');
		}
	return $message;
	}

/* Delete to-do list item */
function cleverness_todo_delete($id) {
   	global $wpdb, $userdata, $cleverness_todo_option;
	require_once (ABSPATH . WPINC . '/pluggable.php');
   	get_currentuserinfo();

   	$table_name = $wpdb->prefix . 'todolist';
   	if ( $cleverness_todo_option['list_view'] == '0' || current_user_can($cleverness_todo_option['delete_capability']) ) {
   		$delete = "DELETE FROM ".$table_name." WHERE id = '".$id."'";
   		$results = $wpdb->query( $delete );
		if ( $results ) $message = __('To-Do item has been deleted.', 'cleverness-to-do-list');
		else {
			$message = __('There was a problem deleting the item.', 'cleverness-to-do-list');
			}
   	} else {
		$message = __('You do not have sufficient privileges to do that.', 'cleverness-to-do-list');
		}
	return $message;
	}

/* Mark to-do list item as completed or uncompleted */
function cleverness_todo_complete($id, $status) {
	global $wpdb, $userdata, $cleverness_todo_option;
	require_once (ABSPATH . WPINC . '/pluggable.php');
   	get_currentuserinfo();

   	 $table_name = $wpdb->prefix . 'todolist';
   	 if ( $cleverness_todo_option['list_view'] == '0' || current_user_can($cleverness_todo_option['complete_capability']) ) {
		$results = $wpdb->update( $table_name, array( 'status' => $status ), array( 'id' => $id ) );
		if ( $status == '1' ) $status_text = __('completed', 'cleverness-to-do-list');
		else $status_text = __('uncompleted', 'cleverness-to-do-list');
		if ( $results ) $message = __('To-Do item has been marked as ', 'cleverness-to-do-list').$status_text.'.';
		else {
			$message = __('There was a problem changing the status of the item.', 'cleverness-to-do-list');
			}
	 } else {
		$message = __('You do not have sufficient privileges to do that.', 'cleverness-to-do-list');
		}
	return $message;
	}

/* Get to-do list item */
function cleverness_todo_get_todo($id) {
   	global $wpdb, $userdata, $cleverness_todo_option;
	require_once (ABSPATH . WPINC . '/pluggable.php');
   	get_currentuserinfo();

   	$table_name = $wpdb->prefix . 'todolist';
   	if ( $cleverness_todo_option['list_view'] == '0' || current_user_can($cleverness_todo_option['edit_capability']) ) {
   		$edit = "SELECT * FROM ".$table_name." WHERE id = '".$id."' LIMIT 1";
   		$result = $wpdb->get_row( $edit );
   		return $result;
	} else {
		$message = __('You do not have sufficient privileges to do that.', 'cleverness-to-do-list');
		}
	return $message;
	}

/* Delete all completed to-do list items */
function cleverness_todo_purge() {
   	global $wpdb, $userdata, $cleverness_todo_option;
	require_once (ABSPATH . WPINC . '/pluggable.php');
   	get_currentuserinfo();

   	$table_name = $wpdb->prefix . 'todolist';
   	if ( $cleverness_todo_option['list_view'] == '0' || current_user_can($cleverness_todo_option['purge_capability']) ) {
   		if ( $cleverness_todo_option['list_view'] == '0' )
   			$purge = "DELETE FROM ".$table_name." WHERE status = '1' AND author = '".$userdata->ID."'";
	   	elseif ( $cleverness_todo_option['list_view'] == '1' )
			$purge = "DELETE FROM ".$table_name." WHERE status = '1'";
   		$results = $wpdb->query( $purge );
		if ( $results ) $message = __('Completed To-Do items have been deleted.', 'cleverness-to-do-list');
		else {
			$message = __('There was a problem removing the completed items.', 'cleverness-to-do-list');
			}
	} else {
		$message = __('You do not have sufficient privileges to do that.', 'cleverness-to-do-list');
		}
	return $message;
	}

/* Create database table and add default options */
function cleverness_todo_install () {
   	global $wpdb, $userdata;
   	get_currentuserinfo();

	$cleverness_todo_db_version = '1.3';

	$table_name = $wpdb->prefix . 'todolist';

   	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
   		$sql = "CREATE TABLE ".$table_name." (
	      id bigint(20) UNIQUE NOT NULL AUTO_INCREMENT,
	      author bigint(20) NOT NULL,
	      status tinyint(1) DEFAULT '0' NOT NULL,
	      priority tinyint(1) NOT NULL,
          todotext text NOT NULL,
		  assign int(10),
		  progress int(3),
		  deadline varchar(30),
		  completed timestamp
	    );";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   		dbDelta($sql);
   		$welcome_text = __('Add your first To-Do List item', 'cleverness-to-do-list');
   		$results = $wpdb->insert( $table_name, array( 'author' => $userdata->ID, 'status' => 0, 'priority' => 1, 'todotext' => $welcome_text ) );

		$new_options = array(
		'list_view' => '0',
		'dashboard_author' => '0',
		'todo_author' => '0',
		'assign' => '1',
		'show_only_assigned' => '1',
		'view_capability' => 'publish_posts',
		'add_capability' => 'publish_posts',
		'edit_capability' => 'publish_posts',
		'delete_capability' => 'manage_options',
		'purge_capability' => 'manage_options',
		'complete_capability' => 'publish_posts',
		'assign_capability' => 'manage_options',
		'view_all_assigned_capability' => 'manage_options',
		'dashboard_number' => '10',
		'priority_0' => __('Important', 'cleverness-to-do-list'),
		'priority_1' => __('Normal', 'cleverness-to-do-list'),
		'priority_2' => __('Low', 'cleverness-to-do-list'),
		'show_deadline' => '0',
		'show_dashboard_deadline' => '0',
		'show_progress' => '0',
		'email_assigned' => '0',
		'show_completed_date' => '0',
		'date_format' => 'm-d-Y',
		'user_roles' => 'contributor, author, editor, administrator'
   		);
   		add_option( 'cleverness_todo_settings', $new_options );
		add_option( 'cleverness_todo_db_version', $cleverness_todo_db_version );
		}

	$installed_ver = get_option( 'cleverness_todo_db_version' );

	if( $installed_ver != $cleverness_todo_db_version ) {

		if ( !function_exists('maybe_create_table') ) {
			require_once(ABSPATH . 'wp-admin/install-helper.php');
		}

		maybe_add_column($table_name, 'assign', "ALTER TABLE `$table_name` ADD `assign` int(10);");
		maybe_add_column($table_name, 'deadline', "ALTER TABLE `$table_name` ADD `deadline` varchar(30);");
		maybe_add_column($table_name, 'progress', "ALTER TABLE `$table_name` ADD `progress` int(3);");
		maybe_add_column($table_name, 'completed', "ALTER TABLE `$table_name` ADD `completed` timestamp;");

    	update_option( 'cleverness_todo_db_version', $cleverness_todo_db_version );
		delete_option( 'atd_db_version' );
		}
	}

/* Create admin page */
function cleverness_todo_todo_subpanel() {
   	global $wpdb, $userdata, $cleverness_todo_option, $message;
   	get_currentuserinfo();

   	$table_name = $wpdb->prefix . 'todolist';
   	$priority = array(0 => $cleverness_todo_option['priority_0'] , 1 => $cleverness_todo_option['priority_1'], 2 => $cleverness_todo_option['priority_2']);
	?>

	<?php if ( isset($message) ) : ?>
		<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
	<?php endif; ?>

	<?php
	/* Display this section if editing an existing to-do item */
	if ($_GET['action'] == 'edittodo') {
    	$id = $_GET['id'];
    	$todo = cleverness_todo_get_todo($id);
	?>

	<div class="wrap">
 		<div id="icon-tools" class="icon32"></div> <h2><?php _e('To-Do List', 'cleverness-to-do-list'); ?></h2>
 		<h3><?php _e('Edit To-Do Item', 'cleverness-to-do-list') ?></h3>
 		<form name="edittodo" action="tools.php?page=cleverness-to-do-list" method="post">
	  		<table class="form-table">
			<tr>
		  		<th scope="row"><label for="cleverness_todo_priority"><?php _e('Priority', 'cleverness-to-do-list') ?></label></th>
		  		<td>
					<select name="cleverness_todo_priority">
					<option value="0" <?php if ($todo->priority == 0) { echo "selected"; } ?>><?php echo $cleverness_todo_option['priority_0']; ?>&nbsp;</option>
					<option value="1" <?php if ($todo->priority == 1) { echo "selected"; } ?>><?php echo $cleverness_todo_option['priority_1']; ?></option>
					<option value="2" <?php if ($todo->priority == 2) { echo "selected"; } ?>><?php echo $cleverness_todo_option['priority_2']; ?></option>
					</select>
					<input type="hidden" name="id" value="<?php echo $todo->id ?>" />
				</td>
			</tr>
			<?php if ($cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' && current_user_can($cleverness_todo_option['assign_capability'])) : ?>
			<tr>
		  		<th scope="row"><label for="cleverness_todo_assign"><?php _e('Assign To', 'cleverness-to-do-list') ?></label></th>
		  		<td>
				<select name='cleverness_todo_assign' id='cleverness_todo_assign' class=''>
					<option value='-1'<?php if ( $todo->assign == '-1' ) echo ' selected="selected"'; ?>><?php _e('None', 'cleverness-to-do-list') ?></option>
					<?php
					if ( $cleverness_todo_option['user_roles'] == '' ) $roles = array('contributor', 'author', 'editor', 'administrator');
					else $roles = explode(", ", $cleverness_todo_option['user_roles']);
					foreach ( $roles as $role ) {
						$role_users = cleverness_todo_get_users($role);
						foreach($role_users as $role_user){
							$user_info = get_userdata($role_user);
							echo '<option value="'.$role_user.'"';
							if ( $todo->assign == $role_user ) echo ' selected="selected"';
							echo '>'.$user_info->display_name.'</option>';
						}
					}
					?>
				</select>
				</td>
			</tr>
			<?php endif; ?>
			<?php if ($cleverness_todo_option['show_deadline'] == '1') : ?>
				<th scope="row"><label for="cleverness_todo_deadline"><?php _e('Deadline', 'cleverness-to-do-list') ?></label></th>
				<td><input type="text" name="cleverness_todo_deadline" id="cleverness_todo_deadline" value="<?php echo wp_specialchars($todo->deadline, 1); ?>" /></td>
			</tr>
			<?php endif; ?>
			<?php if ($cleverness_todo_option['show_progress'] == '1') : ?>
				<th scope="row"><label for="cleverness_todo_progress"><?php _e('Progress', 'cleverness-to-do-list') ?></label></th>
				<td><select name="cleverness_todo_progress">
					<option value="0" <?php if ($todo->progress == 0) { echo "selected"; } ?>>0</option>
					<option value="5" <?php if ($todo->progress == 5) { echo "selected"; } ?>>5</option>
					<option value="10" <?php if ($todo->progress == 10) { echo "selected"; } ?>>10</option>
					<option value="15" <?php if ($todo->progress == 15) { echo "selected"; } ?>>15</option>
					<option value="20" <?php if ($todo->progress == 20) { echo "selected"; } ?>>20</option>
					<option value="25" <?php if ($todo->progress == 25) { echo "selected"; } ?>>25</option>
					<option value="30" <?php if ($todo->progress == 30) { echo "selected"; } ?>>30</option>
					<option value="35" <?php if ($todo->progress == 35) { echo "selected"; } ?>>35</option>
					<option value="40" <?php if ($todo->progress == 40) { echo "selected"; } ?>>40</option>
					<option value="45" <?php if ($todo->progress == 45) { echo "selected"; } ?>>45</option>
					<option value="50" <?php if ($todo->progress == 50) { echo "selected"; } ?>>50</option>
					<option value="55" <?php if ($todo->progress == 55) { echo "selected"; } ?>>55</option>
					<option value="60" <?php if ($todo->progress == 60) { echo "selected"; } ?>>60</option>
					<option value="65" <?php if ($todo->progress == 65) { echo "selected"; } ?>>65</option>
					<option value="70" <?php if ($todo->progress == 70) { echo "selected"; } ?>>70</option>
					<option value="75" <?php if ($todo->progress == 75) { echo "selected"; } ?>>75</option>
					<option value="80" <?php if ($todo->progress == 80) { echo "selected"; } ?>>80</option>
					<option value="85" <?php if ($todo->progress == 85) { echo "selected"; } ?>>85</option>
					<option value="90" <?php if ($todo->progress == 90) { echo "selected"; } ?>>90</option>
					<option value="95" <?php if ($todo->progress == 95) { echo "selected"; } ?>>95</option>
					<option value="100" <?php if ($todo->progress == 100) { echo "selected"; } ?>>100&nbsp;</option>
					</select></td>
			</tr>
			<?php endif; ?>
	   		<tr>
				<th scope="row" valign="top"><label for="cleverness_todo_description"><?php _e('To-Do', 'cleverness-to-do-list') ?></label></th>
				<td><textarea name="cleverness_todo_description" rows="5" cols="50"><?php echo wp_specialchars($todo->todotext, 1); ?></textarea></td>
			</tr>
			</table>
			<?php wp_nonce_field( 'todoupdate' ) ?>
			<input type="hidden" name="action" value="updatetodo" />
	  		<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Edit To-Do Item', 'cleverness-to-do-list') ?> &raquo;" /></p>
 		</form>
 		<p><a href="tools.php?page=cleverness-to-do-list"><?php _e('&laquo; Return to To-Do List', 'cleverness-to-do-list'); ?></a></p>
	</div>

	<?php
	} else {
	/* Display the to-do list items */
	?>

	<div class="wrap">
   		<div id="icon-tools" class="icon32"></div>	<h2><?php _e('To-Do List', 'cleverness-to-do-list'); ?></h2>
		<h3><?php _e('To-Do Items', 'cleverness-to-do-list'); ?>
		<?php if (current_user_can($cleverness_todo_option['add_capability'])) : ?>
			(<a href="#addtd"><?php _e('Add New Item', 'cleverness-to-do-list'); ?></a>)
		<?php endif; ?>
		</h3>
		<table id="todo-list" class="widefat">
		<thead>
		<tr>
	   		<th><?php _e('Item', 'cleverness-to-do-list'); ?></th>
	  		<th><?php _e('Priority', 'cleverness-to-do-list'); ?></th>
			<?php if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' ) : ?><th><?php _e('Assigned To', 'cleverness-to-do-list'); ?></th><?php endif; ?>
			<?php if ( $cleverness_todo_option['show_deadline'] == '1' ) : ?><th><?php _e('Deadline', 'cleverness-to-do-list'); ?></th><?php endif; ?>
			<?php if ( $cleverness_todo_option['show_progress'] == '1' ) : ?><th><?php _e('Progress', 'cleverness-to-do-list'); ?></th><?php endif; ?>
	  		<?php if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' ) : ?><th><?php _e('Added By', 'cleverness-to-do-list'); ?></th><?php endif; ?>
       		<?php if (current_user_can($cleverness_todo_option['edit_capability'])) : ?><th><?php _e('Action', 'cleverness-to-do-list'); ?></th><?php endif; ?>
    	</tr>
		</thead>
		<?php
		if ( $cleverness_todo_option['list_view'] == '0' )
			$sql = "SELECT id, status, todotext, priority, deadline, progress FROM $table_name WHERE status = 0 AND author = $userdata->ID ORDER BY priority";
		elseif ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['show_only_assigned'] == '0' && (current_user_can($cleverness_todo_option['view_all_assigned_capability'])) )
			$sql = "SELECT id, todotext, priority, author, assign, deadline, progress FROM $table_name WHERE status = 0 ORDER BY priority";
		elseif ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['show_only_assigned'] == '0' )
		   	$sql = "SELECT id, todotext, priority, author, assign, deadline, progress FROM $table_name WHERE status = 0 AND assign = $userdata->ID ORDER BY priority";
   		elseif ( $cleverness_todo_option['list_view'] == '1' )
			$sql = "SELECT id, todotext, priority, author, assign, deadline, progress FROM $table_name WHERE status = 0 ORDER BY priority";

   		$results = $wpdb->get_results($sql);
   		if ($results) {
	   		foreach ($results as $result) {
		   		$class = ('alternate' == $class) ? '' : 'alternate';
		   		$prstr = $priority[ $result->priority ];
		   		$priority_class = '';
		   		$user_info = get_userdata($result->author);
		   		if ($result->priority == '0') $priority_class = ' todo-important';
				if ($result->priority == '2') $priority_class = ' todo-low';
				$edit = '';
				if (current_user_can($cleverness_todo_option['edit_capability']))
		  			$edit = '<a href="tools.php?page=cleverness-to-do-list&amp;action=edittodo&amp;id='.$result->id.'" class="edit">'.__('Edit', 'cleverness-to-do-list').'</a>';
				if (current_user_can($cleverness_todo_option['delete_capability']))
					$edit .= ' | <a href="tools.php?page=cleverness-to-do-list&amp;action=deletetodo&amp;id='.$result->id.'" class="delete">'.__('Delete', 'cleverness-to-do-list').'</a>';
		   		echo '<tr id="cleverness_todo-'.$result->id.'" class="'.$class.$priority_class.'">
			   	<td><input type="checkbox" id="td-'.$result->id.'" onclick="window.location = \'tools.php?page=cleverness-to-do-list&amp;action=completetodo&amp;id='.$result->id.'\';" />&nbsp;'.$result->todotext.'</td>
			   	<td>'.$prstr.'</td>';
				if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' ) {
					$assign_user = '';
					if ( $result->assign != '-1' )
						$assign_user = get_userdata($result->assign);
					echo '<td>'.$assign_user->display_name.'</td>';
					}
				if ( $cleverness_todo_option['show_deadline'] == '1' )
					echo '<td>'.$result->deadline.'</td>';
				if ( $cleverness_todo_option['show_progress'] == '1' ) {
					echo '<td>'.$result->progress;
					if ( $result->progress != '' ) echo '%';
					echo '</td>';
					}
		   		if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' )
		   			echo '<td>'.$user_info->display_name.'</td>';
		   		if (current_user_can($cleverness_todo_option['edit_capability']))
					echo '<td>'.$edit.'</td></tr>';
	   		}
   		} else {
	   		echo '<tr><td ';
	   		$colspan = 2;
	   		if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' ) $colspan += 1;
			if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' ) $colspan += 1;
			if ( $cleverness_todo_option['show_deadline'] == '1' ) $colspan += 1;
			if ( $cleverness_todo_option['show_progress'] == '1' ) $colspan += 1;
			if ( current_user_can($cleverness_todo_option['edit_capability']) ) $colspan += 1;
			echo 'colspan="'.$colspan.'"';
	   		echo '>'.__('There is nothing to do...', 'cleverness-to-do-list').'</td></tr>';
   			}
		?>
		</table>
	</div>

	<div class="wrap">
		<h3><?php _e('Completed Items', 'cleverness-to-do-list'); ?>
		<?php if (current_user_can($cleverness_todo_option['purge_capability'])) : ?>
			(<a href="tools.php?page=cleverness-to-do-list&amp;action=purgetodo"><?php _e('Delete All', 'cleverness-to-do-list'); ?></a>)
		<?php endif; ?>
		</h3>
		<table id="todo-list-completed" class="widefat">
		<thead>
		<tr>
	   		<th><?php _e('Item', 'cleverness-to-do-list'); ?></th>
	   		<th><?php _e('Priority', 'cleverness-to-do-list'); ?></th>
			<?php if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' ) : ?><th><?php _e('Assigned To', 'cleverness-to-do-list'); ?></th><?php endif; ?>
			<?php if ( $cleverness_todo_option['show_deadline'] == '1' ) : ?><th><?php _e('Deadline', 'cleverness-to-do-list'); ?></th><?php endif; ?>
			<?php if ( $cleverness_todo_option['show_completed_date'] == '1' ) : ?><th><?php _e('Completed', 'cleverness-to-do-list'); ?></th><?php endif; ?>
	   		<?php if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' ) : ?><th><?php _e('Added By', 'cleverness-to-do-list'); ?></th><?php endif; ?>
       		<?php if (current_user_can($cleverness_todo_option['delete_capability'])) : ?><th><?php _e('Action', 'cleverness-to-do-list'); ?></th><?php endif; ?>
    	</tr>
		</thead>
		<?php
		if ( $cleverness_todo_option['list_view'] == '0' )
			$sql = "SELECT id, status, todotext, priority, deadline, progress, completed FROM $table_name WHERE status = 1 AND author = $userdata->ID";
		elseif ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['show_only_assigned'] == '0' && (current_user_can($cleverness_todo_option['view_all_assigned_capability'])) )
			$sql = "SELECT id, todotext, priority, author, assign, deadline, progress, completed FROM $table_name WHERE status = 1";
		elseif ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['show_only_assigned'] == '0' )
			$sql = "SELECT id, todotext, priority, author, assign, deadline, progress, completed FROM $table_name WHERE status = 1 AND assign = $userdata->ID";
		elseif ( $cleverness_todo_option['list_view'] == '1' )
	   		$sql = "SELECT id, todotext, priority, author, assign, deadline, progress, completed FROM $table_name WHERE status = 1";
		if ( $cleverness_todo_option['show_completed_date'] == '1' )
			$sql .= " ORDER BY completed DESC";
		else
			$sql .= " ORDER BY priority";
   		$results = $wpdb->get_results($sql);
   		if ($results) {
	   		foreach ($results as $result) {
		   		$class = ('alternate' == $class) ? '' : 'alternate';
		   		$prstr = $priority[ $result->priority ];
		   		$user_info = get_userdata($result->author);
				$edit = '';
				if (current_user_can($cleverness_todo_option['delete_capability']))
		   			$edit = '<a href="tools.php?page=cleverness-to-do-list&amp;action=deletetodo&amp;id='.$result->id.'" class="delete">'.__('Delete', 'cleverness-to-do-list').'</a>';
		   		echo '<tr id="cleverness_todo-'.$result->id.'" class="'.$class.'">
			   	<td><input type="checkbox" id="td-'.$result->id.'" checked="checked" onclick="window.location = \'tools.php?page=cleverness-to-do-list&amp;action=uncompletetodo&amp;id='.$result->id.'\';" />&nbsp;'.$result->todotext.'</td>
			   	<td>'.$prstr.'</td>';
				if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' ) {
					$assign_user = '';
					if ( $result->assign != '-1' )
						$assign_user = get_userdata($result->assign);
					echo '<td>'.$assign_user->display_name.'</td>';
					}
				if ( $cleverness_todo_option['show_deadline'] == '1' )
					echo '<td>'.$result->deadline.'</td>';
				if ( $cleverness_todo_option['show_completed_date'] == '1' ) {
					$date = '';
					if ( $result->completed != '0000-00-00 00:00:00' )
						$date = date($cleverness_todo_option['date_format'], strtotime($result->completed));
					echo '<td>'.$date.'</td>';
					}
		   		if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' )
		   			echo '<td>'.$user_info->display_name.'</td>';
		  		if (current_user_can($cleverness_todo_option['delete_capability']))
					 echo '<td>'.$edit.'</td>
			 	</tr>';
	  	 		}
   		} else {
	  		echo '<tr><td ';
			$colspan = 2;
	   		if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' ) $colspan += 1;
			if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' ) $colspan += 1;
			if ( $cleverness_todo_option['show_deadline'] == '1' ) $colspan += 1;
			if ( $cleverness_todo_option['show_completed_date'] == '1' ) $colspan += 1;
			if ( current_user_can($cleverness_todo_option['delete_capability']) ) $colspan += 1;
			echo 'colspan="'.$colspan.'"';
	  	 	echo '>'.__('There are no completed items', 'cleverness-to-do-list').'</td></tr>';
   		}
		?>
   		</table>
	</div>

	<?php if (current_user_can($cleverness_todo_option['add_capability'])) : ?>
	<div class="wrap">
   	 	<h3><?php _e('Add New To-Do Item', 'cleverness-to-do-list') ?></h3>
    	<form name="addtodo" id="addtodo" action="tools.php?page=cleverness-to-do-list" method="post">
	  		<table class="form-table">
			<tr>
		  		<th scope="row"><label for="cleverness_todo_priority"><?php _e('Priority', 'cleverness-to-do-list') ?></label></th>
		  		<td>
        			<select name="cleverness_todo_priority">
       	 				<option value="0"><?php echo $cleverness_todo_option['priority_0']; ?>&nbsp;</option>
        				<option value="1" selected="selected"><?php echo $cleverness_todo_option['priority_1']; ?></option>
       	 		   		<option value="2"><?php echo $cleverness_todo_option['priority_2']; ?></option>
        			</select>
		  		</td>
			</tr>
			<?php if ($cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' && current_user_can($cleverness_todo_option['assign_capability'])) : ?>
			<tr>
		  		<th scope="row"><label for="cleverness_todo_assign"><?php _e('Assign To', 'cleverness-to-do-list') ?></label></th>
		  		<td>
					<select name='cleverness_todo_assign' id='cleverness_todo_assign' class=''>
					<option value='-1'><?php _e('None', 'cleverness-to-do-list') ?></option>
					<?php
					if ( $cleverness_todo_option['user_roles'] == '' ) $roles = array('contributor', 'author', 'editor', 'administrator');
					else $roles = explode(", ", $cleverness_todo_option['user_roles']);
					foreach ( $roles as $role ) {
						$role_users = cleverness_todo_get_users($role);
						foreach($role_users as $role_user){
							$user_info = get_userdata($role_user);
							echo '<option value="'.$role_user.'">'.$user_info->display_name.'</option>';
						}
					}
					?>
					</select>
				</td>
			</tr>
			<?php endif; ?>
			<?php if ($cleverness_todo_option['show_deadline'] == '1') : ?>
			<tr>
				<th scope="row"><label for="cleverness_todo_deadline"><?php _e('Deadline', 'cleverness-to-do-list') ?></label></th>
				<td><input type="text" name="cleverness_todo_deadline" id="cleverness_todo_deadline" value="" /></td>
			</tr>
			<?php endif; ?>
			<?php if ($cleverness_todo_option['show_progress'] == '1') : ?>
			<tr>
				<th scope="row"><label for="cleverness_todo_progress"><?php _e('Progress', 'cleverness-to-do-list') ?></label></th>
				<td><select name="cleverness_todo_progress">
					<option value="0">0</option>
					<option value="5">5</option>
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="30">30</option>
					<option value="35">35</option>
					<option value="40">40</option>
					<option value="45">45</option>
					<option value="50">50</option>
					<option value="55">55</option>
					<option value="60">60</option>
					<option value="65">65</option>
					<option value="70">70</option>
					<option value="75">75</option>
					<option value="80">80</option>
					<option value="85">85</option>
					<option value="90">90</option>
					<option value="95">95</option>
					<option value="100">100&nbsp;</option>
					</select></td>
			</tr>
			<?php endif; ?>
			<tr>
        		<th scope="row" valign="top"><label for="cleverness_todo_description"><?php _e('To-Do', 'cleverness-to-do-list') ?></label></th>
        		<td><textarea name="cleverness_todo_description" rows="5" cols="50" id="the_editor"></textarea></td>
			</tr>
			</table>
	   		<?php wp_nonce_field( 'todoadd' ) ?>
			<input type="hidden" name="action" value="addtodo" />
        	<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Add To-Do Item &raquo;', 'cleverness-to-do-list') ?>" /></p>
		</form>
	</div>
	<?php endif; ?>
	<?php
  	}
}

function cleverness_todo_get_users($role) {
      $wp_user_search = new WP_User_Search('', '', $role);
      return $wp_user_search->get_results();
}

/* Add Page under Tools and Add Settings Page */
function cleverness_todo_admin_menu() {
	if (function_exists('add_submenu_page')) {
		global $userdata, $cleverness_todo_option;
   		get_currentuserinfo();
        add_management_page( __('To-Do List', 'cleverness-to-do-list'), __('To-Do List', 'cleverness-to-do-list'), $cleverness_todo_option['view_capability'], 'cleverness-to-do-list', 'cleverness_todo_todo_subpanel');
		add_options_page( __('To-Do List', 'cleverness-to-do-list'), __('To-Do List', 'cleverness-to-do-list'), 'manage_options', 'cleverness-to-do-list', 'cleverness_todo_settings_page');
        }
	}

/* Add plugin info to admin footer */
function cleverness_todo_admin_footer() {
	$plugin_data = get_plugin_data( __FILE__ );
	printf('%1$s plugin | Version %2$s | by %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']);
	}

/* Add CSS file to admin header */
function cleverness_todo_admin_add_css() {
		$cleverness_style_url = WP_PLUGIN_URL . '/'.plugin_basename(dirname( __FILE__ )).'/admin.css';
        $cleverness_style_file = WP_PLUGIN_DIR . '/'.plugin_basename(dirname( __FILE__ )).'/admin.css';
        if ( file_exists($cleverness_style_file) ) {
            wp_register_style('cleverness_todo_style_sheet', $cleverness_style_url);
            wp_enqueue_style( 'cleverness_todo_style_sheet');
        }
	}

/* Translation Support */
function cleverness_todo_load_translation_file() {
	$plugin_path = plugin_basename( dirname( __FILE__ ) .'/lang' );
	load_plugin_textdomain( 'cleverness-to-do-list', '', $plugin_path );
}

/* Register the options field */
function cleverness_todo_register_settings() {
  register_setting( 'cleverness-todo-settings-group', 'cleverness_todo_settings' );
}

/* Add Action Hooks */
if (function_exists('add_action')) {
	add_action('activate_'.plugin_basename(__FILE__),'cleverness_todo_install');
   	add_action('admin_menu', 'cleverness_todo_admin_menu');
   	add_action('admin_init', 'cleverness_todo_register_settings');
   	add_action('wp_dashboard_setup', 'cleverness_todo_dashboard_setup');
   	add_action('admin_print_styles', 'cleverness_todo_admin_add_css');
  	add_action('widgets_init', 'cleverness_todo_widget');
   	add_action('init', 'cleverness_todo_load_translation_file');
	}
?>
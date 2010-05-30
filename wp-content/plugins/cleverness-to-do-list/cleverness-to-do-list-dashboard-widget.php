<?php
/* Display Dashboard Widget */
function cleverness_todo_todo_in_activity_box() {
   	global $wpdb, $userdata, $cleverness_todo_option;
	get_currentuserinfo();

	$cleverness_widget_action = '';
	if ( isset($_GET['cleverness_widget_action']) ) $cleverness_widget_action = $_GET['cleverness_widget_action'];

	if ( $cleverness_widget_action == 'complete' ) {
		if ( $cleverness_todo_option['list_view'] == '0' || current_user_can($cleverness_todo_option['complete_capability']) ) {
			$cleverness_widget_id = attribute_escape($_GET['cleverness_widget_id']);
			$message = cleverness_todo_complete($cleverness_widget_id, '1');
		} else {
			$message = __('You do not have sufficient privileges to do that.', 'cleverness-to-do-list');
		}
	}

	$table_name = $wpdb->prefix . 'todolist';
	$number = $cleverness_todo_option['dashboard_number'];
	if ( $cleverness_todo_option['list_view'] == '0' )
		$sql = "SELECT id, todotext, priority, deadline, progress FROM $table_name WHERE status = 0 AND author = $userdata->ID ORDER BY priority LIMIT $number";
	elseif ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['show_only_assigned'] == '0' && (current_user_can($cleverness_todo_option['view_all_assigned_capability'])) )
		$sql = "SELECT id, todotext, priority, author, assign, deadline, progress FROM $table_name WHERE status = 0 ORDER BY priority LIMIT $number";
	elseif ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['show_only_assigned'] == '0' )
		$sql = "SELECT id, todotext, priority, author, deadline, progress FROM $table_name WHERE status = 0 AND assign = $userdata->ID ORDER BY priority LIMIT $number";
	elseif ( $cleverness_todo_option['list_view'] == '1' )
		$sql = "SELECT id, todotext, priority, author, assign, deadline, progress FROM $table_name WHERE status = 0 ORDER BY priority LIMIT $number";
	$results = $wpdb->get_results($sql);
	if ($results) {
		foreach ($results as $result) {
			$user_info = get_userdata($result->author);
			$priority_class = '';
		   	if ($result->priority == '0') $priority_class = ' class="todo-important"';
			if ($result->priority == '2') $priority_class = ' class="todo-low"';
			echo '<p><input type="checkbox" id="td-'.$result->id.'" onclick="window.location = \'index.php?cleverness_widget_action=complete&amp;cleverness_widget_id='.$result->id.'\';" /> <span'.$priority_class.'>'.$result->todotext.'</span>';
			if ( ($cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['show_only_assigned'] == '0' && (current_user_can($cleverness_todo_option['view_all_assigned_capability']))) ||  ($cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['show_only_assigned'] == '1') && $cleverness_todo_option['assign'] == '0') {
				$assign_user = '';
				if ( $result->assign != '-1' && $result->assign != '' && $result->assign != '0') {
					$assign_user = get_userdata($result->assign);
					echo ' <small>['.__('assigned to', 'cleverness-to-do-list').' '.$assign_user->display_name.']</small>';
				}
			}
			if ( $cleverness_todo_option['show_dashboard_deadline'] == '1' && $result->deadline != '' )
				echo ' <small>['.__('Deadline:', 'cleverness-to-do-list').' '.$result->deadline.']</small>';
			if ( $cleverness_todo_option['show_progress'] == '1' && $result->progress != '' )
				echo ' <small>['.$result->progress.'%]</small>';
			if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['dashboard_author'] == '0' )
				echo ' <small>- '.__('added by', 'cleverness-to-do-list').' '.$user_info->display_name.'</small>';
			if (current_user_can($cleverness_todo_option['edit_capability']))
		   		echo ' <small>(<a href="tools.php?page=cleverness-to-do-list&amp;action=edittodo&amp;id='. $result->id . '">'. __('Edit', 'cleverness-to-do-list') . '</a>)</small>';
			echo '</p>';
			}
	} else {
		echo '<p>'.__('No items to do.', 'cleverness-to-do-list').'</p>';
		}
		if (current_user_can($cleverness_todo_option['add_capability']))
			echo '<p style="text-align: right">'. '<a href="tools.php?page=cleverness-to-do-list#addtodo">'. __('New To-Do Item &raquo;', 'cleverness-to-do-list').'</a></p>';
	}


/* Add Dashboard Widget */
function cleverness_todo_dashboard_setup() {
	global $userdata, $cleverness_todo_option;
   	get_currentuserinfo();

   	if (current_user_can($cleverness_todo_option['view_capability'])) {
		wp_add_dashboard_widget('cleverness_todo', __( 'To-Do List', 'cleverness-to-do-list' ) . ' <a href="tools.php?page=cleverness-to-do-list">'. __('&raquo;', 'cleverness-to-do-list').'</a>', 'cleverness_todo_todo_in_activity_box' );
		}
	}
?>
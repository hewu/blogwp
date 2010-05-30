<?php
/* Display a list of to-do items using shortcode */
function cleverness_todo_display_items($atts) {
	global $wpdb, $cleverness_todo_option;
	$table_name = $wpdb->prefix . 'todolist';
	$priority = array(0 => $cleverness_todo_option['priority_0'] , 1 => $cleverness_todo_option['priority_1'], 2 => $cleverness_todo_option['priority_2']);
	extract(shortcode_atts(array(
	    'title' => '',
		'type' => 'list',
		'priorities' => 'show',
		'assigned' => 'show',
		'deadline' => 'show',
		'progress' => 'show',
		'addedby' => 'show',
		'completed' => '',
		'completed_title' => '',
		'list_type' => 'ol'
	), $atts));

   ?>

   <?php if ( $type == 'table' ) : ?>
   <table id="todo-list" border="1">
   <?php if ( $title != '' ) echo '<caption>'.$title.'</caption>'; ?>
		<thead>
		<tr>
	   		<th><?php _e('Item', 'cleverness-to-do-list'); ?></th>
	  		<?php if ( $priorities == 'show' ) : ?><th><?php _e('Priority', 'cleverness-to-do-list'); ?></th><?php endif; ?>
			<?php if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' && $assigned == 'show') : ?><th><?php _e('Assigned To', 'cleverness-to-do-list'); ?></th><?php endif; ?>
			<?php if ( $cleverness_todo_option['show_deadline'] == '1' && $deadline == 'show' ) : ?><th><?php _e('Deadline', 'cleverness-to-do-list'); ?></th><?php endif; ?>
			<?php if ( $cleverness_todo_option['show_progress'] == '1' && $progress == 'show' ) : ?><th><?php _e('Progress', 'cleverness-to-do-list'); ?></th><?php endif; ?>
	  		<?php if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' && $addedby == 'show' ) : ?><th><?php _e('Added By', 'cleverness-to-do-list'); ?></th><?php endif; ?>
    	</tr>
		</thead>
		<?php
		$sql = "SELECT * FROM $table_name WHERE status = 0 ORDER BY priority";
   		$results = $wpdb->get_results($sql);
   		if ($results) {
	   		foreach ($results as $result) {
		   		$class = ('alternate' == $class) ? '' : 'alternate';
		   		$prstr = $priority[$result->priority];
		   		$priority_class = '';
		   		$user_info = get_userdata($result->author);
		   		if ($result->priority == '0') $priority_class = ' todo-important';
				if ($result->priority == '2') $priority_class = ' todo-low';
		   		echo '<tr id="cleverness_todo-'.$result->id.'" class="'.$class.$priority_class.'">
			   	<td>'.$result->todotext.'</td>';
			   	if ( $priorities == 'show' )
					echo '<td>'.$prstr.'</td>';
				if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' && $assigned == 'show' ) {
					$assign_user = '';
					if ( $result->assign != '-1' )
						$assign_user = get_userdata($result->assign);
					echo '<td>'.$assign_user->display_name.'</td>';
					}
				if ( $cleverness_todo_option['show_deadline'] == '1' && $deadline == 'show' )
					echo '<td>'.$result->deadline.'</td>';
				if ( $cleverness_todo_option['show_progress'] == '1' && $progress == 'show' ) {
					echo '<td>'.$result->progress;
					if ( $result->progress != '' ) echo '%';
					echo '</td>';
					}
		   		if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' && $addedby == 'show' )
		   			echo '<td>'.$user_info->display_name.'</td>';
	   		}
   		} else {
	   		echo '<tr><td ';
	   		$colspan = 2;
	   		if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' ) $colspan += 1;
			if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' ) $colspan += 1;
			if ( $cleverness_todo_option['show_deadline'] == '1' ) $colspan += 1;
			if ( $cleverness_todo_option['show_progress'] == '1' ) $colspan += 1;
			echo 'colspan="'.$colspan.'"';
	   		echo '>'.__('There are no items listed.', 'cleverness-to-do-list').'</td></tr>';
   			}
		?>
		</table>

		<?php if ( $completed == 'show' ) : ?>
		<table id="todo-list" border="1">
   		<?php if ( $completed_title != '' ) echo '<caption>'.$completed_title.'</caption>'; ?>
		<thead>
		<tr>
	   		<th><?php _e('Item', 'cleverness-to-do-list'); ?></th>
	  		<?php if ( $priorities == 'show' ) : ?><th><?php _e('Priority', 'cleverness-to-do-list'); ?></th><?php endif; ?>
			<?php if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' && $assigned == 'show') : ?><th><?php _e('Assigned To', 'cleverness-to-do-list'); ?></th><?php endif; ?>
			<?php if ( $cleverness_todo_option['show_deadline'] == '1' && $deadline == 'show' ) : ?><th><?php _e('Deadline', 'cleverness-to-do-list'); ?></th><?php endif; ?>
			<?php if ( $cleverness_todo_option['show_completed_date'] == '1' ) : ?><th><?php _e('Completed', 'cleverness-to-do-list'); ?></th><?php endif; ?>
	  		<?php if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' && $addedby == 'show' ) : ?><th><?php _e('Added By', 'cleverness-to-do-list'); ?></th><?php endif; ?>
    	</tr>
		</thead>
		<?php
		$sql = "SELECT * FROM $table_name WHERE status = 1 ORDER BY completed DESC";
   		$results = $wpdb->get_results($sql);
   		if ($results) {
	   		foreach ($results as $result) {
		   		$class = ('alternate' == $class) ? '' : 'alternate';
		   		$prstr = $priority[$result->priority];
		   		$priority_class = '';
		   		$user_info = get_userdata($result->author);
		   		if ($result->priority == '0') $priority_class = ' todo-important';
				if ($result->priority == '2') $priority_class = ' todo-low';
		   		echo '<tr id="cleverness_todo-'.$result->id.'" class="'.$class.$priority_class.'">
			   	<td>'.$result->todotext.'</td>';
			   	if ( $priorities == 'show' )
					echo '<td>'.$prstr.'</td>';
				if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' && $assigned == 'show' ) {
					$assign_user = '';
					if ( $result->assign != '-1' )
						$assign_user = get_userdata($result->assign);
					echo '<td>'.$assign_user->display_name.'</td>';
					}
				if ( $cleverness_todo_option['show_deadline'] == '1' && $deadline == 'show' )
					echo '<td>'.$result->deadline.'</td>';
				if ( $cleverness_todo_option['show_completed_date'] == '1' ) {
					$date = '';
					if ( $result->completed != '0000-00-00 00:00:00' )
						$date = date($cleverness_todo_option['date_format'], strtotime($result->completed));
					}
					echo '<td>'.$date.'</td>';
		   		if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' && $addedby == 'show' )
		   			echo '<td>'.$user_info->display_name.'</td>';
	   		}
   		} else {
	   		echo '<tr><td ';
	   		$colspan = 2;
	   		if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' ) $colspan += 1;
			if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' ) $colspan += 1;
			if ( $cleverness_todo_option['show_deadline'] == '1' ) $colspan += 1;
			if ( $cleverness_todo_option['show_completed'] == '1' ) $colspan += 1;
			echo 'colspan="'.$colspan.'"';
	   		echo '>'.__('There are no items listed.', 'cleverness-to-do-list').'</td></tr>';
   			}
		?>
		</table>
		<?php endif; ?>


		<?php elseif ( $type == 'list' ) : ?>
		  	<?php
		   	if ( $title != '' ) echo '<h3>'.$title.'</h3>';
			echo '<'.$list_type.'>';
		   	$sql = "SELECT * FROM $table_name WHERE status = 0 ORDER BY priority";
   	   		$results = $wpdb->get_results($sql);
   	   		if ($results) {
	   			foreach ($results as $result) {
	   				$user_info = get_userdata($result->author);
			   		echo '<li>';
					echo $result->todotext;
					if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' && $assigned == 'show' ) {
						$assign_user = '';
				   		if ( $result->assign != '-1' && $result->assign != '' ) {
							$assign_user = get_userdata($result->assign);
				   			echo ' - '.$assign_user->display_name;
							}
						}
					if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' && $addedby == 'show' )
		   		   		echo ' - '.$user_info->display_name;
					if ( $cleverness_todo_option['show_progress'] == '1' && $progress == 'show' ) {
				   		echo ' - '.$result->progress;
						if ( $result->progress != '' ) echo '%';
						}
					if ( $cleverness_todo_option['show_deadline'] == '1' && $deadline == 'show' )
						echo ' - '.$result->deadline.'';
					echo '</li>';
	   			}
   			} else {
	   	   		echo '<li>'.__('There are no items listed.', 'cleverness-to-do-list').'</li>';
   			}
		echo '</'.$list_type.'>';

		if ( $completed == 'show' ) {
		   	if ( $completed_title != '' ) echo '<h3>'.$completed_title.'</h3>';
			echo '<'.$list_type.'>';
		   	$sql = "SELECT * FROM $table_name WHERE status = 1 ORDER BY completed DESC";
   	   		$results = $wpdb->get_results($sql);
   	   		if ($results) {
	   			foreach ($results as $result) {
					$user_info = get_userdata($result->author);
			   		echo '<li>';
					if ( $cleverness_todo_option['show_completed_date'] == '1' ) {
						$date = '';
						if ( $result->completed != '0000-00-00 00:00:00' ) {
							$date = date($cleverness_todo_option['date_format'], strtotime($result->completed));
							echo $date.' - ';
							}
					}
					echo $result->todotext;
					if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['assign'] == '0' && $assigned == 'show' ) {
						$assign_user = '';
				   		if ( $result->assign != '-1' && $result->assign != '' ) {
							$assign_user = get_userdata($result->assign);
				   			echo ' - '.$assign_user->display_name;
							}
						}
					if ( $cleverness_todo_option['list_view'] == '1' && $cleverness_todo_option['todo_author'] == '0' && $addedby == 'show' )
		   		   		echo ' - '.$user_info->display_name;
					echo '</li>';
	   			}
   			} else {
	   	   		echo '<li>'.__('There are no items listed.', 'cleverness-to-do-list').'</li>';
   			}
		echo '</'.$list_type.'>';
		}
		endif;
}

add_shortcode('todolist', 'cleverness_todo_display_items');
?>
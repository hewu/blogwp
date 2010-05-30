<?php
/* Creates a page under settings to manage the To-Do List settings */
function cleverness_todo_settings_page() {
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"></div> <h2><?php _e('To-Do List Settings', 'cleverness-to-do-list'); ?></h2>

<form method="post" action="options.php">
    <?php settings_fields( 'cleverness-todo-settings-group' ); ?>
	<?php $options = get_option('cleverness_todo_settings'); ?>

	<p><?php _e('<em>List View</em> sets how the to-do lists are viewed. The <em>Individual</em> setting allows each user to have their own private to-do list. The <em>Group</em> setting allows all users to share one to-do list.', 'cleverness-to-do-list'); ?></p>

    <table class="form-table">
	<tbody>
        <tr>
        <th scope="row"><label for="cleverness_todo_settings[list_view]"><?php _e('List View', 'cleverness-to-do-list'); ?></label></th>
        <td>
			<select id="cleverness_todo_settings[list_view]" name="cleverness_todo_settings[list_view]">
				<option value="0"<?php if ( $options['list_view'] == '0' ) echo ' selected="selected"'; ?>><?php _e('Individual', 'cleverness-to-do-list'); ?>&nbsp;</option>
				<option value="1"<?php if ( $options['list_view'] == '1' ) echo ' selected="selected"'; ?>><?php _e('Group', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
        </tr>
        <tr>
        <th scope="row"><label for="cleverness_todo_settings[show_deadline]"><?php _e('Show Deadline', 'cleverness-to-do-list'); ?></label></th>
        <td>
			<select id="cleverness_todo_settings[show_deadline]" name="cleverness_todo_settings[show_deadline]">
				<option value="0"<?php if ( $options['show_deadline'] == '0' ) echo ' selected="selected"'; ?>><?php _e('No', 'cleverness-to-do-list'); ?></option>
				<option value="1"<?php if ( $options['show_deadline'] == '1' ) echo ' selected="selected"'; ?>><?php _e('Yes', 'cleverness-to-do-list'); ?>&nbsp;</option>
			</select>
		</td>
        </tr>
        <tr>
        <th scope="row"><label for="cleverness_todo_settings[show_progress]"><?php _e('Show Progress', 'cleverness-to-do-list'); ?></label></th>
        <td>
			<select id="cleverness_todo_settings[show_progress]" name="cleverness_todo_settings[show_progress]">
				<option value="0"<?php if ( $options['show_progress'] == '0' ) echo ' selected="selected"'; ?>><?php _e('No', 'cleverness-to-do-list'); ?></option>
				<option value="1"<?php if ( $options['show_progress'] == '1' ) echo ' selected="selected"'; ?>><?php _e('Yes', 'cleverness-to-do-list'); ?>&nbsp;</option>
			</select>
		</td>
        </tr>
		<tr>
        <th scope="row"><label for="cleverness_todo_settings[show_completed_date]"><?php _e('Show Date Completed', 'cleverness-to-do-list'); ?></label></th>
        <td>
			<select id="cleverness_todo_settings[show_completed_date]" name="cleverness_todo_settings[show_completed_date]">
				<option value="0"<?php if ( $options['show_completed_date'] == '0' ) echo ' selected="selected"'; ?>><?php _e('No', 'cleverness-to-do-list'); ?></option>
				<option value="1"<?php if ( $options['show_completed_date'] == '1' ) echo ' selected="selected"'; ?>><?php _e('Yes', 'cleverness-to-do-list'); ?>&nbsp;</option>
			</select>
		</td>
        </tr>
		<tr>
        <th scope="row"><label for="cleverness_todo_settings[date_format]"><?php _e('Date Format', 'cleverness-to-do-list'); ?></label></th>
        <td>
			<input type="text" id="cleverness_todo_settings[date_format]" name="cleverness_todo_settings[date_format]" value="<?php if ( $options['date_format'] != '' ) echo $options['date_format']; else echo 'm-d-Y'; ?>" /><br /><a href="http://codex.wordpress.org/Formatting_Date_and_Time"><?php _e('Documentation on Date Formatting', 'cleverness-to-do-list'); ?></a>
		</td>
        </tr>
	</tbody>
	</table>

	<h3><?php _e('Dashboard Widget Settings', 'cleverness-to-do-list'); ?></h3>
	<table class="form-table">
	<tbody>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[dashboard_number]"><?php _e('Number of List Items to Show', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[dashboard_number]" name="cleverness_todo_settings[dashboard_number]">
				<option value="1"<?php if ( $options['dashboard_number'] == '1' ) echo ' selected="selected"'; ?>><?php _e('1', 'cleverness-to-do-list'); ?></option>
				<option value="5"<?php if ( $options['dashboard_number'] == '5' ) echo ' selected="selected"'; ?>><?php _e('5', 'cleverness-to-do-list'); ?></option>
				<option value="10"<?php if ( $options['dashboard_number'] == '10' ) echo ' selected="selected"'; ?>><?php _e('10', 'cleverness-to-do-list'); ?></option>
				<option value="15"<?php if ( $options['dashboard_number'] == '15' ) echo ' selected="selected"'; ?>><?php _e('15', 'cleverness-to-do-list'); ?></option>
				<option value="20"<?php if ( $options['dashboard_number'] == '20' ) echo ' selected="selected"'; ?>><?php _e('20', 'cleverness-to-do-list'); ?>&nbsp;</option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[show_dashboard_deadline]"><?php _e('Show Deadline', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[show_dashboard_deadline]" name="cleverness_todo_settings[show_dashboard_deadline]">
				<option value="0"<?php if ( $options['show_dashboard_deadline'] == '0' ) echo ' selected="selected"'; ?>><?php _e('No', 'cleverness-to-do-list'); ?></option>
				<option value="1"<?php if ( $options['show_dashboard_deadline'] == '1' ) echo ' selected="selected"'; ?>><?php _e('Yes', 'cleverness-to-do-list'); ?>&nbsp;</option>
			</select>
		</td>
		</tr>
	</tbody>
	</table>

	<h3><?php _e('Priority Label Settings', 'cleverness-to-do-list'); ?></h3>
	<p><?php _e('The highest priority list items are shown in red in the lists. The lowest priority list items are shown in a lighter grey.', 'cleverness-to-do-list'); ?></p>
	<table class="form-table">
	<tbody>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[priority_0]"><?php _e('Highest Priority Label', 'cleverness-to-do-list'); ?></label></th>
		<td>
			<input type="text" id="cleverness_todo_settings[priority_0]" name="cleverness_todo_settings[priority_0]" value="<?php echo $options['priority_0']; ?>" />
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[priority_1]"><?php _e('Middle Priority Label', 'cleverness-to-do-list'); ?></label></th>
		<td>
			<input type="text" id="cleverness_todo_settings[priority_1]" name="cleverness_todo_settings[priority_1]" value="<?php echo $options['priority_1']; ?>" />
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[priority_2]"><?php _e('Lowest Priority Label', 'cleverness-to-do-list'); ?></label></th>
		<td>
			<input type="text" id="cleverness_todo_settings[priority_2]" name="cleverness_todo_settings[priority_2]" value="<?php echo $options['priority_2']; ?>" />
		</td>
		</tr>
	</tbody>
    </table>

	<h3><?php _e('Group View Settings', 'cleverness-to-do-list'); ?></h3>
	<p><?php _e('These settings are only used when <em>List View</em> is set to <em>Group</em>.', 'cleverness-to-do-list'); ?></p>
	<table class="form-table">
	<tbody>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[dashboard_author]"><?php _e('Show <em>Added By</em> on Dashboard Widget', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[dashboard_author]" name="cleverness_todo_settings[dashboard_author]">
				<option value="0"<?php if ( $options['dashboard_author'] == '0' ) echo ' selected="selected"'; ?>><?php _e('Yes', 'cleverness-to-do-list'); ?>&nbsp;</option>
				<option value="1"<?php if ( $options['dashboard_author'] == '1' ) echo ' selected="selected"'; ?>><?php _e('No', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[todo_author]"><?php _e('Show <em>Added By</em> on To-Do List page', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[todo_author]" name="cleverness_todo_settings[todo_author]">
				<option value="0"<?php if ( $options['todo_author'] == '0' ) echo ' selected="selected"'; ?>><?php _e('Yes', 'cleverness-to-do-list'); ?>&nbsp;</option>
				<option value="1"<?php if ( $options['todo_author'] == '1' ) echo ' selected="selected"'; ?>><?php _e('No', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[assign]"><?php _e('Assign Tasks to Users', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[assign]" name="cleverness_todo_settings[assign]">
				<option value="0"<?php if ( $options['assign'] == '0' ) echo ' selected="selected"'; ?>><?php _e('Yes', 'cleverness-to-do-list'); ?>&nbsp;</option>
				<option value="1"<?php if ( $options['assign'] == '1' ) echo ' selected="selected"'; ?>><?php _e('No', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[email_assigned]"><?php _e('Email Assigned Task to User', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[assign]" name="cleverness_todo_settings[email_assigned]">
				<option value="0"<?php if ( $options['email_assigned'] == '0' ) echo ' selected="selected"'; ?>><?php _e('No', 'cleverness-to-do-list'); ?>&nbsp;</option>
				<option value="1"<?php if ( $options['email_assigned'] == '1' ) echo ' selected="selected"'; ?>><?php _e('Yes', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[show_only_assigned]"><?php _e('Show Each User Only Their Assigned Tasks', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[show_only_assigned]" name="cleverness_todo_settings[show_only_assigned]">
				<option value="0"<?php if ( $options['show_only_assigned'] == '0' ) echo ' selected="selected"'; ?>><?php _e('Yes', 'cleverness-to-do-list'); ?>&nbsp;</option>
				<option value="1"<?php if ( $options['show_only_assigned'] == '1' ) echo ' selected="selected"'; ?>><?php _e('No', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
        <th scope="row"><label for="cleverness_todo_settings[user_roles]"><?php _e('User Roles', 'cleverness-to-do-list'); ?></label></th>
        <td>
			<?php _e('This is used in displaying the list of users to-do items can be assigned to.', 'cleverness-to-do-list'); ?><br />
			<?php _e('Separate each role with a comma.', 'cleverness-to-do-list'); ?><br />
			<input type="text" id="cleverness_todo_settings[user_roles]" name="cleverness_todo_settings[user_roles]" value="<?php if ( $options['user_roles'] != '' ) echo $options['user_roles']; else echo 'contributor, author, editor, administrator'; ?>" style="width: 300px;" /><br /><a href="http://codex.wordpress.org/Roles_and_Capabilities"><?php _e('Documentation on User Roles', 'cleverness-to-do-list'); ?></a>
		</td>
        </tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[view_capability]"><?php _e('View To-Do Item Capability', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[view_capability]" name="cleverness_todo_settings[view_capability]">
				<option value="edit_posts"<?php if ( $options['view_capability'] == 'edit_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_posts"<?php if ( $options['view_capability'] == 'publish_posts' ) echo ' selected="selected"'; ?>><?php _e('Publish Posts', 'cleverness-to-do-list'); ?></option>
				<option value="edit_others_posts"<?php if ( $options['view_capability'] == 'edit_others_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Others Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_pages"<?php if ( $options['view_capability'] == 'publish_pages' ) echo ' selected="selected"'; ?>><?php _e('Publish Pages', 'cleverness-to-do-list'); ?></option>
				<option value="edit_users"<?php if ( $options['view_capability'] == 'edit_users' ) echo ' selected="selected"'; ?>><?php _e('Edit Users', 'cleverness-to-do-list'); ?></option>
				<option value="manage_options"<?php if ( $options['view_capability'] == 'manage_options' ) echo ' selected="selected"'; ?>><?php _e('Manage Options', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[complete_capability]"><?php _e('Complete To-Do Item Capability', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[complete_capability]" name="cleverness_todo_settings[complete_capability]">
				<option value="edit_posts"<?php if ( $options['complete_capability'] == 'edit_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_posts"<?php if ( $options['complete_capability'] == 'publish_posts' ) echo ' selected="selected"'; ?>><?php _e('Publish Posts', 'cleverness-to-do-list'); ?></option>
				<option value="edit_others_posts"<?php if ( $options['complete_capability'] == 'edit_others_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Others Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_pages"<?php if ( $options['complete_capability'] == 'publish_pages' ) echo ' selected="selected"'; ?>><?php _e('Publish Pages', 'cleverness-to-do-list'); ?></option>
				<option value="edit_users"<?php if ( $options['complete_capability'] == 'edit_users' ) echo ' selected="selected"'; ?>><?php _e('Edit Users', 'cleverness-to-do-list'); ?></option>
				<option value="manage_options"<?php if ( $options['complete_capability'] == 'manage_options' ) echo ' selected="selected"'; ?>><?php _e('Manage Options', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[add_capability]"><?php _e('Add To-Do Item Capability', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[add_capability]" name="cleverness_todo_settings[add_capability]">
				<option value="edit_posts"<?php if ( $options['add_capability'] == 'edit_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_posts"<?php if ( $options['add_capability'] == 'publish_posts' ) echo ' selected="selected"'; ?>><?php _e('Publish Posts', 'cleverness-to-do-list'); ?></option>
				<option value="edit_others_posts"<?php if ( $options['add_capability'] == 'edit_others_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Others Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_pages"<?php if ( $options['add_capability'] == 'publish_pages' ) echo ' selected="selected"'; ?>><?php _e('Publish Pages', 'cleverness-to-do-list'); ?></option>
				<option value="edit_users"<?php if ( $options['add_capability'] == 'edit_users' ) echo ' selected="selected"'; ?>><?php _e('Edit Users', 'cleverness-to-do-list'); ?></option>
				<option value="manage_options"<?php if ( $options['add_capability'] == 'manage_options' ) echo ' selected="selected"'; ?>><?php _e('Manage Options', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[edit_capability]"><?php _e('Edit To-Do Item Capability', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[edit_capability]" name="cleverness_todo_settings[edit_capability]">
				<option value="edit_posts"<?php if ( $options['edit_capability'] == 'edit_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_posts"<?php if ( $options['edit_capability'] == 'publish_posts' ) echo ' selected="selected"'; ?>><?php _e('Publish Posts', 'cleverness-to-do-list'); ?></option>
				<option value="edit_others_posts"<?php if ( $options['edit_capability'] == 'edit_others_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Others Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_pages"<?php if ( $options['edit_capability'] == 'publish_pages' ) echo ' selected="selected"'; ?>><?php _e('Publish Pages', 'cleverness-to-do-list'); ?></option>
				<option value="edit_users"<?php if ( $options['edit_capability'] == 'edit_users' ) echo ' selected="selected"'; ?>><?php _e('Edit Users', 'cleverness-to-do-list'); ?></option>
				<option value="manage_options"<?php if ( $options['edit_capability'] == 'manage_options' ) echo ' selected="selected"'; ?>><?php _e('Manage Options', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[assign_capability]"><?php _e('Assign To-Do Item Capability', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[assign_capability]" name="cleverness_todo_settings[assign_capability]">
				<option value="edit_posts"<?php if ( $options['assign_capability'] == 'edit_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_posts"<?php if ( $options['assign_capability'] == 'publish_posts' ) echo ' selected="selected"'; ?>><?php _e('Publish Posts', 'cleverness-to-do-list'); ?></option>
				<option value="edit_others_posts"<?php if ( $options['assign_capability'] == 'edit_others_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Others Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_pages"<?php if ( $options['assign_capability'] == 'publish_pages' ) echo ' selected="selected"'; ?>><?php _e('Publish Pages', 'cleverness-to-do-list'); ?></option>
				<option value="edit_users"<?php if ( $options['assign_capability'] == 'edit_users' ) echo ' selected="selected"'; ?>><?php _e('Edit Users', 'cleverness-to-do-list'); ?></option>
				<option value="manage_options"<?php if ( $options['assign_capability'] == 'manage_options' ) echo ' selected="selected"'; ?>><?php _e('Manage Options', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[view_all_assigned_capability]"><?php _e('View All Assigned Tasks Capability', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[view_all_assigned_capability]" name="cleverness_todo_settings[view_all_assigned_capability]">
				<option value="none"<?php if ( $options['view_all_assigned_capability'] == 'none' ) echo ' selected="selected"'; ?>><?php _e('None', 'cleverness-to-do-list'); ?></option>
				<option value="edit_posts"<?php if ( $options['view_all_assigned_capability'] == 'edit_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_posts"<?php if ( $options['view_all_assigned_capability'] == 'publish_posts' ) echo ' selected="selected"'; ?>><?php _e('Publish Posts', 'cleverness-to-do-list'); ?></option>
				<option value="edit_others_posts"<?php if ( $options['view_all_assigned_capability'] == 'edit_others_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Others Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_pages"<?php if ( $options['view_all_assigned_capability'] == 'publish_pages' ) echo ' selected="selected"'; ?>><?php _e('Publish Pages', 'cleverness-to-do-list'); ?></option>
				<option value="edit_users"<?php if ( $options['view_all_assigned_capability'] == 'edit_users' ) echo ' selected="selected"'; ?>><?php _e('Edit Users', 'cleverness-to-do-list'); ?></option>
				<option value="manage_options"<?php if ( $options['view_all_assigned_capability'] == 'manage_options' ) echo ' selected="selected"'; ?>><?php _e('Manage Options', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[delete_capability]"><?php _e('Delete To-Do Item Capability', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[delete_capability]" name="cleverness_todo_settings[delete_capability]">
				<option value="edit_posts"<?php if ( $options['delete_capability'] == 'edit_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_posts"<?php if ( $options['delete_capability'] == 'publish_posts' ) echo ' selected="selected"'; ?>><?php _e('Publish Posts', 'cleverness-to-do-list'); ?></option>
				<option value="edit_others_posts"<?php if ( $options['delete_capability'] == 'edit_others_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Others Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_pages"<?php if ( $options['delete_capability'] == 'publish_pages' ) echo ' selected="selected"'; ?>><?php _e('Publish Pages', 'cleverness-to-do-list'); ?></option>
				<option value="edit_users"<?php if ( $options['delete_capability'] == 'edit_users' ) echo ' selected="selected"'; ?>><?php _e('Edit Users', 'cleverness-to-do-list'); ?></option>
				<option value="manage_options"<?php if ( $options['delete_capability'] == 'manage_options' ) echo ' selected="selected"'; ?>><?php _e('Manage Options', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		<tr>
		<th scope="row"><label for="cleverness_todo_settings[purge_capability]"><?php _e('Purge To-Do Items Capability', 'cleverness-to-do-list'); ?></label></th>
        <td valign="top">
			<select id="cleverness_todo_settings[purge_capability]" name="cleverness_todo_settings[purge_capability]">
				<option value="edit_posts"<?php if ( $options['purge_capability'] == 'edit_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_posts"<?php if ( $options['purge_capability'] == 'publish_posts' ) echo ' selected="selected"'; ?>><?php _e('Publish Posts', 'cleverness-to-do-list'); ?></option>
				<option value="edit_others_posts"<?php if ( $options['purge_capability'] == 'edit_others_posts' ) echo ' selected="selected"'; ?>><?php _e('Edit Others Posts', 'cleverness-to-do-list'); ?></option>
				<option value="publish_pages"<?php if ( $options['purge_capability'] == 'publish_pages' ) echo ' selected="selected"'; ?>><?php _e('Publish Pages', 'cleverness-to-do-list'); ?></option>
				<option value="edit_users"<?php if ( $options['purge_capability'] == 'edit_users' ) echo ' selected="selected"'; ?>><?php _e('Edit Users', 'cleverness-to-do-list'); ?></option>
				<option value="manage_options"<?php if ( $options['purge_capability'] == 'manage_options' ) echo ' selected="selected"'; ?>><?php _e('Manage Options', 'cleverness-to-do-list'); ?></option>
			</select>
		</td>
		</tr>
		</tbody>
		</table>

    <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cleverness-to-do-list') ?>" /></p>

</form>
</div>
<?php
/* Adds information about the plugin on the settings page footer */
add_action( 'in_admin_footer', 'cleverness_todo_admin_footer' );
}
?>
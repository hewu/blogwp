<?php
/*
Plugin Name: Tabagile Scrum Board
Plugin URI:
Description: A simple scrum board that helps you in deal with your stories, sprints and your team members. All members work as a team to manipulate the list under "Manage". A variant derived from AbstractDimension's Todo List Plugin.
Version: 0.1 (alpha)
Author: Maeka
Author URI: http://www.twitter.com/ricardonm

Copyright 2009  Maeka  <http://www.twitter.com/ricardonm>
Copyright 2007  Wordpress By Examples  <http://wordpress.byexamples.com>
Copyright 2006  Abstract Dimension  <http://abstractdimension.com>

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
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


global $table_prefix;
$otd_tablename       = $table_prefix . 'tabagile';
$otd_tablename_users = $table_prefix . 'users';
$otd_message         = '';
$otd_location        = get_settings('siteurl') . '/wp-admin/tools.php?page=' . otd_plugin_basename(__FILE__);
$otd_option_location = get_settings('siteurl') . '/wp-admin/options-general.php?page=' . otd_plugin_basename(__FILE__);

load_plugin_textdomain('otd','wp-content/plugins/' . dirname(otd_plugin_basename(__FILE__)) );

function otd_runinclude ()
{
    $path = ABSPATH . WPINC;
    $incfile = $path . '/pluggable-functions.php';
    $incfile_ella = $path . '/pluggable.php';

    if ( is_readable($incfile) ) {
        require_once($incfile);
    }
    else if ( is_readable($incfile_ella) ) {
        require_once($incfile_ella);
    }
    else {
        echo "Could not read pluggable.php or pluggable-functions.php under $path/.";
        exit;
    }
}


if ( function_exists('add_action') )
{
    otd_runinclude();
    get_currentuserinfo();  // For WP v2.0.x, current_user_can() need it

    add_action('activate_'.otd_plugin_basename(__FILE__), 'otd_activate');

    if ( function_exists('current_user_can') )  // WP >= v2.0
    {
        add_action('admin_menu', 'otd_load_option_panel');

        if ( current_user_can('use_ourtodolist') )
        {
            add_action('admin_menu', 'otd_load_manage_panel');
            add_action('activity_box_end', 'otd_todo_in_activity_box');
        }
    }
    else  // WP < v2.0  - no role-capability feature
    {
        add_action('admin_menu', 'otd_load_manage_panel');
        add_action('activity_box_end', 'otd_todo_in_activity_box');
    }
}

function otd_load_manage_panel ()
{
    add_management_page(__('Tabagile Scrum Board', 'otd'), __('Tabagile Scrum Board', 'otd'), 1, otd_plugin_basename(__FILE__), 'otd_manage_panel');
}
function otd_load_option_panel ()
{
    add_options_page(__('Tabagile Scrum Board', 'otd'), __('Tabagile Scrum Board', 'otd'), 8, otd_plugin_basename(__FILE__), 'otd_option_panel');
}


///////////////////////////  Activation

function otd_activate ()
{
    global $otd_tablename,  $wpdb, $userdata, $wp_roles;
    get_currentuserinfo();

    if( $wpdb->get_var("show tables like '$otd_tablename'") != $otd_tablename )
    {
        $sql = "CREATE TABLE $otd_tablename (
  		id bigint(20) NOT NULL auto_increment,
  		idParent int(11) default NULL,
  		sprintNumber int(11) default NULL,
  		points int(11) default NULL,
  		author bigint(20) NOT NULL default '0',
  		att bigint(4) NOT NULL default '0',
  		targetActors bigint(20) NOT NULL default '0',
  		tasktag bigint(4) NOT NULL default '0',
  		status tinyint(1) NOT NULL default '0',
  		priority tinyint(1) NOT NULL default '0',
  		todotext text NOT NULL,
  		created_at datetime NOT NULL default '0000-00-00 00:00:00',
  		starts_in datetime NOT NULL default '0000-00-00 00:00:00',
  		ended_in datetime NOT NULL default '0000-00-00 00:00:00',
  		UNIQUE KEY id (id)
        );";

        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($sql);

        $welcome_text = addslashes( __('This is Scrum Board plugin! You can use it to manage and share a list of product backlog with all members.', 'otd') );

        $insert = "INSERT INTO $otd_tablename ".
                  "(author,status,priority,todotext) ".
                  "VALUES ($userdata->ID,0,1,'$welcome_text')";

        $results = $wpdb->query($insert);
    }

    if ( function_exists('current_user_can') )  // WP >= v2.0
    {
        otd_update_whocanuse( array_keys($wp_roles->role_names) );
    }
}


////////////////////////  Logic

function otd_option_controller () {
    global $otd_message, $otd_action;

    $otd_action = $_POST['otd_action'];
    $otd_message = '';

    switch ($otd_action) {
        case 'updatetdopt':
        $whocanuse = $_POST['otd_whocanuse'];
		$whocanadmin = $_POST['otd_whocanadmin'];
        otd_update_whocanuse($whocanuse);
        otd_update_whocanadmin($whocanadmin);
        $otd_message = __('Updated.', 'otd');
        break;
        case 'addBacklog':
		$idParent = $_POST['otd_idParent'];
		$sprintNumber = $_POST['otd_sprintNumber'];
		$points = $_POST['otd_points'];
		$author = $_POST['otd_author'];
		$att = $_POST['otd_att'];
        $tasktag = $_POST['otd_tasktag'];
        $status = $_POST['otd_status'];
	    $priority = $_POST['otd_priority'];
        $todotext = $_POST['otd_description'];
        $created_at = $_POST['otd_created_at'];
        $starts_in = $_POST['otd_starts_in'];
        $ended_in = $_POST['otd_ended_in'];

        otd_insert($idParent, $sprintNumber, $points, $author, $att, $tasktag, $status, $priority, $todotext, $created_at, $starts_in, $ended_in);

        $otd_message = __('New Backlog Entry has been added.', 'otd');
        break;
    }
}

function otd_controller ()
{
    global $otd_message;

    $otd_action = $_POST['otd_action'];

    if ( empty($otd_action) ) { $otd_action = $_GET['otd_action']; }
    if ( empty($otd_action) ) { $otd_action = ''; }

    $otd_message = '';

    switch ($otd_action)
    {
        case 'addBacklog':
        $idParent = $_POST['otd_idParent'];
        $sprintNumber = $_POST['otd_sprintNumber'];
        $points = $_POST['otd_points'];
        $author = $_POST['otd_author'];
		$att = $_POST['otd_att'];
        $tasktag = $_POST['otd_tasktag'];
	    $priority = $_POST['otd_priority'];
	    $status = $_POST['otd_status'];
		$todotext = $_POST['otd_description'];
        $created_at = $_POST['otd_created_at'];
        $starts_in = $_POST['otd_starts_in'];
        $ended_in = $_POST['otd_ended_in'];

	    otd_insert($idParent, $sprintNumber, $points, $author, $att, $tasktag, $priority, $status, $todotext, $created_at, $starts_in, $ended_in);
        $otd_message = __('New Backlog Entry has been added.', 'otd');
        break;

        case 'comptd':
        $id = $_GET['id'];
        $status = $_GET['status'];
        otd_complete($id, $status);
        $otd_message = __('Story has been marked completed.', 'otd');
        break;

        case 'incoming':
        $id = $_GET['id'];
        $status = $_GET['status'];
        otd_incoming($id, $status);
        $otd_message = __('Story has been submited to sprint.', 'otd');
        break;

        case 'ready':
        $id = $_GET['id'];
        $status = $_GET['status'];
        otd_incoming($id, $status);
        $otd_message = __('Story has been marked as ready.', 'otd');
        break;

        case 'take':
        $current_user_id = $_GET['current_user_id'];
        $id = $_GET['id'];
        otd_take($current_user_id,$id);
        $otd_message = __('Story has been taked.', 'otd');
        break;

        case 'trashtd':
        $id = $_GET['id'];
        otd_delete($id);
        $otd_message = __('Story has been deleted.', 'otd');
        break;

        case 'updatetd':
        $id = $_POST['id'];
		$att = $_POST['otd_att'];

		$idParent = $_POST['otd_idParent'];
		$sprintNumber = $_POST['otd_sprintNumber'];
		$points = $_POST['otd_points'];
		$author = $_POST['otd_author'];

        $tasktag = $_POST['otd_tasktag'];
        $status = $_POST['otd_status'];
	    $priority = $_POST['otd_priority'];
        $todotext = $_POST['otd_todotext'];

        $created_at = $_POST['otd_created_at'];
        $starts_in = $_POST['otd_starts_in'];
        $ended_in = $_POST['otd_ended_in'];

        otd_update($id, $idParent, $sprintNumber, $points, $author, $att, $tasktag, $status, $priority, $todotext, $created_at, $starts_in, $ended_in);
        $otd_message = __('Story has been updated.', 'otd');
        break;

        case 'setuptd':
        otd_activate();
        $otd_message = __('Scrum database table has been installed.', 'otd');
        break;

        case 'purgetd':
        otd_purge();
        $otd_message = __('Completed Stories have been purged.', 'otd');
        break;

        case 'uncomptd':
        $id = $_GET['id'];
        otd_complete($id, '0');
        $otd_message = __('Story has been marked uncompleted.', 'otd');
        break;
   }
}

function otd_insert ($idParent, $sprintNumber, $points, $author, $att, $tasktag, $priority, $status, $todotext, $created_at, $starts_in, $ended_in)
{
    global $otd_tablename, $wpdb, $userdata;
    get_currentuserinfo();

    $insert = "INSERT INTO $otd_tablename ".
              "(idParent, sprintNumber, points, author, att, tasktag, status, priority, todotext, created_at, starts_in, ended_in)".
              "VALUES ($idParent, $sprintNumber, $points, $userdata->ID, $att, $tasktag, $status, $priority, '$todotext','$created_at', '$starts_in','$ended_in')";
    $results = $wpdb->query( $insert );
}

function otd_update ($id, $idParent, $sprintNumber, $points, $author, $att, $tasktag, $priority, $status, $todotext, $created_at, $starts_in, $ended_in)
{
    global $otd_tablename, $wpdb, $userdata;
    get_currentuserinfo();

    $update = "UPDATE $otd_tablename ".
              "SET idParent = '$idParent', sprintNumber = '$sprintNumber', points = '$points', author = '$author', att = '$att', tasktag = '$tasktag', status = '$status', priority = '$priority', todotext = '$todotext', created_at = '$created_at', starts_in = '$starts_in', ended_in = '$ended_in' WHERE id = '$id'";

    $results = $wpdb->query( $update );
}

function otd_delete ($id)
{
    global $otd_tablename,  $wpdb;
    $delete = "DELETE FROM $otd_tablename WHERE id = '$id'";
    $results = $wpdb->query( $delete );
}

function otd_complete ($id, $status)
{
    global $otd_tablename,  $wpdb;
	$ended_in = date_i18n('Y-m-d G:i:s');
    $update = "UPDATE $otd_tablename SET status = '$status', ended_in = '$ended_in' WHERE id = '$id'";
    $results = $wpdb->query( $update );
}

function otd_get_todo ($id)
{
    global $otd_tablename, $wpdb;

    $edit = "SELECT * FROM $otd_tablename WHERE id = '$id' LIMIT 1";
    $result = $wpdb->get_row( $edit );
    return $result;
}

function otd_purge ()
{
    global $otd_tablename,  $wpdb;

    $purge = "DELETE FROM $otd_tablename WHERE status = '1'";
    $results = $wpdb->query( $purge );
}

function otd_incoming ($id, $status)
{
    global $otd_tablename,  $wpdb;
	$starts_in = date_i18n('Y-m-d G:i:s');
    $update = "UPDATE $otd_tablename SET status = '$status', starts_in = '$starts_in' WHERE id = '$id'";
    $results = $wpdb->query( $update );
}

function otd_take ($current_user_id, $id)
{
    global $otd_tablename,  $wpdb;
	$starts_in = date_i18n('Y-m-d G:i:s');
    $update = "UPDATE $otd_tablename SET att = '$current_user_id' WHERE id = '$id'";
    $results = $wpdb->query( $update );
}

function otd_update_whocanuse ($roles)
{
    global $wp_roles;
    $wp_roles->WP_Roles();

    if ( empty($roles) || ! is_array($roles) ) { $roles = array(); }

    $who_can = $roles;
    $who_cannot = array_diff( array_keys($wp_roles->role_names), $roles);

    foreach ($who_can as $role) {
        $wp_roles->add_cap($role, 'use_ourtodolist');

    }
    foreach ($who_cannot as $role) {
        $wp_roles->remove_cap($role, 'use_ourtodolist');
    }
}

function otd_update_whocanadmin ($roles)
{
    global $wp_roles;
    $wp_roles->WP_Roles();

    if ( empty($roles) || ! is_array($roles) ) { $roles = array(); }

    $who_can = $roles;
    $who_cannot = array_diff( array_keys($wp_roles->role_names), $roles);

    foreach ($who_can as $role) {
        $wp_roles->add_cap($role, 'admin_ourtodolist');

    }
    foreach ($who_cannot as $role) {
        $wp_roles->remove_cap($role, 'admin_ourtodolist');
    }
}




////////////////////////////  UI


/* Display UI to manage option */
function otd_option_panel()
{
    otd_option_controller();
	otd_controller ();

    global $wp_roles, $otd_message;
    $wp_roles->WP_Roles();
?>


<?php if ( !empty($otd_message) ) : ?>
<div id="message" class="updated fade"><p><?php echo $otd_message; ?></p></div>
<?php endif; ?>


<!--################### #################### ##################-->

<div class="wrap">
<h2><?php _e('Product Backlog', 'otd'); ?></h2>
<h3><?php _e('Who can use the Product Backlog?', 'otd'); ?></h3>
<form method="post">

<table border="1" cellpadding="3"><tr><td>use</td><td>admin</td><td>rolename</td></tr>


<?php


foreach ($wp_roles->role_names as $roledex => $rolename)
{
    $role = $wp_roles->get_role($roledex);
    $checked_use = $role->has_cap('use_ourtodolist') ? 'checked="checked"' : '';
    $checked_admin = $role->has_cap('admin_ourtodolist') ? 'checked="checked"' : '';
    $readonly = ($roledex == 'administrator') ? 'readonly="readonly"'  : '';

    echo '<tr><td><input type="checkbox" '.$readonly.' '.$checked_use.' id="'.$roledex.'_option" '. "\n";
    echo 'name="otd_whocanuse[]" value="'.$roledex.'" /></td>';

    echo '<td><input type="checkbox" '.$readonly.' '.$checked_admin.' id="'.$roledex.'_option" '. "\n";
    echo 'name="otd_whocanadmin[]" value="'.$roledex.'" /></td>';

    echo '<td><label for="'.$roledex.'_option" >'.$rolename.'</label></td></tr>'. "\n";

}
?>
	</table>

    <input type="hidden" name="otd_action" value="updatetdopt" />
    <p class="submit"><input type="submit" name="submit" value="<?php _e('Update Options &raquo', 'otd'); ?>" /></p>
</form>
</div>
<?php
}

/* Display UI to manage todolist  */
function otd_manage_panel()
{
    otd_controller();

    global $wpdb, $otd_tablename, $otd_tablename_users, $otd_location, $otd_message;

		// monta array para dropdown com os usuários

    	$sql = "SELECT * FROM ". $otd_tablename_users;
		$results = $wpdb->get_results($sql);
     	if ($results)
     	{
     		foreach ($results as $result)
     		{
     			$att[$result->ID] = $result->user_nicename;
     		}
     	}

		// monta array para dropdown com os usuários


		// monta array para dropdown com os idParent


    	$sql = "SELECT * FROM ". $otd_tablename;
		$results = $wpdb->get_results($sql);
     	if ($results)
     	{
     		foreach ($results as $result)
     		{
     			$idParent[$result->id] = $result->id;
     		}
     	}

		// monta array para dropdown com os idParent


		// monta array para dropdown com os sprintNumber


    	$sql = "SELECT * FROM ". $otd_tablename;
		$results = $wpdb->get_results($sql);
     	if ($results)
     	{
     		foreach ($results as $result)
     		{
     			$sprintNumber[$result->sprintNumber] = $result->sprintNumber;
     		}
     	}

		// monta array para dropdown com os sprintNumber


		// monta array para dropdown com os authors

    	$sql = "SELECT * FROM ". $otd_tablename_users;
		$results = $wpdb->get_results($sql);
     	if ($results)
     	{
     		foreach ($results as $result)
     		{
     			$author[$result->id] = $result->user_nicename;
     		}
     	}

		// monta array para dropdown com os authors



    $priority = array(
                  0 => __('not set', 'otd'),
                  1 => __('important', 'otd'),
                  2 => __('normal', 'otd'),
                  3 => __('low', 'otd')
              );


    $tasktag = array(
                  0 => __('not set', 'otd'),
                  1 => __('story', 'otd'),
                  2 => __('epic', 'otd'),
                  3 => __('theme', 'otd'),
                  4 => __('project', 'otd'),
                  5 => __('task', 'otd')
              );

    $status = array(
                  0 => __('not set', 'otd'),
                  1 => __('notready', 'otd'),
                  2 => __('ready', 'otd'),
                  3 => __('running', 'otd'),
                  4 => __('done', 'otd')
              );

?>

<?php if ( ! empty($otd_message) ) : ?>
<div id="message" class="updated fade"><p><?php echo $otd_message; ?></p></div>
<?php endif; ?>

<?php
 if($_GET['otd_action'] == 'edittd')
 {
    $id = $_GET['id'];
    $todo = otd_get_todo($id);

    $selection_att = '';
    $selection_author = '';
    $selection_sprintNumber = '';


    foreach ($att as $id => $attendant)
    {
        $selected = ($todo->att == $id) ? 'selected="selected"' : '';
        $selection_att .= "  <option value=\"$id\" $selected>{$attendant}</option>\n";
    }


    foreach ($idParent as $idPar => $parent)
    {
        $selected = ($todo->idParent == $idPar) ? 'selected="selected"' : '';
        $selection_idParent .= "  <option value=\"$idPar\" $selected>{$parent}</option>\n";
    }


    foreach ($author as $idPo => $productOwner)
    {
        $selected = ($todo->author == $idPo) ? 'selected="selected"' : '';
        $selection_author .= "  <option value=\"$idPo\" $selected>{$productOwner}</option>\n";
    }


    foreach ($sprintNumber as $idSn => $sprint)
    {
        $selected = ($todo->sprintNumber == $idSn) ? 'selected="selected"' : '';
        $selection_sprintNumber .= "  <option value=\"$idSn\" $selected>{$sprint}</option>\n";
    }


    $selection_tasktag = '';
    for ($h = 0; $h < count($tasktag); $h++)
    {
        $selected = ($todo->tasktag == $h) ? 'selected="selected"' : '';
        $selection_tasktag .= "  <option value=\"$h\" $selected>{$tasktag[$h]}</option>\n";
    }

    $selection_status = '';
    for ($j = 0; $j < count($status); $j++)
    {
        $selected = ($todo->status == $j) ? 'selected="selected"' : '';
        $selection_status .= "  <option value=\"$j\" $selected>{$status[$j]}</option>\n";
    }

    $selection = '';
    for ($k = 0; $k < count($priority); $k++)
    {
        $selected = ($todo->priority == $k) ? 'selected="selected"' : '';
        $selection .= "  <option value=\"$k\" $selected>{$priority[$k]}</option>\n";
    }
?>



<div class="wrap">
 <h2><?php _e('Edit Backlog Entry', 'otd') ?></h2>
 <form method="post">
    <table class="editform" width="100%" cellspacing="2" cellpadding="5">



    <tr>
      <th width="33%" scope="row"><?php _e('Id Parent:', 'otd') ?></th>
      <td width="67%">
        <select name='otd_idParent' class='postform'>
        <option value="0">0</option>
        <?php echo $selection_idParent; ?>
        </select>
      </td>
    </tr>


    <tr>
      <th width="33%" scope="row"><?php _e('Product Owner:', 'otd') ?></th>
      <td width="67%">
        <select name='otd_author' class='postform'>
        <?php echo $selection_author; ?>
        </select>
      </td>
    </tr>


    <tr>
      <th width="33%" scope="row"><?php _e('Sprint Number:', 'otd') ?></th>
      <td width="67%">
        <select name='otd_sprintNumber' class='postform'>
        <?php echo $selection_sprintNumber; ?>
        </select>
      </td>
    </tr>

    <tr>
      <th scope="row"><?php _e('Points:', 'otd') ?></th>
      <td colspan="5">
       <input name="otd_points" type="text" style="width:37%;" value="<?php echo wp_specialchars($todo->points, 1); ?>">
      </td>
    </tr>



    <tr>
      <th width="33%" scope="row"><?php _e('Scrum Master:', 'otd') ?></th>
      <td width="67%">
        <select name='otd_att' class='postform'>
        <?php echo $selection_att; ?>
        </select>
      </td>
    </tr>

    <tr>
      <th width="33%" scope="row"><?php _e('EntryTag:', 'otd') ?></th>
      <td width="67%">
        <select name='otd_tasktag' class='postform'>
        <?php echo $selection_tasktag; ?>
        </select>
      </td>
    </tr>

    <tr>
      <th width="33%" scope="row"><?php _e('Status:', 'otd') ?></th>
      <td width="67%">
        <select name='otd_status' class='postform'>
        <?php echo $selection_status; ?>
        </select>
      </td>
    </tr>

    <tr>
      <th width="33%" scope="row"><?php _e('Priority:', 'otd') ?></th>
      <td width="67%">
        <select name='otd_priority' class='postform'>
        <?php echo $selection; ?>
        </select>
      <input type="hidden" name="id" value="<?php echo $todo->id ?>" />
      </td>
    </tr>


    <tr>
      <th scope="row"><?php _e('Description:', 'otd') ?></th>
      <td colspan="5">
       <textarea name="otd_todotext"
       rows="5" cols="50" style="width:97%;"><?php echo wp_specialchars($todo->todotext, 1); ?></textarea>
      </td>
    </tr>

    <tr>
      <th scope="row"><?php _e('Created At:', 'otd') ?></th>
      <td colspan="5">
       <input name="otd_created_at" type="text" style="width:37%;" value="<?php echo wp_specialchars($todo->created_at, 1); ?>">
      </td>
    </tr>

    <tr>
      <th scope="row"><?php _e('Starts:', 'otd') ?></th>
      <td colspan="5">
       <input name="otd_starts_in" type="text" style="width:37%;" value="<?php echo wp_specialchars($todo->starts_in, 1); ?>">
      </td>
    </tr>

    <tr>
      <th scope="row"><?php _e('End:', 'otd') ?></th>
      <td colspan="5">
       <input name="otd_ended_in" type="text" style="width:37%;" value="<?php echo wp_specialchars($todo->ended_in, 1); ?>">
      </td>
    </tr>

    </table>
    <p class="submit">
      <input type="hidden" name="otd_action" value="updatetd" />
      <input type="submit" name="submit" value="<?php _e('Update Todo', 'otd') ?>" />
    </p>
 </form>
 <p><a href="<?php echo $otd_location; ?>"><?php _e('&laquo; Return to todo list', 'otd'); ?></a></p>
</div>









<?php
 }
 else
 {
?>

















<div class="wrap">
<h2><?php _e('Stories on Sprint', 'otd'); ?>
(<a href="#addtd"><?php _e('add new', 'otd'); ?></a>)</h2>
<table class="widefat" id="todo-list" width="100%" cellpadding="3" cellspacing="3">
  <thead>
  <tr>
     <th><?php _e('Done it!', 'otd'); ?></th>
     <th><?php _e('Id', 'otd'); ?></th>



     <th><?php _e('Par.', 'otd'); ?></th>




     <th><?php _e('Scrum M.', 'otd'); ?></th>
     <th><?php _e('Type', 'otd'); ?></th>
     <th><?php _e('Start', 'otd'); ?></th>

     <th><?php _e('Priority', 'otd'); ?></th>
     <th><?php _e('Desc.', 'otd'); ?></th>
     <th><?php _e('Status', 'otd'); ?></th>
     <th colspan="2"><?php _e('Action', 'otd'); ?></th>
  </tr>
  </thead>
  <tbody>


<?php
   $sql = "SELECT id, idParent, author, att, tasktag, status, priority, todotext, created_at, starts_in FROM ".$otd_tablename . " WHERE status = 3 ORDER BY priority, todotext";
   $results = $wpdb->get_results($sql);

 	global $current_user;

	get_currentuserinfo();

   if ($results)
   {
     foreach ($results as $result)
     {
       $user = get_userdata($result->author);
       $idParent_str = $result->idParent;
       $class = ('alternate' == $class) ? '' : 'alternate';
       $priority_str = $priority[ $result->priority ];
       $att_str = get_userdata($result->att);
	   $status_str = $status[ $result->status ];
       $tasktag_str = $tasktag[ $result->tasktag ];
       $edit = '<a href="' . $otd_location . '&otd_action=edittd&id='.
               $result->id . '" class="edit">'.__('Edit', 'otd') . '</a>&nbsp;'.
               '<a href="' . $otd_location . '&otd_action=trashtd&id='.
               $result->id . '" class="delete">'.__('Delete', 'otd').'</a>&nbsp;'.
			   '<a href="' . $otd_location . '&otd_action=take&id='.
               $result->id . '&current_user_id='. $current_user->id . '" class="edit">'.__('Take', 'otd').'</a>';

       echo "<tr id=\"otd-{$result->id}\" class=\"$class\">
       <td width=\"1%\" ><input type=\"checkbox\" id=\"td-{$result->id}\"
       onclick=\"window.location='$otd_location&otd_action=comptd&status=4&id=$result->id';\" /></td>

	   <td>#{$result->id}</td>
	   <td>#{$idParent_str}</td>
       <td>{$att_str->user_nicename}</td>
       <td>$tasktag_str</td>
	   <td>{$result->starts_in}</td>
       <td>$priority_str</td>
	   <td>{$result->todotext}</td>
       <td>{$status_str}</td>
       <td>$edit</td>
       </tr>";
     }
   }
   else
   {
     echo '<tr><td colspan="9">'.__('There is nothing to do..', 'otd').'</td></tr>';
   }

?>
  </tbody>
</table>
</div>
























<div class="wrap">



<h2><?php _e('Product Backlog', 'otd'); ?>(<a href="#addtd"><?php _e('add new', 'otd'); ?></a>) </h2>

<table class="widefat" id="todo-list" width="100%" cellpadding="3" cellspacing="3">
  <thead>
  <tr>
     <th><?php _e('Sprint it!', 'otd'); ?></th>
     <th><?php _e('Id', 'otd'); ?></th>


     <th><?php _e('Par.', 'otd'); ?></th>
     <th><?php _e('Sprint', 'otd'); ?></th>
     <th><?php _e('Pts.', 'otd'); ?></th>

     <th><?php _e('P.O', 'otd'); ?></th>

     <th><?php _e('Scrum M.', 'otd'); ?></th>
     <th><?php _e('Type', 'otd'); ?></th>
     <th><?php _e('Created', 'otd'); ?></th>

     <th><?php _e('Prior.', 'otd'); ?></th>
     <th><?php _e('Desc.', 'otd'); ?></th>
     <th><?php _e('Status', 'otd'); ?></th>
     <th><?php _e('Action', 'otd'); ?></th>
  </tr>
  </thead>
  <tbody>
<?php

	global $current_user;

	get_currentuserinfo();

   $sql = "SELECT id, idParent, sprintNumber, points, author, att, tasktag, status, priority, todotext, created_at FROM ".$otd_tablename." WHERE status <= 2  ORDER BY priority";
   $results = $wpdb->get_results($sql);
   if ($results)
   {
     foreach ($results as $result)
     {
       $user = get_userdata($result->author);
       $class = ('alternate' == $class) ? '' : 'alternate';
       //$att_str = $att[ $result->att ];

       $idParent_str = $result->idParent;
       $sprintNumber_str = $result->sprintNumber;
       $points_str = $result->points;
       $author_str = get_userdata($result->author);
       $att_str = get_userdata($result->att);
       $tasktag_str = $tasktag[ $result->tasktag ];
       $prstr = $priority[ $result->priority ];
	   $status_str = $status[ $result->status ];


       $edit = '<a href="' . $otd_location . '&otd_action=edittd&id='.
               $result->id . '" class="edit">'.__('Edit', 'otd') . '</a>&nbsp;'.
               '<a href="' . $otd_location . '&otd_action=trashtd&id='.
               $result->id . '" class="delete">'.__('Delete', 'otd').'</a>&nbsp;'.
			   '<a href="' . $otd_location . '&otd_action=take&id='.
               $result->id . '&current_user_id='. $current_user->id . '" class="edit">'.__('Take', 'otd').'</a>';

       echo "<tr id=\"otd-{$result->id}\" class=\"$class\">
       <td width=\"1%\" ><input type=\"checkbox\" id=\"td-{$result->id}\"
       onclick=\"window.location='$otd_location&otd_action=incoming&status=3&id=$result->id';\" /></td>


	   <td>#{$result->id}</td>

	   <td>#{$idParent_str}</td>
	   <td>#{$sprintNumber_str}</td>
	   <td>{$points_str}</td>

       <td>{$author_str->user_nicename}</td>

       <td>{$att_str->user_nicename}</td>
       <td>{$tasktag_str}</td>
	   <td>{$result->created_at}</td>

       <td>$prstr</td>
	   <td>{$result->todotext}</td>
       <td>{$status_str}</td>
       <td>$edit</td>
       </tr>";
     }
   }
   else
   {
     echo '<tr><td colspan="9">'.__('There is nothing to do..', 'otd').'</td></tr>';
   }

?>
  </tbody>
</table>
</div>





















<div class="wrap">
<h2><?php _e('Completed Stories', 'otd'); ?>
(<a href="<?php echo $otd_location; ?>&otd_action=purgetd"><?php _e('purge', 'otd'); ?></a>)</h2>
<table class="widefat" id="todo-list" width="100%" cellpadding="3" cellspacing="3">
    <thead>
    <tr>

     <th><?php _e('Undone it!', 'otd'); ?></th>

     <th><?php _e('Id', 'otd'); ?></th>
     <th><?php _e('Srum M.', 'otd'); ?></th>
     <th><?php _e('Type', 'otd'); ?></th>
     <th><?php _e('Solved in', 'otd'); ?></th>

     <th><?php _e('Priority', 'otd'); ?></th>
     <th><?php _e('Desc.', 'otd'); ?></th>
     <th><?php _e('Status', 'otd'); ?></th>
     <th colspan="2"><?php _e('Action', 'otd'); ?></th>

    </tr>
    </thead>
    <tbody>



<?php
   $sql = "SELECT id, author, att, tasktag, status, priority, todotext, starts_in, ended_in FROM ".
   $otd_tablename . " WHERE status = 4 ORDER BY ended_in";
   $results = $wpdb->get_results($sql);

	function subtract_dates($begin_date, $end_date)
		{
			return round(((strtotime($end_date) - strtotime($begin_date)) / 86400));
		}

   if ($results)
   {
     foreach ($results as $result)
     {
       $user = get_userdata($result->author);
       $class = ('alternate' == $class) ? '' : 'alternate';
       $att_str = get_userdata($result->att);
       $tasktag_str = $tasktag[ $result->tasktag ];
       $prstr = $priority[ $result->priority ];
       $status_str = $status[ $result->status ];

       $edit = '<a href="'. $otd_location . '&amp;otd_action=trashtd&amp;id='.
               $result->id . '" class="delete">'.__('Delete', 'otd') . '</a>';

		$date_diff = subtract_dates($result->starts_in, $result->ended_in);
		$time_to_solve = $date_diff . " days";

       echo "<tr id=\"otd-$result->id\" class=\"$class\">
       <td width=\"1%\"><input type=\"checkbox\" id=\"td-{$result->id}\" checked=\"checked\"
       onclick=\"window.location = '$otd_location&status=3&otd_action=uncomptd&id=$result->id';\" /></td>


 	   <td>#{$result->id}</td>
       <td>{$att_str->user_nicename}</td>
       <td>{$tasktag_str}</td>
	   <td>$time_to_solve</td>

       <td>$prstr</td>
	   <td>{$result->todotext}</td>
       <td>{$status_str}</td>
       <td>$edit</td>


       </tr>";
     }
   }
   else
   {
     echo $sql.'<tr><td colspan="4">'.__('There are no completed tasks.', 'otd').'</td></tr>';
   }
?>
   </tbody>
</table>
</div>

<div class="wrap">

    <h2><?php _e('Add Backlog Entry', 'otd') ?></h2>
    <form name="addtd" id="addtd" method="post">

    	<p>
		<?php _e('Parent ID:', 'otd') ?><br />
        <input type="text" name="otd_idParent" class='postform' value="0">
    	</p>

    	<p>
		<?php _e('Sprint Number:', 'otd') ?><br />
        <input type="text" name="otd_sprintNumber" class='postform' value="0">
    	</p>

     	<p>
 		<?php _e('Points:', 'otd') ?><br />
        <input type="text" name="otd_points" class='postform' value="0">
    	</p>


    	<p>
		<?php _e('Attendant:', 'otd') ?><br />

		<!--// monta dropdown com os usuários-->
        <select name='otd_att' class='postform'>
        <option value='0'><?php _e('select one', 'otd'); ?></option>
		<?php
    	$sql = "SELECT * FROM ". $otd_tablename_users;
		$results = $wpdb->get_results($sql);
     	if ($results)
     	{
     		foreach ($results as $result)
     		{
     			$usridstr=$result->ID;
      			$usrfirstnamestr=$result->user_nicename;
      			echo "<option value='$usridstr'>$usrfirstnamestr</option>";
     		}
     	}
  		?>
  		</select>
		<!--// monta dropdown com os usuários-->
        </p>

        <p>
		<?php _e('Entry Type:', 'otd') ?><br />
        <select name='otd_tasktag' class='postform'>
        <option value='0'><?php _e('select one', 'otd'); ?></option>
        <option value='1'><?php _e('story', 'otd'); ?></option>
        <option value='2'><?php _e('epic', 'otd'); ?></option>
        <option value='3'><?php _e('theme', 'otd'); ?></option>
        <option value='4'><?php _e('project', 'otd'); ?></option>
        <option value='5'><?php _e('task', 'otd'); ?></option>
        </select>
        </p>

        <p>
		<?php _e('Priority:', 'otd') ?><br />
        <select name='otd_priority' class='postform'>
        <option value='0'><?php _e('select one', 'otd'); ?></option>
        <option value='1'><?php _e('important', 'otd'); ?></option>
        <option value='2' selected="selected"><?php _e('normal', 'otd'); ?></option>
        <option value='3'><?php _e('low', 'otd'); ?></option>
        </select>
        </p>

        <p>
		<?php _e('Status:', 'otd') ?><br />
        <select name='otd_status' class='postform'>
        <option value='0'><?php _e('select one', 'otd'); ?></option>
        <option value='1' selected="selected"><?php _e('notready', 'otd'); ?></option>
        <option value='2'><?php _e('ready', 'otd'); ?></option>
        <option value='3'><?php _e('incoming', 'otd'); ?></option>
        <option value='4'><?php _e('done', 'otd'); ?></option>
        </select>
        </p>


        <p>
		<?php _e('Description:', 'otd') ?> <br />
        <textarea name="otd_description" rows="5" cols="50" style="width: 97%;"></textarea>
        </p>

        <p class="submit">

          <input type="hidden" name="otd_created_at" value="<?php printf(__('%2$s'), $current_offset_name, date_i18n(__('Y-m-d G:i:s'))); ?>" />
          <input name="otd_starts_in" type="hidden" style="width:37%;" value="0000-00-00 00:00:00">
          <input name="otd_ended_in" type="hidden" style="width:37%;" value="0000-00-00 00:00:00">
          <input type="hidden" name="otd_action" value="addBacklog" />
          <input type="submit" name="submit" value="<?php _e('Add Backlog &raquo;', 'otd') ?>" />
        </p>
    </form>
</div>

<div class="wrap">
   <h2><?php _e('Install', 'otd') ?></h2>
   <p><?php printf(
__('If you are running Wordpress 1.5 you will need to run %sthis script%s to install the required database table. Maeka. Go! Go! Go!', 'otd'),
'<a href="'.$otd_location.'&amp;otd_action=setuptd">', '</a>'); ?></p>
</div>

<?php
  }
}


/* Display top 10 items on Dashboard  */

function otd_todo_in_activity_box()
{

global $current_user, $wp_roles;

get_currentuserinfo();
otd_option_controller ();

$wp_roles->WP_Roles();

switch($current_user->user_level){
	case 0:$user_role="subscriber";
	break;
	case 1:$user_role="contributor";
	break;
	case 2: case 3: case 4: $user_role="author";
	break;
	case 5: case 6: case 7: $user_role="editor";
	break;
	case 8: case 9: case 10: $user_role="administrator";
	break;
}

$role = $wp_roles->get_role($user_role);
$user_cap_ourtodo_admin = $role->has_cap('admin_ourtodolist');
$user_cap_ourtodo_use = $role->has_cap('use_ourtodolist');


   global $otd_tablename, $wpdb, $otd_location;


if ($user_cap_ourtodo_admin) {
	$sql = "SELECT id, att, author, todotext FROM " . $otd_tablename .
          " WHERE status < 4 ORDER BY priority,id LIMIT 10";

   echo '<div><h3>'.__('Product Backlog', 'otd').
        ' <a href="' . $otd_location . '">'.
        __('&raquo;', 'otd').'</a></h3>';


   $results = $wpdb->get_results($sql);
   if ($results)
   {
     echo '<ol>';
     foreach ($results as $result)
     {
        $user = get_userdata($result->att);
        echo "<li>#$result->id - $result->todotext <small>".
             '(<a href="'. $otd_location . '&otd_action=edittd&id=' . $result->id . '">'.
             __('Edit', 'otd')  . '</a>)</small> ~'.$user->user_nicename.'</li>';
     }
     echo '</ol>';
   }
   else {echo '<p>'.__('There is nothing to do..', 'otd').'</p>';
   }
 echo '<p style="text-align:right">'.
         '<a href="' . $otd_location . '#addtd">'.
         __('New Todo &raquo;', 'otd').'</a></p></div>';
	} else {
	$sql = "SELECT * FROM " . $otd_tablename .
           " WHERE status < 4 and author = ". $current_user->ID.
           " or status < 4 and att = ". $current_user->ID .
           " ORDER BY priority,id LIMIT 10";


				   echo '<div><h3>'.__('Your orders', 'otd').
						' <!--a href="' . $otd_location . '">'.
						__('&raquo;', 'otd').'</a--></h3>';


				   $results = $wpdb->get_results($sql);
				   if ($results)
				   {
					 echo '<ol>';
					 foreach ($results as $result)
					 {
						$user = get_userdata($result->att);
						echo "<li>#$result->id - $result->todotext <small>".
							 '(<a href="'. $otd_location . '&otd_action=edittd&id=' . $result->id . '">'.
							 __('Edit', 'otd')  . '</a>)</small> ~'.$user->user_nicename.'</li>';
					 }
					 echo '</ol>';
				   }
				   else {echo '<p>'.__('There is nothing to do here..', 'otd').'</p>';}



	$sql_completed = "SELECT id, att, author, todotext FROM " . $otd_tablename .
          " WHERE status = 1 and author = ". $current_user->ID ."  ORDER BY priority,id LIMIT 10";

				   echo '<div><h3>'.__('Completed orders', 'otd').
						' <!--a href="' . $otd_location . '">'.
						__('&raquo;', 'otd').'</a--></h3>';


				   $results_completed = $wpdb->get_results($sql_completed);
				   if ($results_completed)
				   {
					 echo '<ol>';
					 foreach ($results_completed as $result_completed)
					 {
						$user = get_userdata($result_completed->author);
						$att = get_userdata($result_completed->att);
						echo "<li>#$result_completed->id - $result_completed->todotext <small>".
							 '(<a href="'. $otd_location . '&otd_action=edittd&id=' . $result_completed->id . '">'.
							 __('Edit', 'otd')  . '</a>)</small> ~'.$att->user_nicename.'</li>';
					 }
					 echo '</ol>';
				   }
				   else {echo '<p>'.__('There is nothing completed here..', 'otd').'</p>';}


?>





<p><h3><?php _e('New backlog entry', 'otd')?></h3></p>

    <form name="addtd" id="addtd" method="post">
    <p>
		<?php _e('Type:', 'otd') ?><br />
        <select name='otd_tasktag' class='postform'>
        <option value='select_one'><?php _e('select one', 'otd'); ?></option>
        <option value='0'><?php _e('story', 'otd'); ?></option>
        <option value='1'><?php _e('epic', 'otd'); ?></option>
        <option value='2'><?php _e('theme', 'otd'); ?></option>
        <option value='3'><?php _e('project', 'otd'); ?></option>
        </select>
     </p>

     <p>
        <input type="hidden" name="otd_author" value="<?php echo $current_user->ID ?>">
        <input type="hidden" name="otd_priority" value="normal">
        <input type="hidden" name='otd_att' value="<?php echo $current_user->ID ?>">
     </p>

        <p>
		<?php _e('Description:', 'otd') ?> <br />
        <textarea name="otd_description" rows="5" cols="50" style="width: 97%;"></textarea>
        </p>

        <p class="submit">
          <input type="hidden" name="otd_created_at" value="<?php printf(__('%2$s'), $current_offset_name, date_i18n(__('Y-m-d G:i:s'))); ?>" />
          <input type="hidden" name="otd_action" value="addBacklog" />
          <input type="submit" name="submit" value="<?php _e('Add Todo &raquo;', 'otd') ?>" />
        </p>
    </form>




<?php

				echo '</div>';
	}
}





///////////////////////////  Developer APIs


/* Public CONSTANTS */

define('OTD_NOTREADY', 0);
define('OTD_READY', 1);
define('OTD_INCOMING', 2);
define('OTD_DONE', 3);

define('OTD_ALL', OTD_NOTREADY+OTD_READY+OTD_INCOMING+OTD_DONE);


/* Private function */
function otd_get_todo_list_($ret_bool=false, $status=OTD_NOTREADY,
                            $limit=0, $show_priority=true, $show_author=true) {
  global $otd_tablename, $wpdb;

  $priority_table = array(0=> __('important', 'otd') , 1 => __('normal', 'otd'), 2=> __('low', 'otd'));
  $limit  = (preg_match('/^[0-9]+$/', $limit) && $limit > 0) ? "LIMIT $limit" : '';

  switch ($status)
  {
    case OTD_NOTREADY:
      $where = ' WHERE status = 0 ';
      break;
    case OTD_READY:
      $where = ' WHERE status = 1 ';
      break;
    case OTD_INCOMING:
      $where = ' WHERE status = 2 ';
      break;
    case OTD_DONE:
      $where = ' WHERE status = 3 ';
      break;
    case OTD_ALL:
    default:
      $where = '';
      break;
  }

  $sql = "SELECT author, todotext, priority, status FROM $otd_tablename ".
         "$where ORDER BY priority $limit";
  $results = $wpdb->get_results($sql);



  if ($results) {
    if ($ret_bool) {
      return true;
    }

    foreach ($results as $result) {
      $user = get_userdata($result->author);
      $author = ($show_author)
                ? ' <span class="otd_author">~ '.$user->display_name.'</span>'
                : '';
      $priority = ($show_priority)
                  ? ' <span class="otd_priority">('.
                    $priority_table[$result->priority].')</span>'
                  : '';
      $classname = (1 == $result->status)
                   ? 'otd_done'
                   : 'otd_ready';

      echo "<li class=\"$classname\">$result->todotext$priority$author</li>";
    }
  }
  else {
    return false;
  }
  return true;
}


/* Public functions  */

function ourtodolist_all($limit=0, $show_priority=false, $show_author=false) {
  return otd_get_todo_list_(false, OTD_ALL, $limit, $show_priority, $show_author);
}

function ourtodolist($limit=0, $show_priority=false, $show_author=false) {
  return otd_get_todo_list_(false, OTD_NOTREADY, $limit, $show_priority, $show_author);
}

function ourtodolist_ready($limit=0, $show_priority=false, $show_author=false) {
  return otd_get_todo_list_(false, OTD_READY, $limit, $show_priority, $show_author);
}

function ourtodolist_completed($limit=0, $show_priority=false, $show_author=false) {
  return otd_get_todo_list_(false, OTD_DONE, $limit, $show_priority, $show_author);
}

function ourtodolist_exists($status=OTD_NOTREADY) {
  /* backward-compatible - prior v1.7 */
  if (true === $status) { $status = OTD_DONE; }
  else if (false === $status) { $status = OTD_NOTREADY; }

  return otd_get_todo_list_(true, $status);
}


/////////////////////////  Misc

// Replace plugin_basename() in WP, taken from <http://trac.wordpress.org/ticket/4408>
function otd_plugin_basename ($file)
{
    $file = str_replace('\\','/',$file); // sanitize for Win32 installs
    $file = preg_replace('|/+|','/', $file); // remove any duplicate slash
    $file = preg_replace('|^.*/wp-content/plugins/|','',$file); // get relative path from plugins dir
    return $file;
}




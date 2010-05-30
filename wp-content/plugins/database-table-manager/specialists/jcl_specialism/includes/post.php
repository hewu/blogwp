<?php
/**
 * Edit post administration panel.
 *
 * Manage Post actions: post, edit, delete, etc. STOP_PRESS
 *
 * @package WordPress
 * @subpackage Administration
 */
/** WordPress Administration Bootstrap */

require_once('admin.php');


$parent_file = 'edit.php';
$submenu_file = 'edit.php';
if (isset($_REQUEST['myaction'])){
	$action = $_REQUEST['myaction'];
}else{
	$action = "none";
};

if (isset($_REQUEST["post"])){
	$post_ID = (int) $_REQUEST["post"];
} else if (isset($_POST["post_ID"])){
	$post_ID = (int) $_POST["post_ID"];
} else {
	$post_ID = 0;
}

	
switch($action) {
case 'update':

	if (!isset($_POST["cancelbutton"])){
		if ($post_ID != 0){
			$this_table_obj->do_update($post_ID);
		}else{
			$post_ID = $this_table_obj->do_insert();
		}
	}
	
	
//note the break is deliberately missing here

case 'edit':
	$editing = true;
	
	$title = __('Edit '.$this_table_obj->plugintitle);
	
	if ((isset($_POST["finishbutton"]) && $_POST["finishbutton"] != "") || (isset($_POST["cancelbutton"]) && $_POST["cancelbutton"] != "")){
		include WP_PLUGIN_DIR.'/'.$this_table_obj->pluginprefix.$this_table_obj->pluginname.'/includes/edit.php';
	}else{
	
		$post = $this_table_obj->get_select("form", $post_ID);

		if ( empty($post->ID) ) wp_die( __('You attempted to edit a post that doesn&#8217;t exist. Perhaps it was deleted?') );
	
		include(WP_PLUGIN_DIR.'/'.$this_table_obj->pluginprefix.$this_table_obj->pluginname.'/includes/edit-form-advanced.php');
	}

	break;
	
case 'add':
	$editing = true;

	$title = __('Add '.$this_table_obj->plugintitle);
	
	$post = $this_table_obj->do_add_defaults($post_ID);
	//$post = add_jcl_stop_press();

	include(WP_PLUGIN_DIR.'/'.$this_table_obj->pluginprefix.$this_table_obj->pluginname.'/includes/edit-form-advanced.php');

	break;


case 'delete':
	
	
	$this_table_obj->do_delete($post_ID);

	include (WP_PLUGIN_DIR.'/'.$this_table_obj->pluginprefix.$this_table_obj->pluginname.'/includes/edit.php');

	break;
	
case 'multidelete':

	$this_table_obj->do_delete($_REQUEST["rowID"]);
	include (WP_PLUGIN_DIR.'/'.$this_table_obj->pluginprefix.$this_table_obj->pluginname.'/includes/edit.php');

	break;

default:
	include (WP_PLUGIN_DIR.'/'.$this_table_obj->pluginprefix.$this_table_obj->pluginname.'/includes/edit.php');;
	break;
} // end switch


function writeFilters($this_table_obj){
?>
	<input type="hidden" value="<?php echo $this_table_obj->filterstatement ?>" id="filterstatement" name="filterstatement"/>
	<input type="hidden" value="<?php echo $this_table_obj->orderstatement ?>" id="orderstatement" name="orderstatement"/>
	<input type="hidden" value="<?php echo $this_table_obj->filterfield ?>" id="filterfield" name="filterfield"/>
	<input type="hidden" value="<?php echo $this_table_obj->filterid ?>" id="filterid" name="filterid"/>
	<input type="hidden" value="<?php echo $this_table_obj->orderfield ?>" id="orderfield" name="orderfield"/>
	<input type="hidden" value="<?php echo $this_table_obj->orderdirection ?>" id="orderdirection" name="orderdirection"/>
<?php
}

function matchSort ($this_table_obj,$sort){
	//echo "[[[".$this_table_obj->orderfield."]]]";
	//echo "[[[".$sort."]]]";
	//echo "[[[".$this_table_obj->tablename.".".$sort."]]]";
	//echo substr($this_table_obj->orderfield, strpos($this_table_obj->orderfield,".") + 1);
	
	if ($this_table_obj->orderfield == $sort)
			return true;
	if ($this_table_obj->orderfield == $this_table_obj->tablename.".".$sort)
			return true;
	if (substr($this_table_obj->orderfield, strpos($this_table_obj->orderfield,".") + 1) == $sort)
			return true;
	return false;
}

function getSelectValue($sort){
	if (strpos($sort,".")){
		return substr($sort, strpos($sort,".") + 1);
	}else{
		return $sort;
	}

}

?>
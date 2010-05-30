<?php

/**
 * Edit Posts modified for stop press
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');


if ( !current_user_can('edit_posts') )
	wp_die(__('Cheatin&#8217; uh?'));


if ( empty($title) )
	$title = __('Edit '.$this_table_obj->plugintitle);
	$parent_file = 'edit.php';



require_once('admin-header.php');

?>

<div class="wrap">
<?php //screen_icon(); ?>

<div id="<?php echo $this_table_obj->pluginname?>_icon32" class="icon32"><br/></div>
<h2><?php echo esc_html( $title );?></h2>


<script>
	window.jQuery = window.$ = jQuery;
	
	function doSubmit(){
		var rowID = "";
		$(".rowID").each(
			function (intIndex, ctrl){
			 if (ctrl.checked){
				rowID += ctrl.value + ",";
			 }
			}
		)
	$("#row_ID").val(rowID);
	}
	
	function updateFilter(ctrl){
			$("#filterfield").val(ctrl.name);
			$("#filterid").val(ctrl.options[ctrl.selectedIndex].value);
			$("#filterstatement").val(ctrl.name + " = '" + ctrl.options[ctrl.selectedIndex].value + "'");
			$("#jcl-posts-filter").submit();
	}

	function addNewRecord(){
			$("#myaction").val("add");
			$("#jcl-posts-filter").submit();
	}
	
	function updateOrder(ctrl, field){
			if (field == "disabled")
				return;
			if ($("#orderfield").val() == field){
				//toggle
				if ($("#orderdirection").val() == "desc"){
					$("#orderdirection").val("asc");
				}else{
					$("#orderdirection").val("desc");
				}
			}else{
				$("#orderfield").val(field);
				$("#orderdirection").val("asc");
			}
			$("#jcl-posts-filter").submit();
	}
	
	
</script>
<form id="jcl-posts-filter" class="edit_form_<?php echo $this_table_obj->pluginname?>" action="?page=<?php echo $this_table_obj->pluginprefix.$this_table_obj->pluginname."/".$this_table_obj->pluginname.".php"?>" method="post" onsubmit="javascript:doSubmit();">
	<input type="hidden" value="" id="row_ID" name="rowID"/>
	<input type="hidden" value="multidelete" id="myaction" name="myaction"/>
	<?php writeFilters($this_table_obj); ?>
	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action">
				<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
				<!--<option value="edit"><?php _e('Edit'); ?></option>-->
				<option value="delete"><?php _e('Delete'); ?></option>
			</select>
			<input type="submit" value="<?php esc_attr_e('Apply'); ?>" class="button-secondary action" />
		</div>
		<div class="alignright">
		<input class="button-primary" type="button" value="Add Record" onclick="javascript:addNewRecord();"/>
		</div>
		<div class="clear"></div>
	</div>

	<div class="clear"></div>

	<?php include( WP_PLUGIN_DIR.'/'.$this_table_obj->pluginprefix.$this_table_obj->pluginname.'/includes/edit-post-rows.php' ); ?>


	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action2">
				<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
				<!--<option value="edit"><?php _e('Edit'); ?></option>-->
				<option value="delete"><?php _e('Delete'); ?></option>
			</select>
			<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
		</div>
		<div class="alignright">
		<input class="button-primary" type="button" value="Add Record" onclick="javascript:addNewRecord();"/>
		</div>
		<br class="clear" />
	</div>
</form>

<div id="ajax-response"></div>

<br class="clear" />

</div>


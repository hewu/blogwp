<?php
/**
 * Edit posts rows table for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');
?>
<table id="rowtable" class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
<?php print_headers($this_table_obj); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php print_headers($this_table_obj); ?>
	</tr>
	</tfoot>

	<tbody>
<?php print_rows($this_table_obj); ?>
	</tbody>
</table>

<?php



/**
* {@internal Missing Short Description}}
*
1329   * @since unknown
1330   *
1331   * @param unknown_type $posts
1332   */
  function print_rows($this_table_obj ) {
        
      add_filter('the_title','esc_html');
    
      jcl_filter_row($this_table_obj);

	  $posts = $this_table_obj->get_select("rows");
      foreach ( $posts as $post ) {
          jcl_post_row($this_table_obj, $post);
      }
  }
  


function print_headers($this_table_obj) {
      $styles = array();
	  
?>
      <th scope="col" class="manage-column column-cb check-column"><input type="checkbox"/></th>
<?php
	  
	 $columns = $this_table_obj->get_rowdef();
  
      for ( $i = 1; $i <= sizeof($columns); $i++ ) {
	  
		$header = isset($columns[$i]["header"])==1 ? $columns[$i]["header"] : $columns[$i]["field"];
		$width = isset($columns[$i]["width"]) ? " width = '".$columns[$i]["width"]."'" : "";
		
		if (isset($this_table_obj->orderfield)){
			if (isset($columns[$i]["sort"]) && matchSort($this_table_obj,$columns[$i]["sort"])){
				if (isset ($this_table_obj->orderdirection) && $this_table_obj->orderdirection == "desc"){
					$sortclass = "u";
				}else{
					$sortclass = "d";
				}
			} elseif (isset($columns[$i]["field"]) && $this_table_obj->orderfield == $columns[$i]["field"]){
				if (isset ($this_table_obj->orderdirection) && $this_table_obj->orderdirection == "desc"){
					$sortclass = "u";
				}else{
					$sortclass = "d";
				}
			} else {
				$sortclass = "";
			}
		}else{
			$sortclass = "";
			
		}
		
		if (isset($columns[$i]["sort"])){
			$sortfield = $columns[$i]["sort"];
		}else{
			if (isset($columns[$i]["field"])){
				$sortfield = $columns[$i]["field"];
			}else{
				$sortfield = "disabled";
			}
		}
		
		if (isset($columns[$i]["textalign"])){
			$textalign = "text-align:".$columns[$i]["textalign"].";";
		}else{
			$textalign = "";
		}
		
		if (!isset($columns[$i]["display"]) || $columns[$i]["display"] != "none"){
			if (isset($columns[$i]["cell"])){
				if ($columns[$i]["cell"]=="start"){
?>
					<th onclick="updateOrder(this, '<?php echo $sortfield;?>')" style="<?php echo $textalign?>" scope="col" <?php echo $width?>><div><?php echo $header; ?></div>
<?php
				} else if ($columns[$i]["cell"]=="end"){
?>
					<div><?php echo $header; ?></div></th>
<?php				
				} else {
?>
					<div><?php echo $columns[$i]["header"]; ?></div>
<?php
				}
			}else{
?>
				<th onclick="updateOrder(this, '<?php echo $sortfield;?>')" style="<?php echo $textalign?>" scope="col" <?php echo $width?>><?php echo $header; ?><div class="<?php echo $sortclass ?>"></div></th>
<?php	
			}
		} 
	}
  }
    

function jcl_filter_row($this_table_obj) {

	global $wpdb;
	
	if ($this_table_obj->showfilterbar != "yes")
			return;
	
	$columns = $this_table_obj->get_rowdef();	
	echo "<tr><th></th>";
	for ( $i = 1; $i <= sizeof($columns); $i++ ) {

		if (isset($columns[$i]["extra"]) && $columns[$i]["extra"] == "AUTO_INCREMENT" )
			continue;	
?>
		<th>
		<?php 
		if (isset($columns[$i]["filter"])){
		
			if (!isset($columns[$i]["htmlelement"])){
				$columns[$i]["htmlelement"] = "";
			}
			
			switch ($columns[$i]["htmlelement"]){
				case "selectvalue":
							$query = "SELECT * FROM ".$wpdb->prefix.str_replace("_ID","",$columns[$i]["field"]);
							if (isset($columns[$i]["sort"])){
								$query .= " ORDER BY ".$columns[$i]["sort"];
							}	
							$options =  $wpdb->get_results($wpdb->prepare($query));

							echo "<select name='".$columns[$i]["field"]."' style='width:95%' onchange='javascript:updateFilter(this);'>";
							echo "<option value=''></option>";
							foreach ($options as $option){
							
								if ($columns[$i]["field"] == $this_table_obj->filterfield && $option->ID == (int)$this_table_obj->filterid){
								echo "<option value='".$option->ID."' selected='true'>".$option->$columns[$i]["selectfield"]."</option>";
								}else{
								echo "<option value='".$option->ID."'>".$option->$columns[$i]["selectfield"]."</option>";
								}
							}
							echo "</select>";
					break;
				case "no:yes":
					if ($post->$columns[$i]["field"] == "0"){
						echo "no";
					}else{
						echo "yes";
					}
					break;
				default:
				
			}
		
		}
		?></th>

<?php	
	}
	
	echo "</tr>";
}
	
/*
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $a_post
 * @param unknown_type $pending_comments
 * @param unknown_type $mode
 */
function jcl_post_row($this_table_obj, $post) {
	global $wpdb;
	static $rowclass;

	$rowclass = 'alternate' == $rowclass ? '' : 'alternate';
	global $current_user;?>
	
	<tr id='post-<?php echo $post->ID; ?>' class='<?php echo trim( $rowclass . ' author-' . $post_owner . ' status-' . $post->post_status ); ?> iedit' valign="top">

	<th scope="row" class="check-column"><input type="checkbox" class="rowID" value="<?php echo $post->ID; ?>" /></th>

	<?php
	$columns = $this_table_obj->get_rowdef();	

	for ( $i = 1; $i <= sizeof($columns); $i++ ) {

		if (isset($columns[$i]["extra"]) && $columns[$i]["extra"] == "AUTO_INCREMENT" )
			continue;
			

		if ((isset($columns[$i]["field"]) && $columns[$i]["field"] == "title") || (isset($columns[$i]["action"]) && $columns[$i]["action"]=="title")){
			$attributes = 'class="post-title column-title"';
		
		$title = isset($columns[$i]["field"]) ? $post->$columns[$i]["field"] : $columns[$i]["field"] = "";
		?>
		<td <?php echo $attributes ?>><strong><a class="row-title" href="<?php jcl_get_link($this_table_obj,$post->ID) ?>" title="<?php echo esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $title)); ?>"><?php echo $title ?></a></strong>
		<?php

			$actions = array();
			$actions['edit'] = '<a href="' . jcl_get_link($this_table_obj,$post->ID,"edit") . '" title="' . esc_attr(__('Edit this post')) . '">' . __('Edit') . '</a>';
			$actions['delete'] = "<a class='submitdelete' title='" . esc_attr(__('Delete this post')) . "' href='" . jcl_get_link($this_table_obj,$post->ID,"delete") . "' onclick=\"if ( confirm('" . esc_js(sprintf(__("You are about to delete this post '%s'\n 'Cancel' to stop, 'OK' to delete."), $columns[$i]["field"] )) . "') ) { return true;}return false;\">" . __('Delete') . "</a>";


			$actions = apply_filters('post_row_actions', $actions, $post);
			$action_count = count($actions);
			$j = 0;
			echo '<div class="row-actions">';
			foreach ( $actions as $action => $link ) {
				++$j;
				( $j == $action_count ) ? $sep = '' : $sep = ' | ';
				echo "<span class='$action'>$link$sep</span>";
			}
			echo '</div>';

		?>
		</td>
		<?php
		}else{
			
			if (isset($columns[$i]["textalign"])){
				$textalign = "text-align:".$columns[$i]["textalign"].";";
			}else{
				$textalign = "";
			}
			if (isset($columns[$i]["cell"])){
				if ($columns[$i]["cell"] ==  "start"){
					echo '<td style=\''.$textalign.'\'><div>';
				}else{
					echo '<div>';
				}
			}else{
				echo '<td style=\''.$textalign.'\'>';
			}
			
			if (!isset($columns[$i]["htmlelement"])){
				$columns[$i]["htmlelement"] = "";
			}
			
			switch ($columns[$i]["htmlelement"]){
				case "selectvalue":
					$selectfield = getSelectValue($columns[$i]["selectfield"]);
					if (isset ($post->$selectfield)){
						echo $post->$selectfield;
					}else{
						echo $post->$columns[$i]["field"];
					}
					break;
				case "no:yes":
					if ($post->$columns[$i]["field"] == "0"){
						echo "no";
					}else{
						echo "yes";
					}
					break;
				default:
				
					$output = str_replace("<br/>"," ",$post->$columns[$i]["field"]);
					$output = strip_tags($output);
					if (isset($columns[$i]["truncate"])){
						if (strlen($output) > $columns[$i]["truncate"]){
							$output = esc_html(substr($output,0,$columns[$i]["truncate"]))."...";
						}
					}else{
						$output = esc_html($output);
					}
					echo $output;
					
					if (isset($columns[$i]["cell"])){
						if ($columns[$i]["cell"] ==  "end"){
							echo '</div></td>';
						}else{
							echo '</div>';
						}
					}else{
						echo '</td>';
					}
			
			}
			
		}
			

}


?>
	</tr>
<?php
	//$post = $global_post;
}


function jcl_get_link($this_table_obj, $id, $action) {
	$action = "page=".$this_table_obj->pluginprefix.$this_table_obj->pluginname."/".$this_table_obj->pluginname.".php&myaction=".$action."&";
	return admin_url("admin.php?{$action}post=$id");
}



  
?>
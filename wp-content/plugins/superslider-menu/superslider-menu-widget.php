<?php

	function ss_menu_widget($args, $widget_args=1) {
        

	  
	  extract($args, EXTR_SKIP);
	  
	  if ( is_numeric($widget_args) )
	    $widget_args = array( 'number' => $widget_args );
	  $widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	  extract($widget_args, EXTR_SKIP);
	
	  $options = get_option('ssMenu_widget_options');
	  
	  
	  
	  
	  if ( !isset($options[$number]) )
	    return;
	
	  $title = ($options[$number]['title'] != "") ? $options[$number]['title'] : ""; 
	
	  if ( !empty( $title ) ){ echo $before_widget . $before_title . $title . $after_title;}
	       
	    if(method_exists('ssMenu','foldcats')) {

            ssMenu::$add_ssmenu_script = true;//switch on js loader
	        ssMenu::foldcats($number);
	        
	       } else {
	        echo "<ul>\n";
	        wp_list_cats('sort_column=name&optioncount=1&hierarchical=0');
	        echo "</ul>\n";
	       }	
	    echo $after_widget;
	  }
	

	function ss_menu_widget_init() {
		
		if ( !$options = get_option('ssMenu_widget_options') )
		    $options = array();
		  $control_ops = array('width' => 380, 'height' => 400, 'id_base' => 'ss_menu');
			$widget_ops = array('classname' => 'ss_menu', 'description' =>
		  __('Animated expanding / fold down category menu to show subcategories and posts'));
		  $name = __('SuperSlider Menu');
		  $id = false;
		  
		foreach ( array_keys($options) as $o ) {// Old widgets can have null values for some reason
			if ( !isset($options[$o]['title']) || !isset($options[$o]['title']) )
	      continue;
			$id = "ss_menu-$o"; // Never never never translate an id
			wp_register_sidebar_widget($id, $name, 'ss_menu_widget', $widget_ops, array( 'number' => $o ));
			wp_register_widget_control($id, $name, 'ss_menu_widgetControl', $control_ops, array( 'number' => $o ));
            }
		
		  // If there are none, we register the widget's existance with a generic template
		  if ( !$id ) {
		
		    wp_register_sidebar_widget('ss_menu-1', $name, 'ss_menu_widget', $widget_ops, array( 'number' => -1 ) );
		    wp_register_widget_control('ss_menu-1', $name, 'ss_menu_widgetControl', $control_ops, array( 'number' => -1 ) );
			
		  }

	}

	// Run our code later in case this loads prior to any required plugins.
	if (method_exists('ssMenu','foldcats')) {
		ss_menu_widget_init();
	} else {

		$fname = basename(__FILE__);
		$current = get_settings('active_plugins');
		array_splice($current, array_search($fname, $current), 1 ); // Array-fu!
		update_option('active_plugins', $current);
		do_action('deactivate_' . trim($fname));
		header('Location: ' . get_settings('siteurl') . '/wp-admin/plugins.php?deactivate=true');
		exit;
	}

	function ss_menu_widgetControl($widget_args) {
		
	  global $wp_registered_widgets;
	  static $updated = false;
	
	  if ( is_numeric($widget_args) )
	    $widget_args = array( 'number' => $widget_args );
	  $widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	  extract( $widget_args, EXTR_SKIP );
	
	  $options = get_option('ssMenu_widget_options');
	  if ( !is_array($options) )
	    $options = array();
	
	  if ( !$updated && !empty($_POST['sidebar']) ) {
	    $sidebar = (string) $_POST['sidebar'];
	
	    $sidebars_widgets = wp_get_sidebars_widgets();
	    if ( isset($sidebars_widgets[$sidebar]) )
	      $this_sidebar =& $sidebars_widgets[$sidebar];
	    else
	      $this_sidebar = array();
	
	    foreach ( $this_sidebar as $_widget_id ) {
	      if ( 'ss_menu_widget' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
	        $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
	        if ( !in_array( "ss_menu-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed.
	          unset($options[$widget_number]);
	      }
	    }
	
	    foreach ( (array) $_POST['ss_menu'] as $widget_number => $ss_menu ) {
	      if ( !isset($ss_menu['title']) && isset($options[$widget_number]) ) // user clicked cancel
	        continue;
	      $title = strip_tags(stripslashes($ss_menu['title']));
	      $catSortOrder= 'DESC' ;
	      if($ss_menu['catSortOrder'] == 'ASC') {
	        $catSortOrder= 'ASC' ;
	      }
            
          $useDescription= 'yes' ;
	      if($ss_menu['useDescription'] == 'no') {
	        $useDescription= 'no' ;
	      }
            
	      if( isset($ss_menu['moretext'] )) {
	        $moretext = $ss_menu['moretext'] ;
	      }
	      if( isset($ss_menu['tipText'] )) {
	        $tipText = $ss_menu['tipText'] ;
	      }
	      $linkToCat= 'yes' ;
	      if($ss_menu['linkToCat'] == 'no') {
	        $linkToCat= 'no' ;
	      }
	      $showPostCount= 'no' ;
	      if( isset($ss_menu['showPostCount'])) {
	        $showPostCount= 'yes' ;
	      }
	      $showMorePosts= 'no' ;
	      if( isset($ss_menu['showMorePosts'])) {
	        $showMorePosts= 'yes' ;
	      }
	      $showEmptyCat= 'no' ;
	      if( isset($ss_menu['showEmptyCat'])) {
	        $showEmptyCat= 'yes' ;
	      }
	      if( isset($ss_menu['limitPosts'])) {
	        $limitPosts = $ss_menu['limitPosts'] ;
	      }
	      if($ss_menu['catSort'] == 'catName') {
	        $catSort= 'catName' ;
	      } elseif ($ss_menu['catSort'] == 'catId') {
	        $catSort= 'catId' ;
	      } elseif ($ss_menu['catSort'] == 'catSlug') {
	        $catSort= 'catSlug' ;
	      } elseif ($ss_menu['catSort'] == 'catOrder') {
	        $catSort= 'catOrder' ;
	      } elseif ($ss_menu['catSort'] == 'catCount') {
	        $catSort= 'catCount' ;
	      } elseif ($ss_menu['catSort'] == '') {
	        $catSort= '' ;
	        $catSortOrder= '' ;
	      }
	      $postSortOrder= 'DESC' ;
	      if($ss_menu['postSortOrder'] == 'ASC') {
	        $postSortOrder= 'ASC' ;
	      }
	      if($ss_menu['postSort'] == 'postTitle') {
	        $postSort= 'postTitle' ;
	      } elseif ($ss_menu['postSort'] == 'postId') {
	        $postSort= 'postId' ;
	      } elseif ($ss_menu['postSort'] == 'postComment') {
	        $postSort= 'postComment' ;
	      } elseif ($ss_menu['postSort'] == 'postDate') {
	        $postSort= 'postDate' ;
	      } elseif ($ss_menu['postSort'] == '') {
	        $postSort= '' ;
	        $postSortOrder= '' ;
	      }
	     
	      $catfeed= $ss_menu['catfeed'];
	      $inExclude= 'include' ;
	      if($ss_menu['inExclude'] == 'exclude') {
	        $inExclude= 'exclude' ;
	      }
	     
	      $inExcludeCats=addslashes($ss_menu['inExcludeCats']);
	      
	      $options[$widget_number] = compact( 'title','showPostCount','catSort',
	          'catSortOrder','expand','inExclude', 
	          'inExcludeCats','postSort','postSortOrder','limitPosts', 
	          'catfeed', 'moretext', 'tipText', 'showMorePosts', 'showEmptyCat', 'useDescription' );
	    }
	
	    update_option('ssMenu_widget_options', $options);
	    $updated = true;
	  }
	
	 if ( -1 == $number ) {
	    /* default options go here */
	    $title = 'SuperSlider-Menu';
	    $showPostCount = 'yes';
	    $catSort = 'catName';
	    $catSortOrder = 'DESC';
	    $postSort = 'postDate';
	    $postSortOrder = 'DESC';
	    $number = '%i%';
	    $inExclude='include';
	    $inExcludeCats='';
	    $moretext='more from';
	    $tipText='View listing of all entries under ';
	    $showMorePosts='yes';
	    $limitPosts='5';
	    $useDescription= 'yes';
	    $catfeed='none';
	  } else {
	    $title = attribute_escape($options[$number]['title']);
	    $showPostCount = $options[$number]['showPostCount'];
	    $inExcludeCats = $options[$number]['inExcludeCats'];
	    $inExclude = $options[$number]['inExclude'];
	    $catSort = $options[$number]['catSort'];
	    $catSortOrder = $options[$number]['catSortOrder'];
	    $postSort = $options[$number]['postSort'];
	    $postSortOrder = $options[$number]['postSortOrder'];;
	    $moretext = $options[$number]['moretext'];
	    $tipText = $options[$number]['tipText'];
	    $showMorePosts = $options[$number]['showMorePosts'];
	    $showEmptyCat = $options[$number]['showEmptyCat'];
	    $limitPosts = $options[$number]['limitPosts'];
	    $useDescription = $options[$number]['useDescription'];
	    $catfeed = $options[$number]['catfeed'];
	  }

    // Here is our little form segment.

    echo '<p style="text-align:left;"><label for="ss_menu-title-'.$number.'">' . __('Title:') . '<input class="widefat" style="width: 200px;" id="ss_menu-title-'.$number.'" name="ss_menu['.$number.'][title]" type="text" value="'.$title.'" /></label></p>';
  ?>
	
	<p>
		<label for="ss_menu-showPostCount-<?php echo $number ?>"><input type="checkbox" name="ss_menu[<?php echo $number ?>][showPostCount]" 
	<?php if ($showPostCount =='yes')  echo 'checked'; ?> id="ss_menu-showPostCount-<?php echo $number ?>">
		</input> Show Post Count. </label>
	    <label for="ss_menu-showEmptyCat-<?php echo $number ?>"><input type="checkbox" name="ss_menu[<?php echo $number ?>][showEmptyCat]"
	<?php if ($showEmptyCat =='yes')  echo 'checked'; ?> id="ss_menu-showEmptyCat-<?php echo $number ?>">
		</input> Show Empty Categories.</label>
</p>

<p>
		<label for="ss_menulimitPosts-<?php echo $number ?>">Limit # of posts, 
		<input type="text" name="ss_menu[<?php echo $number; ?>][limitPosts]" value="<?php echo $limitPosts; ?>" id="ss_menulimitPosts-<?php echo $number ?>" size="3" maxlength="3">
	</input> to be shown, per category.</label>
</p>
<hr />
<p>
	<label for="ss_menu-showMorePosts-<?php echo $number ?>"><input type="checkbox" name="ss_menu[<?php echo $number ?>][showMorePosts]" 
	<?php if ($showMorePosts =='yes')  echo 'checked'; ?> id="ss_menu-showMorePosts-<?php echo $number ?>">
		</input> Add more link :</label>
	
	<label for="ss_menu-moretext-<?php echo $number ?>">text : 
	<input type="text" name="ss_menu[<?php echo $number ?>][moretext]" size="8" value="<?php echo $moretext ?>" id="ss_menu-moretext-<?php echo $number ?>">
	</input> Category name. </label>
</p>
<hr />
<p>
	<label for="ss_menu-useDescription-<?php echo $number ?>"><input type="checkbox" name="ss_menu[<?php echo $number ?>][useDescription]"
	<?php if ($useDescription =='yes')  echo 'checked'; ?> id="ss_menu-useDescription-<?php echo $number ?>">
		</input> Use category description for tooltips. </label>
    </p>
    <p>
	<label for="ss_menu-tipText-<?php echo $number ?>">Default tooltip text : <br />
	<input type="text" name="ss_menu[<?php echo $number ?>][tipText]" size="24" value="<?php echo $tipText ?>" id="ss_menu-tipText-<?php echo $number ?>">
	</input> Category name.</label><br /> <small>(Tooltip shows category description. This will show if there is no description)</small>
</p>
<hr />
<p>
		Sort Categories by:
		<br />
		<select name="ss_menu[<?php echo $number ?>][catSort]">
			<option 
	<?php if($catSort=='catName') echo 'selected'; ?>
			id="sortName" value='catName'> category name
			</option>
			<option 
	<?php if($catSort=='catId') echo 'selected'; ?>
			id="sortId" value='catId'> category id
			</option>
			<option 
	<?php if($catSort=='catSlug') echo 'selected'; ?>
			id="sortSlug" value='catSlug'> category Slug
			</option>
			<option 
	<?php if($catSort=='catOrder') echo 'selected'; ?>
			id="sortOrder" value='catOrder'> category (term) Order
			</option>
			<option 
	<?php if($catSort=='catCount') echo 'selected'; ?>
			id="sortCount" value='catCount'> category Count
			</option>
		</select>
		
		<label for="ss_menu-catSortASC-<?php echo $number ?>">
		<input type="radio" name="ss_menu[<?php echo $number ?>][catSortOrder]" <?php if($catSortOrder=='ASC') echo 'checked'; ?>
		id="ss_menu-catSortASC-<?php echo $number ?>" value='ASC'>
		</input> Ascending
		</label>
		
		<label for="ss_menu-catSortDESC-<?php echo $number ?>">
		<input type="radio" name="ss_menu[<?php echo $number ?>][catSortOrder]" <?php if($catSortOrder=='DESC') echo 'checked'; ?>
		id="ss_menu-catSortDESC-<?php echo $number ?>" value='DESC'>
		</input> Descending
		</label>
</p>
<p>
		Sort Posts by:
		<br />
		<select name="ss_menu[<?php echo $number ?>][postSort]">
			<option <?php if($postSort=='postTitle') echo 'selected'; ?>
			id="sortPostTitle-<?php echo $number ?>" value='postTitle'>Post Title
			</option>
			<option <?php if($postSort=='postId') echo 'selected'; ?>
			id="sortPostId-<?php echo $number ?>" value='postId'>Post id
			</option>
			<option <?php if($postSort=='postDate') echo 'selected'; ?>
			id="sortPostDate-<?php echo $number ?>" value='postDate'>Post Date
			</option>
			<option <?php if($postSort=='postComment') echo 'selected'; ?>
			id="sortComment-<?php echo $number ?>" value='postComment'>Post Comment Count
			</option>
		</select>
		
		<label for="postSortASC">
		<input type="radio" name="ss_menu[<?php echo $number ?>][postSortOrder]" <?php if($postSortOrder=='ASC') echo 'checked'; ?>
		id="postSortASC" value='ASC'>
		</input> Ascending
		</label>
		
		<label for="postPostDESC">
		<input type="radio" name="ss_menu[<?php echo $number ?>][postSortOrder]" <?php if($postSortOrder=='DESC') echo 'checked'; ?>
		id="postPostDESC" value='DESC'>
		</input> Descending
		</label>
</p>
<hr />
<p>
	<select name="ss_menu[<?php echo $number ?>][inExclude]">
		
		<option <?php if($inExclude=='include') echo 'selected'; ?>
		id="inExcludeInclude-<?php echo $number ?>" value='include'>Include
		</option>
		
		<option <?php if($inExclude=='exclude') echo 'selected'; ?>
		id="inExcludeExclude-<?php echo $number ?>" value='exclude'>Exclude
		</option>
	</select>
	
	<label for="ss_menu-inExcludeCats-<?php echo $number ?>">
	These categories (ID separated by commas):
	<input type="text" name="ss_menu[<?php echo $number ?>][inExcludeCats]" value="<?php echo $inExcludeCats ?>" id="ss_menu-inExcludeCats-<?php echo $number ?>">
	</input>
	</label>	
</p>
<hr />
<p>Include RSS link 
	<label for="ss_menu-catfeedNone-<?php echo $number ?>">
	<input type="radio" name="ss_menu[<?php echo $number ?>][catfeed]" 
        <?php if($catfeed=='none') echo 'checked'; ?>
	id="ss_menu-catfeedNone-<?php echo $number ?>" value='none'>
	</input>	
		None
	</label>
	<label for="ss_menu-catfeedText-<?php echo $number ?>">
	<input type="radio" name="ss_menu[<?php echo $number ?>][catfeed]" 
        <?php if($catfeed=='text') echo 'checked'; ?>
	id="ss_menu-catfeedText-<?php echo $number ?>" value='text'>
	</input>	
		text (RSS)
	</label>
	<label for="ss_menu-catfeedImage-<?php echo $number ?>">
	<input type="radio" name="ss_menu[<?php echo $number ?>][catfeed]" 
        <?php if($catfeed=='image') echo 'checked'; ?>
	id="ss_menu-catfeedImage-<?php echo $number ?>" value='image'>
	</input>	
		image 
		<img src='../wp-includes/images/rss.png' />
	</label>
</p>

<?php
    echo '<input type="hidden" id="ss_menu-submit-'.$number.'" name="ss_menu['.$number.'][submit]" value="1" />';
	}
?>

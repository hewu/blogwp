<?php
/*
SuperSlider-Menu
Copyright 2010 Daiv Mowbray
This file is part of SuperSlider-Menu
*/

function add_feedlink($catfeed,$cat) {
//echo 'the cat feed is : '.$catfeed.' .<br />';	
    $rssLink = "\n";
    if ($catfeed == 'text') {			

        $rssLink .= '<a class="menuRss" href="' . get_category_feed_link($cat->term_id) .'">(RSS)</a>';
                
    } elseif ($catfeed =='image') {	
        
        $options = get_option('ssMenu_options');
		
		if ( class_exists('ssBase')) { 
		  $baseoptions = get_option('ssBase_options');
		  $ss_global_over_ride = $baseoptions[ss_global_over_ride];
		  if ( $ss_global_over_ride == 'on') {
		   $options[css_load] = $baseoptions[css_load];
		  }
		}  
      
        if ($options[css_load] == 'default'){
            $rssPath = WP_PLUGIN_URL.'/superslider-menu/plugin-data/superslider/ssMenu/rss/rss_out.png';
            
        }elseif ($options[css_load] == 'pluginData') {
            $rssPath = WP_CONTENT_URL.'/plugin-data/superslider/ssMenu/rss/rss_out.png';            
        
        }elseif ($options[css_load] == 'off') {
            $rssPath = get_option( 'siteurl' ).'/wp-includes/images/rss.png';            
        }
        
      $url = get_settings(siteurl) ;
      $rssLink .= '<a class="menuRss" href="' . get_category_feed_link($cat->term_id) .'"><img src="'.$rssPath.'" alt="rss" /></a>';
            
    } else {		
        $rssLink = '';
    }
    $rssLink .= "\n";
    return $rssLink;
}

function get_sub_cat($cat, $categories, $posts, $subCatCount, $subCatPostCount, $number, $grandParents, $parents, $children, $grandChildren) { 
      
      global $options;
      extract($options[$number]);
	 
	 $link2 = '';
	  $moreposts = 0;
	  	
		foreach ($categories as $cat2) {
		  
		  $subCatLink2=''; // clear info from subCatLink2
		  		  
		  if ( $cat->term_id == $cat2->parent ) {
			// check to see if there are more subcategories under this one

			$subCatPostCount = $subCatPostCount + $cat2->count;
            $mylevel = '';
            if ( in_array($cat2->term_id, $parents) ) { $mylevel = 2; }
            if ( in_array($cat2->term_id, $children) ) { $mylevel = 3; }
            if ( in_array($cat2->term_id, $grandChildren) ) { $mylevel = 4; }                   

               if (!in_array($cat2->term_id, $parents)) {

                    $subCatLinks .= "<div class='ssmToggleBar ssm_".$cat2->slug."' ><span class='subsym show_".$mylevel."'>&nbsp;</span>" ;
                    $link2 = make_cat_link($cat2, $tipText, $useDescription);
                    $subCatCount = 0;
                    
               } else {                  
                   
                    $subCatCount = 1; // this means that the subcat has a child                    
   
                    list ($subCatLink2, $subCatCount,$subCatPostCount,$subCatPosts)= 
                            get_sub_cat($cat2, $categories, $posts, $subCatCount, $subCatPostCount, $number, $grandParents, $parents, $children, $grandChildren);                 
                                      //$cat, $categories, $posts, $subCatCount, $subCatPostCount, $number, $grandParents, $parents, $children, $grandChildren
                  
                    $subCatLinks .= "\n<div class='ssmToggleBar ssm_".$cat2->slug."' >"."<span class='subsym show_".$mylevel."'>&nbsp;</span>" ;
                    $link2 = make_cat_link($cat2, $tipText, $useDescription);
                      
                }
                
                if( $showPostCount == 'yes') {

                    $theCount = $subCatPostCount2 + $cat2->count;
                    $link2 .= ' <span class="postCount">('.$theCount.')</span> ';
                      
                } 
                  
                $link2 .= add_feedlink($catfeed,$cat2)."</div>\n";
        
                $subCatLinks.= $link2 ;
                $subCatLinks.="\n<div class='showme_".$mylevel."' >";
                
                $subCatPosts = array();
                $myposts = array();

                foreach ($posts as $post) {
        
                  if ($post->term_id == $cat2->term_id ) {
        
                    array_push($subCatPosts, $post->id);
                    
                    array_push ($myposts, $post);
                    
                  }        
                }
                
                $postsincat = count($myposts) ;
                if ( $postsincat > $limitPosts) {

                    $moreposts = 1;
                    $myposts = array_slice($myposts, 0, $limitPosts);

		        } else { 
		            
		            $moreposts = 0; 
		        
		        }
		        foreach ($myposts as $post) {

		         $subCatLinks.= "<span class='ssMenuPost'><a href='".get_permalink($post->id)."'>" .  strip_tags($post->post_title) . "</a></span>\n";

		        }
        // add in more link 
        if ( ($moreposts == 1) && ($showMorePosts == 'yes') && ($postsincat > $limitPosts) )  $subCatLinks .= '<span class="ssMenuPost ssMore"><a href="'.get_category_link($cat2->term_id).'">'.$moretext.' '.$cat2->name.'</a></span>';
        
        $subCatLinks .= "$subCatLink2";
		
		$subCatLinks.= "</div><!-- ending subcategory -->\n";
        
      }
    }

  return array($subCatLinks,$subCatCount,$subCatPostCount,$subCatPosts);
}

function make_cat_link($cat, $tipText, $useDescription) {
   
    $link = "<a href='".get_category_link($cat->term_id)."' class='tool catLink' ";

    if ( $useDescription == 'yes' && !empty($cat->description) ) {
            $link .= 'title="' . wp_specialchars(apply_filters('description',$cat->description,$cat)) . '"';
        
    } else {
            $link .= 'title="'.$tipText.' '. $cat->name . '"';    
    }
	
    $link .= '>';   
    $link .= apply_filters('list_cats', $cat->name, $cat).'</a>';
    
    return $link;
}

function ssm_list_categories($number) {

	   global $wpdb,$options;
		
		$options = get_option('ssMenu_widget_options');
		extract($options[$number]);

		// check for exclusion or inclusion of categories
		$inExclusions = array();
//echo 'the inExclude : '.$inExclude.'.<br />';
//echo 'the inExcludeCats : '.$inExcludeCats.'.<br />';
        if ( !empty($inExclude) && !empty($inExcludeCats) ) {
           $exterms = preg_split('/[,]+/',$inExcludeCats);
            if ($inExclude == 'include') {
                $in='IN';
            } else {
                $in='NOT IN';
            }
            if ( count($exterms) ){
                
                foreach ( $exterms as $exterm ) {
                    if (empty($inExclusions))
                        $inExclusions = "'" . sanitize_title($exterm) . "'";
                    else
                        $inExclusions .= ", '" . sanitize_title($exterm) . "' ";
                }
            }
        }
//echo 'the inExclusions : '.$inExclusions.'.<br />';

        $isPage='';
        
        if (!$showPages) {
            $isPage="AND $wpdb->posts.post_type='post'";
        }
        if ( empty($inExclusions) ) {
            $inExcludeQuery = "''";					
        } else {				
            $inExcludeQuery ="AND $wpdb->terms.term_id $in ($inExclusions)";
        }

        if ($catSort=='catName') {
        $catSortColumn="ORDER BY $wpdb->terms.name";
        
        } elseif ($catSort=='catId') {
          $catSortColumn="ORDER BY $wpdb->terms.term_id";
          
        } elseif ($catSort=='catSlug') {
          $catSortColumn="ORDER BY $wpdb->terms.slug";
          
        } elseif ($catSort=='catOrder') {
          $catSortColumn="ORDER BY $wpdb->terms.term_order";
          
        } elseif ($catSort=='catCount') {
          $catSortColumn="ORDER BY $wpdb->term_taxonomy.count";
        }


        if ($postSort=='postDate') {
          $postSortColumn="ORDER BY $wpdb->posts.post_date";
        
        } elseif ($postSort=='postId') {
          $postSortColumn="ORDER BY $wpdb->posts.id";
          
        } elseif ($postSort=='postTitle') {
          $postSortColumn="ORDER BY $wpdb->posts.post_title";
          
        } elseif ($postSort=='postComment') {
          $postSortColumn="ORDER BY $wpdb->posts.comment_count";
        }


		$catquery = "SELECT $wpdb->term_taxonomy.count as 'count',
			$wpdb->terms.term_id, $wpdb->terms.name, $wpdb->terms.slug,
			$wpdb->term_taxonomy.parent, $wpdb->term_taxonomy.description FROM
			$wpdb->terms, $wpdb->term_taxonomy WHERE $wpdb->terms.term_id =
			$wpdb->term_taxonomy.term_id AND $wpdb->terms.name != 'Blogroll' AND
			$wpdb->term_taxonomy.taxonomy = 'category' $inExcludeQuery $catSortColumn
			$catSortOrder";
		$postquery = "SELECT $wpdb->terms.term_id, $wpdb->terms.name,
			$wpdb->terms.slug, $wpdb->term_taxonomy.count, $wpdb->posts.id,
			$wpdb->posts.post_title, $wpdb->posts.post_name,
			date($wpdb->posts.post_date) as 'date' FROM $wpdb->posts, $wpdb->terms,
			$wpdb->term_taxonomy, $wpdb->term_relationships  WHERE $wpdb->posts.id =
			$wpdb->term_relationships.object_id AND $wpdb->posts.post_status='publish'
			AND $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id AND
			$wpdb->term_relationships.term_taxonomy_id =
			$wpdb->term_taxonomy.term_taxonomy_id AND $wpdb->term_taxonomy.taxonomy =
			'category' $isPage $postSortColumn $postSortOrder";
				/* changing to use only one query 
				 * don't forget to exclude pages if so desired
				 */
			  
      $categories = $wpdb->get_results($catquery);			  
      $posts= $wpdb->get_results($postquery); 

//echo 'the catquery is :<br />'.$catquery.'.<br />';

/*
echo 'the categories are :<br />';
var_dump($categories);
echo '___________________.<br />';
*/
      $grandChildren = array();
      $children = array();
      $parents = array();
      $grandParents = array();
	  $subCatPosts = array();
	        
	   $mycats = $categories;
	  // let's remove any empty categories if showEmptyCat is set to no
	  foreach( $categories as $cat ) {
      
        if( $cat->count == 0 && $showEmptyCat == 'no') {
        
            $key = array_search($cat, $categories);
            unset($categories[$key]);
        }     
       if ( $cat->parent == '0') {
            
            array_push( $grandParents, $cat->term_id ); 
            $key = array_search($cat, $categories);
            unset($categories[$key]);
        } 


        if ( $cat->parent != '0'   ) { 

          array_push( $parents, $cat->term_id);

        }
      }
      
      foreach( $categories as $cat ) {
        
        if ( in_array($cat->parent, $parents)) {
        array_push( $children, $cat->term_id);
        
        }
      }
      
      foreach( $categories as $cat ) {
        
        if ( in_array($cat->parent, $children) ) {
        array_push( $grandChildren, $cat->term_id);

        }
      }
$categories = $mycats;
     
      // knock total number of posts to work with down to a max of 1000
      $totalPostCount = count( $posts );
  
      if ( $totalPostCount > 1000 ) {
      
        $posts = array_slice($posts, 0, 1000);
      
      }
      
      // start the html structure
	  echo '<div id="ssMenuHolder"><div id="ssMenuList">';

      foreach( $categories as $cat ) {
        
        if ($cat->parent == 0) {
        
			$subCatPostCount = 0;
			$subCatCount = 0; 
			$myposts = array(); 
			$subCatPosts = array(); 
 
            // get the sub cats and their posts
            list ($subCatLinks, $subCatCount,$subCatPostCount, $subCatPosts)=
			 get_sub_cat($cat, $categories, $posts, $subCatCount, $subCatPostCount, $number, $grandParents, $parents, $children, $grandChildren);
                        //$cat, $categories, $posts, $subCatCount, $subCatPostCount, $number, $grandParents, $parents, $children, $grandChildren

			$theCount = $cat->count + $subCatPostCount;


            print( "\n<div class='ssmToggleBar ssm_".$cat->slug."'><span class='subsym show_1'>&nbsp;</span>" );
            
            $link = make_cat_link($cat, $tipText, $useDescription);
            
			if( $showPostCount == 'yes') $link .= ' <span class="postCount">('. $theCount .')</span>';			
			
			$link .= add_feedlink($catfeed,$cat).'</div>';
			
		    print( $link );        
			print( '<div class="showme_1 ssm_'.$cat->slug.'"><div class="linkList">' );
  

           // Now print out the post info
            if (!is_array($subCatPosts)) $subCatPosts = array();
           
            foreach ($posts as $post) {
                                 
              if (($post->term_id == $cat->term_id) && (!in_array($post->id, $subCatPosts))  ) {
                
                array_push ($myposts, $post);
              }
            }
            $postsincat = count($myposts) ;
            if ( $postsincat > $limitPosts) $moreposts = 1;

            $myposts = array_slice($myposts, 0, $limitPosts);
           
           foreach ($myposts as $post) {

              echo " <span class='ssMenuPost'><a href='".get_permalink($post->id)."'>".strip_tags($post->post_title) . "</a></span>\n";

            }
   
            if ( ($moreposts == 1) && ($showMorePosts == 'yes') && ($postsincat > $limitPosts) )  echo '<span class="ssMenuPost ssMore"><a href="'.get_category_link($cat->term_id).'">'.$moretext.' '.$cat->name.'</a></span>';
             
            // list the sub cats and their posts
            echo $subCatLinks;
            
            if( $subCatCount = 1) echo "</div><!-- ending second level with sub cats -->\n ";
            
            echo "</div><!-- ending first level category -->\n";        

      } // end if theCount>0
   
   }
      echo "\n</div></div><!-- closeing the superslider-menu plugin -->\n";
 }
?>
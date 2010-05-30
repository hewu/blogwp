<?php
/*
Plugin Name: WP YUI Menu
Plugin URI: http://johndturner.com/wordpress-stuff/plugins/wordpress-yui-menu/
Version: 1.1
Description: Integrates a Horizontal YUI Menu in Wordpress.<a href="http://developer.yahoo.com/yui/menu/">Styling Info</a>
Author: John Turner
Author URI: http://johndturner.com

*/
?>
<?php
if(!function_exists('yui_menu_wp_head')){
	function yui_menu_wp_head() { 
	?>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.6.0/build/menu/assets/skins/sam/menu.css" /> 
	<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.6.0/build/yahoo-dom-event/yahoo-dom-event.js&2.6.0/build/container/container_core-min.js&2.6.0/build/menu/menu-min.js"></script>
	<script type="text/javascript">
	   YAHOO.util.Event.onContentReady("navmenu", function () {
	    var oMenuBar = new YAHOO.widget.MenuBar("navmenu", { 
	                                                    autosubmenudisplay: true, 
	                                                    hidedelay: 750, 
	                                                    lazyload: true });
	        oMenuBar.render();
	    });
	</script>
	<?php	
	}
}

add_action('wp_head', 'yui_menu_wp_head');

if(!function_exists('yui_wp_list_pages')){
	function yui_wp_list_pages($args = '') {
		$defaults = array(
			'depth' => 0, 'show_date' => '',
			'date_format' => get_option('date_format'),
			'child_of' => 0, 'exclude' => '',
			'title_li' => __('Pages'), 'echo' => 1,
			'authors' => '', 'sort_column' => 'menu_order, post_title',
			'link_before' => '', 'link_after' => ''
		);
	
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
	
		$output = '<div id="navmenu" class="yui-skin-sam yuimenubar yuimenubarnav"><div class="bd"><ul class="first-of-type">';
		$current_page = 0;
	
		// sanitize, mostly to keep spaces out
		$r['exclude'] = preg_replace('[^0-9,]', '', $r['exclude']);
	
		// Allow plugins to filter an array of excluded pages
		$r['exclude'] = implode(',', apply_filters('wp_list_pages_excludes', explode(',', $r['exclude'])));
	
		// Query pages.
		$r['hierarchical'] = 0;
		$pages = get_pages($r);
	
		if ( !empty($pages) ) {
			if ( $r['title_li'] )
				$output .= '<li class="pagenav yuimenuitem">' . $r['title_li'] . ' <div class="yuimenu"><div class="bd"><ul>';
	
			global $wp_query;
			if ( is_page() || $wp_query->is_posts_page )
				$current_page = $wp_query->get_queried_object_id();
			$output .= yui_walk_page_tree($pages, $r['depth'], $current_page, $r);
			if ( $r['title_li'] )
				$output .= '</ul></div></div></li>';
		}
	    $output .= '</ul></div></div>';
		$output = apply_filters('wp_list_pages', $output);
	
		if ( $r['echo'] )
			echo $output ;
		else
			return $output;
	}
}

if(!function_exists('yui_walk_page_tree')){
	function yui_walk_page_tree($pages, $depth, $current_page, $r) {
		$walker = new yui_Walker_Page;
		$args = array($pages, $depth, $r, $current_page);
		return call_user_func_array(array(&$walker, 'walk'), $args);
	}
}

if(!class_exists('yui_Walker_Page')){
	class yui_Walker_Page extends Walker {
	
		var $tree_type = 'page';
	
	
		var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');
	
		function start_lvl(&$output, $depth) {
			$indent = str_repeat("\t", $depth);
			$output .= "\n$indent<div class=\"yuimenu\"><div class=\"bd\"><ul class=\"first-of-type\">\n";
		}
	
	
		function end_lvl(&$output, $depth) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul></div></div>\n";
		}
	
	
		function start_el(&$output, $page, $depth, $args, $current_page) {
			if ( $depth )
				$indent = str_repeat("\t", $depth);
			else
				$indent = '';
	
			extract($args, EXTR_SKIP);
			$css_class = 'page_item page-item-'.$page->ID;
			if ( !empty($current_page) ) {
				$_current_page = get_page( $current_page );
				if ( isset($_current_page->ancestors) && in_array($page->ID, (array) $_current_page->ancestors) )
					$css_class .= ' current_page_ancestor';
				if ( $page->ID == $current_page )
					$css_class .= ' current_page_item';
				elseif ( $_current_page && $page->ID == $_current_page->post_parent )
					$css_class .= ' current_page_parent';
			} elseif ( $page->ID == get_option('page_for_posts') ) {
				$css_class .= ' current_page_parent';
			}
	
			$output .= $indent . '<li class="yuimenuitem ' . $css_class . '"><a class="yuimenuitemlabel" href="' . get_page_link($page->ID) . '" title="' . attribute_escape(apply_filters('the_title', $page->post_title)) . '">' . $link_before . apply_filters('the_title', $page->post_title) . $link_after . '</a>';
	
			if ( !empty($show_date) ) {
				if ( 'modified' == $show_date )
					$time = $page->post_modified;
				else
					$time = $page->post_date;
	
				$output .= " " . mysql2date($date_format, $time);
			}
		}
	
		function end_el(&$output, $page, $depth) {
			$output .= "</li>\n";
		}
	
	}
}

?>

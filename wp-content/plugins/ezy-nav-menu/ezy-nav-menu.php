<?php
/*
   Plugin Name: EZY Nav Menu
   Plugin URI: http://sww.co.nz/wordpress-plugins/ezy-nav-menu
   Description: Adds nav menu template tag for creating nav menus using WPs built-in links management
   Author: Aidan Curran
   Version: 0.4
   Author URI: http://sww.co.nz/
 */

   /*
   show_nav() - default shows nav category, div id is 'nav'
   show_nav('nav2', 'nav2style') - shows nav2 category links, div id is 'nav2', class is 'nav2style'
   */
   
   function show_nav($nav_id='nav', $css_class='nav', $before_html='', $after_html='', $seperator='', $display_as_list=false) {
      if ($display_as_list) {
         $containertag = "ul";
         $openlist = "<li>";
         $closelist = "</li>";
      } else {
         $containertag = "div";
         $openlist = "";
         $closelist = "";
      }
      $useseperator = $seperator;
      echo '<'.$containertag.' id="'.$nav_id.'" class="'.$css_class.'">';
      $navlinks =  get_bookmarks('orderby=rating&category_name='.$nav_id);
      $numOfItems = count($navlinks);
      $counter = 0;
      foreach ((array) $navlinks as $navlink ) {
         $counter += 1;
         if ($counter == $numOfItems) $useseperator = "";
         if ( !empty($navlink->link_url)) $link_url = $navlink->link_url; else $link_url = "";
         if ( !empty($navlink->link_name)) $link_name = $navlink->link_name; else $link_name = "";
         if ( !empty($navlink->link_description)) $link_description = ' title="' . $navlink->link_description . '"'; else $link_description = "";
         if ( !empty($navlink->link_target)) $link_target = ' target="' . $navlink->link_target . '"'; else $link_target = "";
         $markCurrent = '';
         $rootpath_ar = explode("/", $_SERVER['REQUEST_URI']);
         $rootpath = "/" . $rootpath_ar[1] . "/";
         if ($link_url == $_SERVER['REQUEST_URI'] || $link_url . "/" == $_SERVER['REQUEST_URI'] || $link_url == $_SERVER['REQUEST_URI'] . "/" || $link_url == $rootpath) {
            $markCurrent = ' class="current"';
         }
         echo $openlist . '<a href="' . $link_url . '"' . $link_description . $link_target . $markCurrent . '>' . $before_html . $link_name . $after_html . "</a>" . $closelist . $useseperator . "\n";
      }
      echo "</$containertag>\n";
   }
?>

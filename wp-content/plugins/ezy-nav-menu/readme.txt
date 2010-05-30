=== EZY Nav Menu ===
Version 0.4
Contributors: acurran
Donate link: http://sww.co.nz/payments/
Tags: template tags, navigation, menu, nav menu, CMS, CMS enabling plugin, navigation links
Requires at least: 2.1
Tested up to: 2.8.4
Stable tag: 0.4

Makes use of WP's built in 'Edit Links' to create and manage a website navigation menu that can be displayed using a custom template tag.

== Description ==

As well as being the leading blogging platform, WordPress is also one of the most flexible and easy to use general purpose content management systems for a wide variety of websites. However, one important CMS feature that is missing in the 'out of the box' installation of WordPress is a navigation menu management interface. A number of attempts have been made at plugins to address this need but most have been overly complex or cumbersome in some way so I have always coded my own navigation systems into my website templates. 

After experimenting with a few different approaches for building navigation menus for a number of different websites, I discovered that WordPress actually does have an interface for managing navigation menus - well 'sorta'... I found that one good approach to creating navigation menus that could be easily managed by my clients is to use WordPress's built in links management interface. All it takes is a few lines of PHP code in the header template file to utilise the links navagation interface for the purpose of managing the website navigation menu. I have used this approach for building single level and multiple level navigation menus. 

Now I have turned this approach to navigation menus into a simple plugin. This initial version adds the template tag 'show_nav()' which generates a single level navigation menu from the links contained in a 'nav' category. It is intended for use by users who create or edit their own templates. A multi-level version may follow.

== Change Log ==

Version 0.4 (21 Aug 2009)
>  Updated so that class='current' gets added to nav link when on a child page as well as when on the page itself

Version 0.3 (13 Jul 2009)
>  Added option for seperator

Version 0.2 (23 Apr 2009)
>  Added before and after HTML snippet capability and the option to display menu as unordered list

Version 0.1  
>  Initial release - single level navigation only

== Installation ==

1. Upload the file `ezy-nav-menu.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

If updating plugin please deactivate plugin and reactivate after uploading.

Usage:

* Create a links category called 'nav' and add all the links for the navigation menu to it. Use the rating to control the order (lower value appears first)
* In your theme template add the template tag 'show_nav()' at the point where you want the menu to appear (usually in the header template). This will generate the HTML output:

	    <div id="nav" class="nav">
	       <a href="{link web address}" 
	          [title="{link description (if specified)}"] 
	          [target="{link target (if specified)}"] 
	          [class="current"(when link matches current page)]>[Link name]</a>
	       ...
	       ...
	    </div>
Note: for the `class="current"` to get added to link for current page, you need to have permalinks set so it is not using the default query string type links and you need to enter relative paths starting with / in your links (i.e. drop the http://mysitename.com part).
* Style the navigation menu in any way you want in the CSS using the style hooks provided (i.e. div#nav, div#nav a, div#nav a.current, div#nav a:hover, etc.). 

Additional Usage Options:

* show_nav() takes 6 optional parameters: 
       
	    show_nav($nav_id, $css_class, $before_html, $after_html, $seperator, $display_as_list)

	    $nav_id - change the id attribute on the outer container (default - 'nav')
	    $css_class - change the class attribute on the outer container (default - 'nav')
	    $before_html - add a HTML snippet in front of the link text
	    $after_html - add a HTML snippet after the link text
	    $seperator - seperator string placed between links 
	    $display_as_list - boolean value; if set to true, outputs nav menu as unordered list (default - false)

       e.g. the following line,
       
	    show_nav("nav", "nav", "<div>", "</div>", true);
       
       would generate output such as the following:
       
	    <ul id="nav" class="nav">
	       <li><a href="link_url1"><div>Link Text 1</div></a></li>
	       <li><a href="link_url2" class="current"><div>Link Text 2</div></a></li>
	       ...
	       ...
	    </ul>       
       
== Frequently Asked Questions ==
= Is this plugin for me? =
If you create or modify your template (theme) files and know a little CSS - Yes. If you've never opened a template file - maybe not this version, but soon I may do a version suitable for 'non-template' editing people too. 


== Screenshots ==
1. No new interface!
 Just uses WP's existing
'Edit Links' interface. 
Simple!


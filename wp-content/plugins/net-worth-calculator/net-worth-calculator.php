<?php
/**
 * @package Personal_Wealth
 * @author Credit Card Finder Pty Ltd
 * @version 1.5.3
 */
/*
Plugin Name: Net Worth Calculator
Plugin URI: http://www.creditcardfinder.com.au/net-worth
Description: Plugin to track and display your assets, liabilities, and net worth.
Author: Credit Card Finder Pty Ltd
Version: 1.5.3
Author URI: http://www.creditcardfinder.com.au/
*/

include('widget.php');
include('shortcode.php');
include('pw_install.php');

if (!class_exists("NetWorthCalculator")) {
	class NetWorthCalculator {
		function NetWorthCalculator() { # constructor
			
		}
		
		function css() {
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/css/ui.jqgrid.css" />'."\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/css/redmond/jquery-ui-1.7.1.custom.css" />'."\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/css/personal-wealth.css" />'."\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/css/colorpicker/colorpicker.css" />'."\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/css/jquery.autocomplete.css" />'."\n";
		}
		
		function js() {
			wp_enqueue_script('jqgrid-locale', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/src/i18n/grid.locale-en.js', array('jquery'));
			wp_enqueue_script('jqgrid-base', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/src/grid.base.js', array('jquery'));
			wp_enqueue_script('jqgrid-celledit', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/src/grid.celledit.js', array('jquery'));
			wp_enqueue_script('jqgrid-common', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/src/grid.common.js', array('jquery'));
			wp_enqueue_script('jqgrid-formedit', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/src/grid.formedit.js', array('jquery'));
			wp_enqueue_script('jqgrid-celledit', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/src/grid.celledit.js', array('jquery'));
			wp_enqueue_script('jqgrid-fmatter', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/src/jquery.fmatter.js', array('jquery'));
			wp_enqueue_script('jqgrid-inlinedit', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/src/grid.inlinedit.js', array('jquery'));
			wp_enqueue_script('jqgrid-tablednd', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/jquery.tablednd_0_5.js', array('jquery'));
			
			wp_enqueue_script('colorpicker-base', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/colorpicker/colorpicker.js', array('jquery'));
			wp_enqueue_script('colorpicker-eye', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/colorpicker/eye.js', array('jquery'));
			wp_enqueue_script('colorpicker-utils', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/colorpicker/utils.js', array('jquery'));
			
			wp_enqueue_script('rgbcolor-base', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/rgbcolor.js', array('jquery'));
			wp_enqueue_script('jquery-numeric', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/jquery.numeric.pack.js', array('jquery', 'jquery-ui-core'));
			wp_enqueue_script('jquery-form', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/jquery.form.js', array('jquery'));
			wp_enqueue_script('jquery-effects-core', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/effects/effects.core.js', array('jquery'));
			wp_enqueue_script('jquery-effects-highlight', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/effects/effects.highlight.js', array('jquery', 'jquery-effects-core'));
			wp_enqueue_script('jquery-ui-accordion', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/src/ui.accordion.js', array('jquery', 'jquery-effects-core'));
			
			wp_enqueue_script('jquery-autocomplete', get_bloginfo('wpurl') . '/wp-content/plugins/net-worth-calculator/js/jquery.autocomplete.pack.js', array('jquery'));
		}
		
		function widget() {
			register_widget('NetWorthCalculator_Widget');
		}
		
		function manage_data() {
			include('manage-data.php');
		}
		
		function admin() {
			// include('admin-settings.php');
		}
		
		function appearance() {
			include('appearance.php');
		}
		
		function admin_actions() {
			add_menu_page('Manage Data < Net Worth Calculator', 'Net Worth Calculator', 2, 'personal-wealth', array($this, 'manage_data'));
			#add_submenu_page('personal-wealth', 'General Settings < Net Worth Calculator', 'General', 10, 'personal-wealth-general', array($this, 'admin'));
			add_submenu_page('personal-wealth', 'Appearance < Net Worth Calculator', 'Appearance', 2, 'personal-wealth-settings', array($this, 'appearance'));
		}
		
		function shortcode($atts) {
			global $post;
			extract(shortcode_atts(array(
				'month' => '',
				'user' => '',
				'view' => 'data',
				'width' => '300',
				'height' => '300',
				'num_of_months' => '6'
			), $atts));
			
			if($month == '') {
				$date = getdate(strtotime($post->post_date));
				$month = $date{'mon'}.'-'.$date{'year'};
			}
			
			if($user == '') {
				$user_info = get_userdata($post->post_author);
				$user = $user_info->user_login;
			}
			
			$shortcode = new NetWorthCalculator_Shortcode($month, $user, $view, $width, $height, $num_of_months);
			if($view == 'data') {
				return $shortcode->view_data();
			} else if($view == 'graph') {
				return $shortcode->view_graph();
			}
		}
	}
} # End Class NetWorthCalculator

if (class_exists("NetWorthCalculator")) {
	$NetWorthCalculator = new NetWorthCalculator();
}

# Actions and Filters	
if (isset($NetWorthCalculator)) {
	# Actions
	add_action('init', array(&$NetWorthCalculator, 'js'));
	add_action('wp_head', array(&$NetWorthCalculator, 'css'));
	add_action('admin_head', array(&$NetWorthCalculator, 'css'));
	add_action('admin_menu', array(&$NetWorthCalculator, 'admin_actions'));
	add_action('widgets_init', array(&$NetWorthCalculator, 'widget'));
	# Hooks
	register_activation_hook(__FILE__, 'ccf_personal_wealth_activate');
	# Filters
	
	# Shortcodes
	add_shortcode('networth', array(&$NetWorthCalculator, 'shortcode'));
	
}

?>

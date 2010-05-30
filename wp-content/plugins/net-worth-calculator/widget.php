<?php
/*  Copyright 2010 Credit Card Finder Pty Ltd  (email: info@creditcardfinder.com.au)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if (!class_exists("NetWorthCalculator_Widget")) {
	class NetWorthCalculator_Widget extends WP_Widget {
		function NetWorthCalculator_Widget() {
			# Widget settings
			$widget_ops = array('classname' => 'net-worth-calculator', 'description' => 'Net Worth Calculator Widget');
			
			# Widget control settings
			$control_ops = array('width' => 20, 'height' => 350, 'id_base' => 'net-worth-calculator-widget');
			
			# Create the widget
			$this->WP_Widget('net-worth-calculator-widget', 'Net Worth Calculator Widget', $widget_ops, $control_ops);
		}
		
		function widget($args, $instance) {
			extract($args);
			global $current_user;
			get_currentuserinfo();
			
			# Before widget (defined by themes)
			echo $before_widget;
			
			# Display graph from widget settings
			if($current_user->ID > 0) {
				echo $this->display_graph($instance);
			}
			
			# After widget (defined by themes)
			echo $after_widget;
		}
		
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			
			# Strip tags if needed and update the widget settings
			$instance['width'] = strip_tags($new_instance['width']);
			$instance['height'] = strip_tags($new_instance['height']);
			$instance['show_data_points'] = strip_tags($new_instance['show_data_points']);
			$instance['last_x_months'] = strip_tags($new_instance['last_x_months']);
			if($instance['last_x_months'] < 1) {
				$instance['last_x_months'] = 1;
			} else if($instance['last_x_months'] > 12) {
				$instance['last_x_months'] = 12;
			}
			$instance['bgcolor'] = strip_tags($new_instance['bgcolor']);
			
			return $instance;
		}
		
		function form($instance) {
			# Set up some default widget settings
			
			$defaults = array(
				'width' => 200,
				'height' => 150,
				'show_data_points' => 'true',
				'last_x_months' => 6,
				'bgcolor' => '#f9f9f9'
			);
			$instance = wp_parse_args((array) $instance, $defaults);
			?>
			
			<p>
				<label for="<?php echo $this->get_field_id('width'); ?>">Width:</label>
				<input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" style="width: 90%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('height'); ?>">Height:</label>
				<input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $instance['height']; ?>" style="width: 90%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('show_data_points'); ?>">Show Data Points:</label>
				<input id="<?php echo $this->get_field_id('show_data_points'); ?>" name="<?php echo $this->get_field_name('show_data_points'); ?>" value="<?php echo $instance['show_data_points']; ?>" style="width: 90%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('last_x_months'); ?>">Show the last x months:</label>
				<input id="<?php echo $this->get_field_id('last_x_months'); ?>" name="<?php echo $this->get_field_name('last_x_months'); ?>" value="<?php echo $instance['last_x_months']; ?>" style="width: 90%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('bgcolor'); ?>">Background color:</label>
				<input id="<?php echo $this->get_field_id('bgcolor'); ?>" name="<?php echo $this->get_field_name('bgcolor'); ?>" value="<?php echo $instance['bgcolor']; ?>" style="width: 90%;" />
			</p>
			
			<?php
		}
		
		function display_graph($instance) {
			### Networth Graph ###
			# Main Title
			$pw_main_title = get_user_option('pw_main_title');
			$pw_main_title_font_name = get_user_option('pw_main_title_font_name');
			$pw_main_title_font_color = get_user_option('pw_main_title_font_color');
			$pw_main_title_font_size = get_user_option('pw_main_title_font_size');
			$pw_main_title_bg_color = get_user_option('pw_main_title_bg_color');
			# Vertical
			$pw_vert_line_color = get_user_option('pw_vert_line_color');
			$pw_vert_font_name = get_user_option('pw_vert_font_name');
			$pw_vert_font_color = get_user_option('pw_vert_font_color');
			$pw_vert_font_size = 8;
			# Horizontal
			$pw_horiz_line_color = get_user_option('pw_horiz_line_color');
			$pw_horiz_font_name = get_user_option('pw_horiz_font_name');
			$pw_horiz_font_color = get_user_option('pw_horiz_font_color');
			$pw_horiz_font_size = 10;
			# Graph
			$pw_graph_do_not_fill = get_user_option('pw_graph_do_not_fill');
			$pw_graph_gradient_color_1 = get_user_option('pw_graph_gradient_color_1');
			$pw_graph_gradient_color_2 = get_user_option('pw_graph_gradient_color_2');
			$pw_graph_no_line = get_user_option('pw_graph_no_line');
			$pw_graph_line_color = get_user_option('pw_graph_line_color');
			$pw_graph_line_width = get_user_option('pw_graph_line_width');
			# Data Points
			$pw_graph_no_data_points = $instance['show_data_points'];
			$pw_data_point_color = get_user_option('pw_data_point_color');
			
			# User selected settings
			$width = $instance['width'];
			$height = $instance['height'];
			$last_x_months = $instance['last_x_months'];
			$bgcolor = $instance['bgcolor'];
			
			$out = '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/net-worth-calculator/js/swfobject.js"></script>
			<script type="text/javascript">

			var flashvars = {
				csv_file: "'.get_bloginfo('wpurl').'/wp-content/plugins/net-worth-calculator/generate_csv.php?last='.$last_x_months.'",

				main_title: "'.$pw_main_title.'",
				main_title_font_name: "'.$pw_main_title_font_name.'",
				main_title_font_color: "'.$pw_main_title_font_color.'",
				main_title_font_size: "'.$pw_main_title_font_size.'",
				main_title_bg_color: "'.$pw_main_title_bg_color.'",

				vert_line_color: "'.$pw_vert_line_color.'",
				vert_font_name: "'.$pw_vert_font_name.'",
				vert_font_color: "'.$pw_vert_font_color.'",
				vert_font_size: "'.$pw_vert_font_size.'",

				horiz_line_color: "'.$pw_horiz_line_color.'",
				horiz_font_name: "'.$pw_horiz_font_name.'",
				horiz_font_color: "'.$pw_horiz_font_color.'",
				horiz_font_size: "'.$pw_horiz_font_size.'",

				graph_gradient_color_1: "'.$pw_graph_gradient_color_1.'",
				graph_gradient_color_2: "'.$pw_graph_gradient_color_2.'",
				graph_line_color: "'.$pw_graph_line_color.'",
				graph_line_width: "'.$pw_graph_line_width.'",

				data_point_color: "'.$pw_data_point_color.'",
				'.($pw_graph_do_not_fill != 'true' ? 'graph_do_not_fill: true,' : '').'
				'.($pw_graph_no_line != 'true' ? 'graph_no_line: true,' : '').'
				'.($pw_graph_no_data_points != 'true' ? 'graph_no_data_points: true,' : '').'
			};
			</script>';

			$out .= ccf_show_graph_swf('widget_flashcontent', $vars=array('show_div'=>true), $width, $height, $hex_bg=$bgcolor);

			return $out;
		}
	}
} # End Class NetWorthCalculator_Widget
?>

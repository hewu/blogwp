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
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$("#accordion").accordion({ header: "h4" });
});
</script>

<?php
	include_once('grid_edit/ordering.php');
	include_once('graph.php');
	print ccf_graph_display();

	global $current_user;
	get_currentuserinfo();
	
	if($_POST['pw_hidden'] == 'Y') {
		# Form data sent
		$pw_main_title = $_POST['pw_main_title'];
		$pw_main_title_font_name = $_POST['pw_main_title_font_name'];
		$pw_main_title_font_color = $_POST['pw_main_title_font_color'];
		$pw_main_title_font_size = $_POST['pw_main_title_font_size'];
		$pw_main_title_bg_color = $_POST['pw_main_title_bg_color'];
		# Add 0x to colour values
		if(substr($pw_main_title_font_color, 0, 2) != '0x') {
			$pw_main_title_font_color = '0x'.$pw_main_title_font_color;
		}
		if(substr($pw_main_title_bg_color, 0, 2) != '0x') {
			$pw_main_title_bg_color = '0x'.$pw_main_title_bg_color;
		}
		update_user_option($current_user->ID, 'pw_main_title', $pw_main_title);
		update_user_option($current_user->ID, 'pw_main_title_font_name', $pw_main_title_font_name);
		update_user_option($current_user->ID, 'pw_main_title_font_color', $pw_main_title_font_color);
		update_user_option($current_user->ID, 'pw_main_title_font_size', $pw_main_title_font_size);
		update_user_option($current_user->ID, 'pw_main_title_bg_color', $pw_main_title_bg_color);
		
		$pw_vert_line_color = $_POST['pw_vert_line_color'];
		$pw_vert_font_name = $_POST['pw_vert_font_name'];
		$pw_vert_font_color = $_POST['pw_vert_font_color'];
		$pw_vert_font_size = $_POST['pw_vert_font_size'];
		# Add 0x to colour values
		if(substr($pw_vert_line_color, 0, 2) != '0x') {
			$pw_vert_line_color = '0x'.$pw_vert_line_color;
		}
		if(substr($pw_vert_font_color, 0, 2) != '0x') {
			$pw_vert_font_color = '0x'.$pw_vert_font_color;
		}
		update_user_option($current_user->ID, 'pw_vert_line_color', $pw_vert_line_color);
		update_user_option($current_user->ID, 'pw_vert_font_name', $pw_vert_font_name);
		update_user_option($current_user->ID, 'pw_vert_font_color', $pw_vert_font_color);
		update_user_option($current_user->ID, 'pw_vert_font_size', $pw_vert_font_size);
		
		$pw_horiz_line_color = $_POST['pw_horiz_line_color'];
		$pw_horiz_font_name = $_POST['pw_horiz_font_name'];
		$pw_horiz_font_color = $_POST['pw_horiz_font_color'];
		$pw_horiz_font_size = $_POST['pw_horiz_font_size'];
		# Add 0x to colour values
		if(substr($pw_horiz_line_color, 0, 2) != '0x') {
			$pw_horiz_line_color = '0x'.$pw_horiz_line_color;
		}
		if(substr($pw_horiz_font_color, 0, 2) != '0x') {
			$pw_horiz_font_color = '0x'.$pw_horiz_font_color;
		}
		update_user_option($current_user->ID, 'pw_horiz_line_color', $pw_horiz_line_color);
		update_user_option($current_user->ID, 'pw_horiz_font_name', $pw_horiz_font_name);
		update_user_option($current_user->ID, 'pw_horiz_font_color', $pw_horiz_font_color);
		update_user_option($current_user->ID, 'pw_horiz_font_size', $pw_horiz_font_size);
		
		$pw_graph_do_not_fill = $_POST['pw_graph_do_not_fill'];
		$pw_graph_gradient_color_1 = $_POST['pw_graph_gradient_color_1'];
		$pw_graph_gradient_color_2 = $_POST['pw_graph_gradient_color_2'];
		$pw_graph_no_line = $_POST['pw_graph_no_line'];
		$pw_graph_line_color = $_POST['pw_graph_line_color'];
		$pw_graph_line_width = $_POST['pw_graph_line_width'];
		# Add 0x to colour values
		if(substr($pw_graph_gradient_color_1, 0, 2) != '0x') {
			$pw_graph_gradient_color_1 = '0x'.$pw_graph_gradient_color_1;
		}
		if(substr($pw_graph_gradient_color_2, 0, 2) != '0x') {
			$pw_graph_gradient_color_2 = '0x'.$pw_graph_gradient_color_2;
		}
		if(substr($pw_graph_line_color, 0, 2) != '0x') {
			$pw_graph_line_color = '0x'.$pw_graph_line_color;
		}
		update_user_option($current_user->ID, 'pw_graph_do_not_fill', $pw_graph_do_not_fill);
		update_user_option($current_user->ID, 'pw_graph_gradient_color_1', $pw_graph_gradient_color_1);
		update_user_option($current_user->ID, 'pw_graph_gradient_color_2', $pw_graph_gradient_color_2);
		update_user_option($current_user->ID, 'pw_graph_no_line', $pw_graph_no_line);
		update_user_option($current_user->ID, 'pw_graph_line_color', $pw_graph_line_color);
		update_user_option($current_user->ID, 'pw_graph_line_width', $pw_graph_line_width);
		
		$pw_graph_no_data_points = $_POST['pw_graph_no_data_points'];
		$pw_data_point_color = $_POST['pw_data_point_color'];
		update_user_option($current_user->ID, 'pw_graph_no_data_points', $pw_graph_no_data_points);
		update_user_option($current_user->ID, 'pw_data_point_color', $pw_data_point_color);
		# Add 0x to colour values
		if(substr($pw_data_point_color, 0, 2) != '0x') {
			$pw_data_point_color = '0x'.$pw_data_point_color;
		}
		
		?>
		<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
		<?php
	} else {
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
		$pw_vert_font_size = get_user_option('pw_vert_font_size');
		# Horizontal
		$pw_horiz_line_color = get_user_option('pw_horiz_line_color');
		$pw_horiz_font_name = get_user_option('pw_horiz_font_name');
		$pw_horiz_font_color = get_user_option('pw_horiz_font_color');
		$pw_horiz_font_size = get_user_option('pw_horiz_font_size');
		# Graph
		$pw_graph_do_not_fill = get_user_option('pw_graph_do_not_fill');
		$pw_graph_gradient_color_1 = get_user_option('pw_graph_gradient_color_1');
		$pw_graph_gradient_color_2 = get_user_option('pw_graph_gradient_color_2');
		$pw_graph_no_line = get_user_option('pw_graph_no_line');
		$pw_graph_line_color = get_user_option('pw_graph_line_color');
		$pw_graph_line_width = get_user_option('pw_graph_line_width');
		# Data Points
		$pw_graph_no_data_points = get_user_option('pw_graph_no_data_points');
		$pw_data_point_color = get_user_option('pw_data_point_color');
		
		# Remove 0x from colour values
		//if(substr($pw_main_title_font_color)) {
		$pw_main_title_font_color = strtolower(str_replace('0x', '', $pw_main_title_font_color));
		$pw_main_title_bg_color = strtolower(str_replace('0x', '', $pw_main_title_bg_color));
		$pw_vert_line_color = strtolower(str_replace('0x', '', $pw_vert_line_color));
		$pw_vert_font_color = strtolower(str_replace('0x', '', $pw_vert_font_color));
		$pw_horiz_line_color = strtolower(str_replace('0x', '', $pw_horiz_line_color));
		$pw_horiz_font_color = strtolower(str_replace('0x', '', $pw_horiz_font_color));
		$pw_graph_gradient_color_1 = strtolower(str_replace('0x', '', $pw_graph_gradient_color_1));
		$pw_graph_gradient_color_2 = strtolower(str_replace('0x', '', $pw_graph_gradient_color_2));
		$pw_graph_line_color = strtolower(str_replace('0x', '', $pw_graph_line_color));
		$pw_data_point_color = strtolower(str_replace('0x', '', $pw_data_point_color));
	}
	
	function ccf_draw_select_box($name, $value, $array) {
		print '<select class="auto_submit" name="'.$name.'">';
		foreach ($array as $el) {
			print '<option '.($value == $el ? 'selected="selected" ' : '').'value="'.$el.'" style="padding-right: 6px">'.$el.'</option>';
		}
		print '</select>';
	}
	
	$font_sizes = array(8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36);
	$fonts = array('Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace', 'Garamond', 'Georgia, serif', 'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif', 'Verdana, Geneva, sans-serif');
?>

<div class="wrap">
	<h2>Net Worth Calculator Appearance Settings</h2>
	<div id="appearance" style="float: right;">
		<h2>Flash not available!</h2>
		<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
	</div>
	<form style="float: left;" id="personalwealth_form" name="personalwealth_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="pw_hidden" value="Y">

		<p>Adjust the default appearance settings for your Net Worth Calculator graph.
<div id="accordion">
	<h4>Main title:</h4>
	<div>
	<blockquote>
		<p class="clearfix"><input type="text" name="pw_main_title" class="auto_submit" value="<?php echo $pw_main_title; ?>" size="20" maxlength="20"></p>

		<p class="clearfix"><?php ccf_draw_select_box('pw_main_title_font_name', $pw_main_title_font_name, $fonts); ?>
		&nbsp;&nbsp;
		<?php ccf_draw_select_box('pw_main_title_font_size', $pw_main_title_font_size, $font_sizes); ?>px

		<p class="clearfix">
		<input type="text" name="pw_main_title_font_color" class="auto_submit" value="<?php echo $pw_main_title_font_color; ?>" size="7" maxlength="6" style="float: left"><span id="pw_main_title_font_color" class="color_preview" style="background-color: #<?php echo $pw_main_title_font_color; ?>"></span> &nbsp;<?php _e("(font colour)"); ?>
		<p
		<input type="text" name="pw_main_title_bg_color" class="auto_submit" value="<?php echo $pw_main_title_bg_color; ?>" size="7" maxlength="6" style="float: left"><span id="pw_main_title_bg_color" class="color_preview" style="background-color: #<?php echo $pw_main_title_bg_color; ?>"></span> &nbsp;<?php _e("(background)"); ?>
		</p>
	</blockquote>
	</div>
	
	<h4>Vertical (Net Worth) labels:</h4>
	<div>	
		<p class="clearfix">
		<?php ccf_draw_select_box('pw_vert_font_name', $pw_vert_font_name, $fonts); ?>
		&nbsp;&nbsp;
		<?php ccf_draw_select_box('pw_vert_font_size', $pw_vert_font_size, $font_sizes); ?>px

		<p class="clearfix">
		<input type="text" name="pw_vert_font_color" class="auto_submit" value="<?php echo $pw_vert_font_color; ?>"  size="7" maxlength="6" style="float: left"><span id="pw_vert_font_color" class="color_preview" style="background-color: #<?php echo $pw_vert_font_color; ?>"></span> &nbsp;<?php _e("(font colour)"); ?></p>
		
		<p class="clearfix">
		<input type="text" name="pw_vert_line_color" class="auto_submit" value="<?php echo $pw_vert_line_color; ?>" size="7" maxlength="6" style="float: left"><span id="pw_vert_line_color" class="color_preview" style="background-color: #<?php echo $pw_vert_line_color; ?>"></span> &nbsp;<?php _e("(line colour)"); ?></p>
	</div>

	<h4>Horizontal (Calendar) labels:</h4>
	<blockquote>
		<p class="clearfix">
		<?php ccf_draw_select_box('pw_horiz_font_name', $pw_horiz_font_name, $fonts); ?>
		&nbsp;&nbsp;
		<?php ccf_draw_select_box('pw_horiz_font_size', $pw_horiz_font_size, $font_sizes); ?>px
		</p>
		<p class="clearfix">
		<input type="text" name="pw_horiz_font_color" class="auto_submit" value="<?php echo $pw_horiz_font_color; ?>" size="7" maxlength="6" style="float: left"><span id="pw_horiz_font_color" class="color_preview" style="background-color: #<?php echo $pw_horiz_font_color; ?>"></span> &nbsp;<?php _e("(font colour)"); ?></p>
		<p class="clearfix">
		<input type="text" name="pw_horiz_line_color" class="auto_submit" value="<?php echo $pw_horiz_line_color; ?>" size="7" maxlength="6" style="float: left"><span id="pw_horiz_line_color" class="color_preview" style="background-color: #<?php echo $pw_horiz_line_color; ?>"></span> &nbsp;<?php _e("(line colour)"); ?></p>
		
	</blockquote>

	<h4>Graph Gradient:</h4>
	<blockquote>
		<p class="clearfix"><?php _e("Fill Graph with Gradient: "); ?>
		<input type="checkbox" name="pw_graph_do_not_fill" class="auto_submit" <?php if($pw_graph_do_not_fill == 'true') { print 'checked '; } ?>value="true"/></p>
		<p class="clearfix">
		<input type="text" name="pw_graph_gradient_color_1" class="auto_submit" value="<?php echo $pw_graph_gradient_color_1; ?>" size="5" style="float: left"><span id="pw_graph_gradient_color_1" class="color_preview" style="background-color: #<?php echo $pw_graph_gradient_color_1; ?>"></span> &nbsp;<?php _e("(gradient colour 1)"); ?><br/></p>
		<p class="clearfix">
		<input type="text" name="pw_graph_gradient_color_2" class="auto_submit" value="<?php echo $pw_graph_gradient_color_2; ?>" size="5" style="float: left"><span id="pw_graph_gradient_color_2" class="color_preview" style="background-color: #<?php echo $pw_graph_gradient_color_2; ?>"></span> &nbsp;<?php _e("(gradient colour 2)"); ?><br/></p>
	</blockquote>

	<h4>Graph Line:</h4>
	<blockquote>
		<p class="clearfix"><?php _e("Draw Line on Graph: "); ?>
		<input type="checkbox" name="pw_graph_no_line" class="auto_submit" <?php if($pw_graph_no_line == 'true') { print 'checked '; } ?>value="true"/></p>
		<p class="clearfix">
		<input type="text" name="pw_graph_line_color" class="auto_submit" value="<?php echo $pw_graph_line_color; ?>" size="7" maxlength="6" style="float: left"><span id="pw_graph_line_color" class="color_preview" style="background-color: #<?php echo $pw_graph_line_color; ?>"></span> &nbsp;<?php _e("(line colour)"); ?><br/></p>
		<p class="clearfix"><?php _e("Line Width: "); ?><br/>
		<input type="text" name="pw_graph_line_width" class="auto_submit" value="<?php echo $pw_graph_line_width; ?>" size="20"></p>
	</blockquote>

	<h4>Data Points:</h4>
	<blockquote>
		<p class="clearfix"><?php _e("Show Data Points: "); ?>
		<input type="checkbox" name="pw_graph_no_data_points" class="auto_submit" <?php if($pw_graph_no_data_points == 'true') { print 'checked '; } ?>value="true"/></p>
		<p class="clearfix">
		<input type="text" name="pw_data_point_color" class="auto_submit" value="<?php echo $pw_data_point_color; ?>" size="7" maxlength="6" style="float: left"><span id="pw_data_point_color" class="color_preview" style="background-color: #<?php echo $pw_data_point_color; ?>"></span></p>
	</blockquote>
	
	<h4>Ordering:</h4>
	<blockquote>
		<div style="width: 180px; float: left">
			<table id="assets"></table>
		</div>
		<div style="float: left">
			<table id="liabilities"></table>
		</div>
		
	</blockquote>

</div>
	</form>
</div>

<script type="text/javascript">
function refresh_graph() {
	// get new flashvars
	// alert('0x'+jQuery('input[name=pw_horiz_line_color]').val());
	var flashvars = {
		csv_file: "<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/net-worth-calculator/generate_csv.php",
		
		main_title: jQuery('input[name=pw_main_title]').val(),
		main_title_font_name: jQuery('select[name=pw_main_title_font_name]').val(),
		main_title_font_color: '0x'+jQuery('input[name=pw_main_title_font_color]').val(),
		main_title_font_size: jQuery('select[name=pw_main_title_font_size]').val(),
		main_title_bg_color: '0x'+jQuery('input[name=pw_main_title_bg_color]').val(),

		vert_line_color: '0x'+jQuery('input[name=pw_vert_line_color]').val(),
		vert_font_name: jQuery('select[name=pw_vert_font_name]').val(),
		vert_font_color: '0x'+jQuery('input[name=pw_vert_font_color]').val(),
		vert_font_size: jQuery('select[name=pw_vert_font_size]').val(),

		horiz_line_color: '0x'+jQuery('input[name=pw_horiz_line_color]').val(),
		horiz_font_name: jQuery('select[name=pw_horiz_font_name]').val(),
		horiz_font_color: '0x'+jQuery('input[name=pw_horiz_font_color]').val(),
		horiz_font_size: jQuery('select[name=pw_horiz_font_size]').val(),

		graph_gradient_color_1: '0x'+jQuery('input[name=pw_graph_gradient_color_1]').val(),
		graph_gradient_color_2: '0x'+jQuery('input[name=pw_graph_gradient_color_2]').val(),
		graph_line_color: '0x'+jQuery('input[name=pw_graph_line_color]').val(),
		graph_line_width: jQuery('input[name=pw_graph_line_width]').val(),
	};
	
	flashvars.data_point_color = '0x'+jQuery('input[name=pw_data_point_color]').val();
	if(jQuery('input[name=pw_graph_do_not_fill]:checked').val() != 'true') {
		flashvars.graph_do_not_fill = 'true';
	}
	if(jQuery('input[name=pw_graph_no_line]:checked').val() != 'true') {
		flashvars.graph_no_line = 'true';
	}
	if(jQuery('input[name=pw_graph_no_data_points]:checked').val() != 'true') {
		flashvars.graph_no_data_points = 'true';
	}
	
	var params = {};
	var attributes = {
	  id: "appearance",
	  name: "appearance"
	};
	
	swfobject.embedSWF("<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/net-worth-calculator/ccf_personal_wealth.swf", "appearance", "300", "300", "8.0.0","<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/net-worth-calculator/js/expressinstall.swf", flashvars, params, attributes);
}

jQuery('.auto_submit').change(function() {
	jQuery("#personalwealth_form").ajaxSubmit();
	refresh_graph();
	jQuery(this).effect('highlight', {}, 1000);
});
</script>
<?php print ccf_show_graph_swf('appearance', $vars=array(), $width=300, $height=300); ?>
<script type="text/javascript">
jQuery('input[name=pw_graph_line_width]').numeric();

<?php 
function ccf_draw_color_selector($name) {
	print <<<EOT
	jQuery('input[name=$name]').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			jQuery(el).val(hex);
			jQuery(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			jQuery(this).ColorPickerSetColor(this.value);
		},
		onChange: function (hsb, hex, rgb) {
			jQuery('#$name.color_preview').css('backgroundColor', '#' + hex);
			jQuery('input[name=$name]').val(hex);
		}
	})
	.bind('keyup', function(){
		jQuery(this).ColorPickerSetColor(this.value);
	});

EOT;
}

ccf_draw_color_selector('pw_main_title_font_color');
ccf_draw_color_selector('pw_main_title_bg_color');
ccf_draw_color_selector('pw_vert_line_color');
ccf_draw_color_selector('pw_vert_font_color');
ccf_draw_color_selector('pw_horiz_line_color');
ccf_draw_color_selector('pw_horiz_font_color');
ccf_draw_color_selector('pw_graph_gradient_color_1');
ccf_draw_color_selector('pw_graph_gradient_color_2');
ccf_draw_color_selector('pw_graph_line_color');
ccf_draw_color_selector('pw_data_point_color');
?>
</script>
<?php 
	$json_url = get_bloginfo('wpurl').'/wp-content/plugins/net-worth-calculator/json.php';
	grid_edit_ordering($json_url, 'asset', 'assets');
	grid_edit_ordering($json_url, 'liability', 'liabilities');
?>

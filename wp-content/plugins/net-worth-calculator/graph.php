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

function ccf_graph_display($uid = 0) {
	### Networth Graph ###
	# Main Title
	$pw_main_title = get_user_option('pw_main_title', $uid);
	$pw_main_title_font_name = get_user_option('pw_main_title_font_name', $uid);
	$pw_main_title_font_color = get_user_option('pw_main_title_font_color', $uid);
	$pw_main_title_font_size = get_user_option('pw_main_title_font_size', $uid);
	$pw_main_title_bg_color = get_user_option('pw_main_title_bg_color', $uid);
	# Vertical
	$pw_vert_line_color = get_user_option('pw_vert_line_color', $uid);
	$pw_vert_font_name = get_user_option('pw_vert_font_name', $uid);
	$pw_vert_font_color = get_user_option('pw_vert_font_color', $uid);
	$pw_vert_font_size = get_user_option('pw_vert_font_size', $uid);
	# Horizontal
	$pw_horiz_line_color = get_user_option('pw_horiz_line_color', $uid);
	$pw_horiz_font_name = get_user_option('pw_horiz_font_name', $uid);
	$pw_horiz_font_color = get_user_option('pw_horiz_font_color', $uid);
	$pw_horiz_font_size = get_user_option('pw_horiz_font_size', $uid);
	# Graph
	$pw_graph_do_not_fill = get_user_option('pw_graph_do_not_fill', $uid);
	$pw_graph_gradient_color_1 = get_user_option('pw_graph_gradient_color_1', $uid);
	$pw_graph_gradient_color_2 = get_user_option('pw_graph_gradient_color_2', $uid);
	$pw_graph_no_line = get_user_option('pw_graph_no_line', $uid);
	$pw_graph_line_color = get_user_option('pw_graph_line_color', $uid);
	$pw_graph_line_width = get_user_option('pw_graph_line_width', $uid);
	# Data Points
	$pw_graph_no_data_points = get_user_option('pw_graph_no_data_points', $uid);
	$pw_data_point_color = get_user_option('pw_data_point_color', $uid);

	$url = get_bloginfo('wpurl');
	
	$out = <<<EOT
		<script type="text/javascript" src="$url/wp-content/plugins/net-worth-calculator/js/swfobject.js"></script>
		<script type="text/javascript">
		<!--
		var flashvars = {
			csv_file: "$url/wp-content/plugins/net-worth-calculator/generate_csv.php",

			main_title: "$pw_main_title",
			main_title_font_name: "$pw_main_title_font_name",
			main_title_font_color: "$pw_main_title_font_color",
			main_title_font_size: "$pw_main_title_font_size",
			main_title_bg_color: "$pw_main_title_bg_color",

			vert_line_color: "$pw_vert_line_color",
			vert_font_name: "$pw_vert_font_name",
			vert_font_color: "$pw_vert_font_color",
			vert_font_size: "$pw_vert_font_size",

			horiz_line_color: "$pw_horiz_line_color",
			horiz_font_name: "$pw_horiz_font_name",
			horiz_font_color: "$pw_horiz_font_color",
			horiz_font_size: "$pw_horiz_font_size",

			graph_gradient_color_1: "$pw_graph_gradient_color_1",
			graph_gradient_color_2: "$pw_graph_gradient_color_2",
			graph_line_color: "$pw_graph_line_color",
			graph_line_width: "$pw_graph_line_width",

			data_point_color: '$pw_data_point_color',
EOT;
			if($pw_graph_do_not_fill != 'true') $out .= 'graph_do_not_fill: true,';
			if($pw_graph_no_line != 'true') $out .= 'graph_no_line: true,';
			if($pw_graph_no_data_points != 'true') $out .= 'graph_no_data_points: true,';
	$out .= '};
		function DoneLoading() {
			var img = new Image();
			img.src = "/wp-content/themes/default/images/kubrickheader.jpg";
		}
		// -->
		</script>';
	return $out;
}

function ccf_show_graph_swf($element_id='flashcontent', $vars=array(), $width=300, $height=200, $hex_bg='f9f9f9', $msg_no_flash='Flash not available!') {

	$user = ($vars['user']) ? $vars['user'] : '';
	$this_month = ($vars['this_month']) ? $vars['this_month'] : '';
	$num_months = ($vars['num_months']) ? $vars['num_months'] : '';

	$urlbase = get_bloginfo('wpurl');

	$out = '';

	if ($vars['show_div']) {

$out .= <<<EOT1
<div id="$element_id">
	<h2>$msg_no_flash</h2>
	<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
</div><div id="DoneLoading"></div>

EOT1;

	}
	if (!$vars['no_tags']) {
		$out .= '<script type="text/javascript">';
	}
	if ($user && $num_months && $this_month) {

$out .= <<<EOT2
	flashvars.csv_file = "$urlbase/wp-content/plugins/net-worth-calculator/generate_csv.php?user=$user&last=$num_months&month=$this_month";
EOT2;

	}

$out .= <<<EOT3
	var params = {};
	var attributes = {
	  id: "$element_id",
	  name: "$element_id"
	};
	swfobject.embedSWF("$urlbase/wp-content/plugins/net-worth-calculator/ccf_personal_wealth.swf", "$element_id", "$width", "$height", "8.0.0","$urlbase/wp-content/plugins/net-worth-calculator/js/expressinstall.swf", flashvars, params, attributes);
EOT3;
	if (!$vars['no_tags']) {
		$out .= '</script>';
	}

	return $out;
}

?>

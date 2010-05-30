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

include_once('grid_edit/months.php');
include_once('grid_edit/items.php');
include_once('graph.php');

print ccf_graph_display();
?>

<div class="wrap">
	<h2>Net Worth Calculator</h2>
	<div id="box" style="width: 720px">
		<div id="left-box" style="float: left; width: 320px;">
			<table id="months"></table>
			<br/>
			<div id="managedata">
				<h2>Flash not available!</h2>
				<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
			</div>
			<script type="text/javascript">
			function copy_link_graph(month) {
				var copy = prompt("Copy and paste this code into your post:", '[networth view="graph"]');
				window.clipboardData.setData('Text', copy);
			}
			</script>
			<span class="copy-link" style="margin-right: 20px"><a id="copy-link-graph" href="#">Get embed code for this graph</a></span>
		</div>
		<div id="right-box" style="float: left; width: 400px;">
			<table id="assets"></table>
			<br/>
			<table id="liabilities"></table>
			<script type="text/javascript">
			function copy_link_data(month) {
				var copy = prompt("Copy and paste this code into your post:", '[networth month="'+month+'" view="data"]');
				window.clipboardData.setData('Text', copy);
			}
			</script>
			<span class="copy-link"><a id="copy-link-data" href="#">Get embed code for this data table</a></span>
			<br/>
		</div>
	</div>


	<?php
		print ccf_show_graph_swf('managedata', $vars=array(), $width=300, $height=300);

		$json_url = get_bloginfo('wpurl').'/wp-content/plugins/net-worth-calculator/json.php';
		grid_edit_months($json_url); 
		grid_edit_items($json_url, 'liability', 'liabilities');
		grid_edit_items($json_url, 'asset', 'assets');
		
	?>
</div>

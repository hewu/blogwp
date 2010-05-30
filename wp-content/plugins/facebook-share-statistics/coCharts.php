<?php

/*  Copyright 2010  Carmine Olivo  (email : carmine.olivo@gmail.com)

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

function coFSS_parseXmlLinksStats( $urls, $bViewAll = false, $bSort = true )
{
	$linksstats = array();

	$total_count = 0;
	$total_max = 0;
	$xml = simplexml_load_file( 'http://api.facebook.com/restserver.php?method=links.getStats&urls=' . $urls );
	if ( ! empty($xml) )
	{
		foreach ( $xml->link_stat as $l )
		{
			$linkstat = array();
			$linkstat['total_count'] = intval( $l->total_count );
			if ( $bViewAll || $linkstat['total_count'] > 0 )
			{
				$url = esc_url( $l->url );
				$strlen_url = strlen( $url );
				$strlen_bloginfourl = strlen( get_bloginfo('url') );
				if ( $strlen_url > $strlen_bloginfourl+1 )
					$url_display = '&hellip;' . substr( $url, $strlen_bloginfourl );
				else
					$url_display = $url;
				$linkstat['url'] = $url;
				$linkstat['url_display'] = $url_display;
				$linkstat['share_count'] = intval( $l->share_count );
				$linkstat['like_count'] = intval( $l->like_count );
				$linkstat['comment_count'] = intval( $l->comment_count );
				$linkstat['click_count'] = intval( $l->click_count );
				$linkstat['url_normalized'] = esc_url_raw( $l->normalized_url );
				$linksstats[] = $linkstat;

				if ( $linkstat['total_count'] > 0 )
					$total_count++;
				if ( $linkstat['total_count'] > $total_max )
					$total_max = $linkstat['total_count'];
			}
		}
		if ( $bSort )
			usort( $linksstats, 'coFSS_sortByTotal' );
	}
	array_push( $linksstats, array( 'count' => $total_count, 'max' => $total_max ) );

	return $linksstats;
}

function coFSS_sortByTotal( $linkstatA, $linkstatB ) {
	if ( $linkstatA['total_count'] == $linkstatB['total_count'] )
		return 0;
	elseif ( $linkstatA['total_count'] < $linkstatB['total_count'] )
		return 1;
	else
		return -1;
}

function coFSS_showStats( $linksstats )
{
	$settings = coFSS_getOptions();

	$totals = array_pop( $linksstats );

	echo '
<div class="wrap">';

	echo '
	<img id="coFSSlogo" src="'.CO_FSS_DIRURL.'coFSSlogo64.png" alt="" />
	<h2>Facebook Share Statistics</h2>';

	if ( !empty( $linksstats ) )
	{
		echo '
	<table class="widefat post fixed" cellspacing="0" cellpadding="0" border=1>
		 <thead>
			<tr>
				<th title="'.__('The URL for the page being shared','coFSS').'">
					URL
				</th>
				<th title="'.__('SHARE COUNT','coFSS').': '.__('The number of times users have shared the post on Facebook','coFSS').'">
					<img src="'.CO_FSS_DIRURL.'FBshare.png"
						alt="'.__('SHARE COUNT','coFSS').': '.__('The number of times users have shared the post on Facebook','coFSS').'" />
				</th>
				<th title="'.__('LIKE COUNT','coFSS').': '.__('The number of times Facebook users have &quot;Liked&quot; the post','coFSS').'">
					<img src="'.CO_FSS_DIRURL.'FBlike.png"
						alt="'.__('LIKE COUNT','coFSS').': '.__('The number of times Facebook users have &quot;Liked&quot; the post','coFSS').'" />
				</th>
				<th title="'.__('COMMENT COUNT','coFSS').': '.__('The number of comments users have made on the shared story','coFSS').'">
					<img src="'.CO_FSS_DIRURL.'FBcomment.png"
						alt="'.__('COMMENT COUNT','coFSS').': '.__('The number of comments users have made on the shared story','coFSS').'" />
				</th>
				<th title="'.__('TOTAL COUNT','coFSS').': '.__('The total number of times the URL has been shared, liked, or commented on','coFSS').'">
					<img src="'.CO_FSS_DIRURL.'FBtotal.png"
						alt="'.__('TOTAL COUNT','coFSS').': '.__('The total number of times the URL has been shared, liked, or commented on','coFSS').'" />
				</th>
				<th title="'.__('CLICK COUNT','coFSS').': '.__('The number of times users have clicked back to the Share page from Facebook','coFSS').'">
					<img src="'.CO_FSS_DIRURL.'FBclick.png"
						alt="'.__('CLICK COUNT','coFSS').': '.__('The number of times users have clicked back to the Share page from Facebook','coFSS').'" />
				</th>
			</tr>
		 </thead>
		 <tbody>';
		$count = 1;
		foreach ( $linksstats as $linkstat )
		{
			$c = '0';
			if ( $settings['viewall'] === '1' && $linkstat['total_count'] < 7 )
				$c = dechex(14-$linkstat['total_count']*2);
			echo '
			<tr style="color:#'.$c.$c.$c.'" title="'.$count.'">
				<td><a href="'.$linkstat['url_normalized'].'" title="'.$linkstat['url'].'">'.$linkstat['url_display'].'</a></td>
				<td>'.$linkstat['share_count'].'</td>
				<td>'.$linkstat['like_count'].'</td>
				<td>'.$linkstat['comment_count'].'</td>
				<td>'.$linkstat['total_count'].'</td>
				<td>'.$linkstat['click_count'].'</td>
			</tr>';
			$count++;
		}
		echo '
		</tbody>
	</table>';

		echo '
	<div class="coChart">
		<a href="' .coFSS_getChartUrl($linksstats,'share_count',true).   '" target="_blank"><img title="'.__('SHARE COUNT','coFSS').'" src="'   .coFSS_getChartUrl($linksstats,'share_count').   '" /></a>
		<a href="' .coFSS_getChartUrl($linksstats,'like_count',true).    '" target="_blank"><img title="'.__('LIKE COUNT','coFSS').'" src="'    .coFSS_getChartUrl($linksstats,'like_count').    '" /></a>
		<a href="' .coFSS_getChartUrl($linksstats,'comment_count',true). '" target="_blank"><img title="'.__('COMMENT COUNT','coFSS').'" src="' .coFSS_getChartUrl($linksstats,'comment_count'). '" /></a>
		<a href="' .coFSS_getChartUrl($linksstats,'total_count',true).   '" target="_blank"><img title="'.__('TOTAL COUNT','coFSS').'" src="'   .coFSS_getChartUrl($linksstats,'total_count').   '" /></a>
		<a href="' .coFSS_getChartUrl($linksstats,'click_count',true).   '" target="_blank"><img title="'.__('CLICK COUNT','coFSS').'" src="'   .coFSS_getChartUrl($linksstats,'click_count').   '" /></a>
	</div>';
	}
	else
		echo '
	<p>' . __('No results found.') . '</p>';

	global $current_user;
	get_currentuserinfo();
	if ( (int)$current_user->user_level >= 8 )
	{
		if ( $settings['viewall'] === '1' ) $viewall = ' checked="checked"'; else $viewall = '';
		if ( $settings['sort'] === '1' ) $sort = ' checked="checked"'; else $sort = '';
		echo '
	<form method="post" action="options.php">
		'; settings_fields( 'coFSS-page' ); echo '
		<table class="form-table">
			<tr valign="top">
				<th scope="row" style="white-space:nowrap">'.__('Number of posts to show:').'</th>
				<td><input type="text" name="coFSS-settings[posts_per_page]" value="'.$settings['posts_per_page'].'" /></td>
				<td><input type="checkbox" name="coFSS-settings[viewall]" value="1"'.$viewall.' />&nbsp;'.__('View all').' 
					<input type="checkbox" name="coFSS-settings[sort]" value="1"'.$sort.' />&nbsp;'.__('Order by Rating').'</td>
			</tr>
			<tr valign="top">
				<th scope="row" style="white-space:nowrap">'.__('Categories').' ('.__('Filter').'):</th>
				<td><input type="text" name="coFSS-settings[cat]" value="'.$settings['cat'].'" title="ID"/></td>
				<td>&mdash;'.__('Separate multiple categories with commas.').'</td>
			</tr>
			<tr valign="top">
				<th scope="row" style="white-space:nowrap">'.__('Pages').' ('.__('Add').'):</th>
				<td><input type="text" name="coFSS-settings[page_id]" value="'.$settings['page_id'].'" title="ID"/></td>
				<td>&mdash;'.__('Page IDs, separated by commas.').'</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" class="button-primary" value="'.__('Update').'" /></p>
	</form>';
	}

	echo '
</div>';
}

function coFSS_getChartUrl( $linksstats, $sDataKey, $bBig = false )
{
	$count = 1;
	foreach( $linksstats as $linkstat )
	{
		$sData .= $linkstat[$sDataKey] . ',';
		if ( !$bBig )
			$sLabels .= $count . '|';
		else
			$sLabels .= urlencode(html_entity_decode($linkstat['url_display'],ENT_NOQUOTES,'UTF-8')) . '|';
		$count++;
	}
	$sData = trim( $sData, ',' );
	$sLabels = trim ( $sLabels, '|' );

	$chart_api = 'http://chart.apis.google.com/chart?';
	$chart_opt = 'cht=p3&amp;chf=bg,s,F9F9F900&amp;chco=3377FF';
	if ( !$bBig )
		$chart_size = '128x64';
	else
		$chart_size = '1000x300';
	$chart_url = $chart_api.$chart_opt.'&amp;chs='.$chart_size.'&amp;chd=t:'.$sData.'&amp;chl='.$sLabels;

	return $chart_url;
}

?>
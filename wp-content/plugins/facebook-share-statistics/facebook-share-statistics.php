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

/*
Plugin Name: Facebook Share Statistics
Plugin URI: http://wordpress.org/extend/plugins/facebook-share-statistics/
Description: Show informations about your posts shared on <em>Facebook</em>, with statistics and charts about the number of "<em>like</em>", <em>comments</em> and <em>clicks</em> received.
Version: 0.8.2
Author: Carmine Olivo
Author URI: http://wordpress.org/extend/plugins/profile/carmine-olivo
License: GPL2
*/

/*
Text Domain: coFSS
*/

class coFSS
{
	/** settings */
	private $nPostsPerPage;
	private $bViewAll;
	private $bSort;
	private $sCat;
	private $sPageID;
	private $nUserLevel;

	/** stats info */
	private $nCount_total;
	private $nMax_total;

	function __construct()
	{
		if ( is_admin() )
		{
			define( 'CO_FSS_URL', WP_PLUGIN_URL.'/facebook-share-statistics/' );

			add_action( 'activate_facebook-share-statistics/facebook-share-statistics.php', array($this,'activate') );
			load_plugin_textdomain( 'coFSS', null, basename(dirname(__FILE__)) );
			add_action( 'admin_menu', array($this,'menu') );
		}
	}

	function doPage ()
	{
		$urls = $this->getPermalinks();
		$linksstats = $this->parseXmlLinksStats( $urls );
		$this->showStats( $linksstats );
	}

	function getPermalinks ()
	{
		$urls  = array( get_bloginfo('url').'/' => get_bloginfo('title') );

		$pageID = explode(',',$this->sPageID); // and othersID
		foreach ( $pageID as $p )
			$urls[get_permalink($p)] = get_the_title($p);

		if ( $this->nPostsPerPage > 0 )
		{
			query_posts( 'posts_per_page='.$this->nPostsPerPage.'&cat='.$this->sCat.'&post_status=publish' );

			if ( have_posts() ) : while ( have_posts() ) : the_post();
					$urls[get_permalink()] = get_the_title();
			endwhile; endif;

			wp_reset_query();
		}

		return $urls;
	}

	function parseXmlLinksStats( $urls )
	{
		$linksstats = array();

		$nCount_total = 0;
		$nMax_total = 0;
		$sUrls = '';
		foreach ( $urls as $url => $t )
			$sUrls .= $url.',';
		$xml = simplexml_load_file( 'http://api.facebook.com/restserver.php?method=links.getStats&urls=' . $sUrls );
		if ( ! empty($xml->link_stat) )
		{
			foreach ( $xml->link_stat as $l )
			{
				$linkstats = array();
				$linkstats['total_count'] = intval( $l->total_count );
				if ( $this->bViewAll || $linkstats['total_count'] > 0 )
				{
					$sUrl = esc_url( $l->url );
					$linkstats['url'] = $sUrl;
					$linkstats['display'] = $urls[$sUrl];
					$linkstats['share_count'] = intval( $l->share_count );
					$linkstats['like_count'] = intval( $l->like_count );
					$linkstats['comment_count'] = intval( $l->comment_count );
					$linkstats['click_count'] = intval( $l->click_count );
					$linkstats['url_normalized'] = esc_url_raw( $l->normalized_url );
					$linksstats[] = $linkstats;

					if ( $linkstats['total_count'] > 0 )
						$nCount_total++;
					if ( $linkstats['total_count'] > $nMax_total )
						$nMax_total = $linkstats['total_count'];
				}
			}
			if ( $this->bSort )
				usort( $linksstats, array($this,'sortByTotal') );
		}
		$this->nCount_total = $nCount_total;
		$this->nMax_total = $nMax_total;

		return $linksstats;
	}

	function sortByTotal( $linkstatsA, $linkstatsB ) {
		if ( $linkstatsA['total_count'] == $linkstatsB['total_count'] )
			return 0;
		elseif ( $linkstatsA['total_count'] < $linkstatsB['total_count'] )
			return 1;
		else
			return -1;
	}

	private function showStats( $linksstats )
	{
		echo '
	<div class="wrap">';

		echo '
		<h2>Facebook Share Statistics</h2>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="coFSSdonate">
			<input type="hidden" name="cmd" value="_s-xclick" />
			<input type="hidden" name="hosted_button_id" value="K6UN75J6WB3F2" />
			<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" name="submit" alt="Donate with PayPal" />
			<img alt="" border="0" src="https://www.paypal.com/it_IT/i/scr/pixel.gif" width="1" height="1" />
		</form>
		<img id="coFSSlogo" src="'.CO_FSS_URL.'coFSSlogo64.png" alt="" />';

		if ( ! empty( $linksstats ) )
		{
			echo '
		<table class="widefat post fixed" cellspacing="0" cellpadding="0" border="1">
			<thead>
				<tr>
					<th title="'.__('The URL for the page being shared','coFSS').'">
						URL
					</th>
					<th title="'.__('SHARE COUNT','coFSS').': '.__('The number of times users have shared the post on Facebook','coFSS').'">
						<img src="'.CO_FSS_URL.'FBshare.png"
							alt="'.__('SHARE COUNT','coFSS').': '.__('The number of times users have shared the post on Facebook','coFSS').'" />
					</th>
					<th title="'.__('LIKE COUNT','coFSS').': '.__('The number of times Facebook users have &quot;Liked&quot; the post','coFSS').'">
						<img src="'.CO_FSS_URL.'FBlike.png"
							alt="'.__('LIKE COUNT','coFSS').': '.__('The number of times Facebook users have &quot;Liked&quot; the post','coFSS').'" />
					</th>
					<th title="'.__('COMMENT COUNT','coFSS').': '.__('The number of comments users have made on the shared story','coFSS').'">
						<img src="'.CO_FSS_URL.'FBcomment.png"
							alt="'.__('COMMENT COUNT','coFSS').': '.__('The number of comments users have made on the shared story','coFSS').'" />
					</th>
					<th title="'.__('TOTAL COUNT','coFSS').': '.__('The total number of times the URL has been shared, liked, or commented on','coFSS').'">
						<img src="'.CO_FSS_URL.'FBtotal.png"
							alt="'.__('TOTAL COUNT','coFSS').': '.__('The total number of times the URL has been shared, liked, or commented on','coFSS').'" />
					</th>
					<th title="'.__('CLICK COUNT','coFSS').': '.__('The number of times users have clicked back to the Share page from Facebook','coFSS').'">
						<img src="'.CO_FSS_URL.'FBclick.png"
							alt="'.__('CLICK COUNT','coFSS').': '.__('The number of times users have clicked back to the Share page from Facebook','coFSS').'" />
					</th>
				</tr>
			</thead>
			<tbody>';

			$count = 1;
			foreach ( $linksstats as $linkstats )
			{
				$c = '0';
				if ( $this->bViewAll )
					$c = dechex( 14 - (($linkstats['total_count'] > 14) ? 14 : $linkstats['total_count']) ); // $c = dechex( 14 - $linkstats['total_count']*14/$this->nMax_total );
				echo '
				<tr style="color:#'.$c.$c.$c.'" title="'.$count.'">
					<td><a href="'.$linkstats['url_normalized'].'" title="'.$linkstats['url'].'">'.$linkstats['display'].'</a></td>
					<td title="'.__('SHARE COUNT','coFSS').': '.__('The number of times users have shared the post on Facebook','coFSS').'">'.$linkstats['share_count'].'</td>
					<td title="'.__('LIKE COUNT','coFSS').': '.__('The number of times Facebook users have &quot;Liked&quot; the post','coFSS').'">'.$linkstats['like_count'].'</td>
					<td title="'.__('COMMENT COUNT','coFSS').': '.__('The number of comments users have made on the shared story','coFSS').'">'.$linkstats['comment_count'].'</td>
					<td title="'.__('TOTAL COUNT','coFSS').': '.__('The total number of times the URL has been shared, liked, or commented on','coFSS').'">'.$linkstats['total_count'].'</td>
					<td title="'.__('CLICK COUNT','coFSS').': '.__('The number of times users have clicked back to the Share page from Facebook','coFSS').'">'.$linkstats['click_count'].'</td>
				</tr>';
				$count++;
			}
			echo '
			</tbody>
		</table>';

			echo '
		<div class="coChart">';

			$sTotalChartUrl = $this->getChartUrl( $linksstats, 'total_count', __('TOTAL COUNT','coFSS').':' );
			if ( $sTotalChartUrl )
				echo '
			<a href="'.$sTotalChartUrl['big'].'" target="_blank" class="lightbox"><img id="total_chart" title="'.__('TOTAL COUNT','coFSS').'"  alt="'.__('TOTAL COUNT','coFSS').'" src="'.$sTotalChartUrl['small'].'" /></a>';

			$sShareChartUrl = $this->getChartUrl( $linksstats, 'share_count', __('SHARE COUNT','coFSS').':' );
			if ( $sShareChartUrl )
				echo '
			<a href="'.$sShareChartUrl['big'].'" target="_blank" class="lightbox"><img id="share_chart" title="'.__('SHARE COUNT','coFSS').'" alt="'.__('SHARE COUNT','coFSS').'" src="'.$sShareChartUrl['small'].'" /></a>';

			$sLikeChartUrl = $this->getChartUrl( $linksstats, 'like_count', __('LIKE COUNT','coFSS').':' );
			if ( $sLikeChartUrl )
				echo '
			<a href="'.$sLikeChartUrl['big'].'" target="_blank" class="lightbox"><img id="like_chart" title="'.__('LIKE COUNT','coFSS').'" alt="'.__('LIKE COUNT','coFSS').'" src="'.$sLikeChartUrl['small'].'" /></a>';

			$sCommentChartUrl = $this->getChartUrl( $linksstats, 'comment_count', __('COMMENT COUNT','coFSS').':' );
			if ( $sCommentChartUrl )
				echo '
			<a href="'.$sCommentChartUrl['big'].'" target="_blank" class="lightbox"><img id="comment_chart" title="'.__('COMMENT COUNT','coFSS').'" alt="'.__('COMMENT COUNT','coFSS').'" src="'.$sCommentChartUrl['small'].'" /></a>';

			$sClickChartUrl = $this->getChartUrl( $linksstats, 'click_count', __('CLICK COUNT','coFSS').':' );
			if ( $sClickChartUrl )
				echo '
			<a href="'.$sClickChartUrl['big'].'" target="_blank" class="lightbox"><img id="click_chart" title="'.__('CLICK COUNT','coFSS').'" alt="'.__('CLICK COUNT','coFSS').'" src="'.$sClickChartUrl['small'].'" /></a>';

			echo '
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("a.lightbox").lightBox({
					overlayBgColor: "#666",
					overlayOpacity: 0.5,
					imageLoading:   "'.CO_FSS_URL.'jquery-lightbox-0.5/images/lightbox-ico-loading.gif",
					imageBtnClose:  "'.CO_FSS_URL.'jquery-lightbox-0.5/images/lightbox-btn-close.gif",
					imageBtnPrev:   "'.CO_FSS_URL.'jquery-lightbox-0.5/images/lightbox-btn-prev.gif",
					imageBtnNext:   "'.CO_FSS_URL.'jquery-lightbox-0.5/images/lightbox-btn-next.gif",
					imageBlank:     "'.CO_FSS_URL.'jquery-lightbox-0.5/images/lightbox-blank.gif",
					txtImage:       "",
					txtOf:          "/"
				});
			});
			</script>';

			echo '
		</div>';
		}
		else
			echo '
		<h3>' . __('No results found.') . '</h3>';

		if ( $this->getCurrentUserLevel() >= 8 )
		{
			if ( $this->bViewAll ) $viewall = ' checked="checked"'; else $viewall = '';
			if ( $this->bSort ) $sort = ' checked="checked"'; else $sort = '';
			echo '
		<form method="post" action="options.php">
			'; settings_fields( 'coFSS-page' ); echo '
			<table class="form-table">
				<tr valign="top">
					<th scope="row" style="white-space:nowrap">'.__('Number of posts to show:').'</th>
					<td><input type="text" name="coFSS-settings[posts_per_page]" value="'.$this->nPostsPerPage.'" /></td>
					<td><input type="checkbox" name="coFSS-settings[viewall]" value="1"'.$viewall.' />&nbsp;'.__('View all').' 
						<input type="checkbox" name="coFSS-settings[sort]" value="1"'.$sort.' />&nbsp;'.__('Order by Rating').'</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="white-space:nowrap">'.__('Categories').':</th>
					<td><input type="text" name="coFSS-settings[cat]" value="'.$this->sCat.'" title="ID"/></td>
					<td>&mdash;'.__('Separate multiple categories with commas.').'</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="white-space:nowrap">'.__('Pages').':</th>
					<td><input type="text" name="coFSS-settings[page_id]" value="'.$this->sPageID.'" title="ID"/></td>
					<td>&mdash;'.__('Page IDs, separated by commas.').'</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="white-space:nowrap">'.__('Show menu').':</th>
					<td colspan="2"><select name="coFSS-settings[user_level]">'.$this->getDropdownUserLevels_options($this->nUserLevel).'</select></td>
				</tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="'.__('Update').'" /></p>
		</form>';
		}

		echo '
	</div>';
	}

	function getCurrentUserLevel()
	{
		global $current_user;
		return $current_user->user_level;
	}

	function getDropdownUserLevels_options( $nSelected = 10 )
	{
		$p = '';
		$r = '';

		$editable_roles = get_editable_roles();

		foreach( $editable_roles as $role => $details ) {
			$name = translate_user_role($details['name'] );
			
			$user_level = 10;
			while ( $user_level > 0  &&  ! isset($details['capabilities']['level_'.$user_level]) )
				$user_level--;

			if ( $nSelected == $user_level ) // Make default first in list
				$p = "\n\t<option selected='selected' value='".$user_level."'>$name</option>";
			else
				$r .= "\n\t<option value='".$user_level."'>$name</option>";
		}
		return $p . $r;
	}

	function getChartUrl( $linksstats, $sDataKey, $sTitle = '' )
	{
		$sChartUrl = array();

		$nCount = 0;
		foreach( $linksstats as $linkstats )
		{
			if ( $linkstats[$sDataKey] > 0 )
			{
				$nCount++;
				$sData .= $linkstats[$sDataKey] . ',';
				$sLabels .= $nCount . '|';
				$sLabels_big .= html_entity_decode($linkstats['display'],ENT_NOQUOTES,'UTF-8') . ' ('.$linkstats[$sDataKey].')' . '|';
			}
		}
		$sData = trim( $sData, ',' );
		$sLabels = trim( $sLabels, '|' );
		$sLabels_big = trim( $sLabels_big, '|' );

		if ( $nCount > 0 )
		{
			$sChartApi = 'http://chart.apis.google.com/chart?';

			$sChartOpt  = 'cht=p3';
			$sChartOpt .= '&chf=bg,s,F9F9F900';
			$sChartOpt .= '&chco=3377FF';
			$sChartOpt .= '&chd=t:'.$sData;

			$sChartOpt_big  = $sChartOpt.'&chs=1000x300';
			$sChartOpt_big .= '&chl=' . $sLabels_big;
			$sChartOpt_big .= '&chts=CCCCCC,20';
			$sChartOpt_big .= '&chtt=' . $sTitle;

			$sChartOpt .= '&chs=128x64';

			$sChartUrl['small'] = $sChartApi . str_replace('%3D','=',str_replace('%26','&amp;',urlencode( $sChartOpt )));
			$sChartUrl['big']   = $sChartApi . str_replace('%3D','=',str_replace('%26','&amp;',urlencode( $sChartOpt_big )));
		}

		return $sChartUrl;
	}

	function activate()
	{
		register_uninstall_hook( __FILE__, array($this,'uninstall') );

		$settings = $this->validateSettings( /*defaults*/ );
		add_option( 'coFSS-settings', $settings, null, 'no' );
	}

	function uninstall()
	{
		delete_option( 'coFSS-settings' );
	}

	function init()
	{
		global $current_user;
		get_currentuserinfo();
		wp_register_style( 'coFSS-style', CO_FSS_URL.'facebook-share-statistics.css' );
		wp_register_style( 'coFSS-style_lightbox', CO_FSS_URL.'jquery-lightbox-0.5/css/jquery.lightbox-0.5.css' );
		wp_register_script( 'coFSS-script_lightbox', CO_FSS_URL.'jquery-lightbox-0.5/js/jquery.lightbox-0.5.js' );
		register_setting( 'coFSS-page', 'coFSS-settings', array($this,'validateSettings') );
	}

	function style()
	{
		wp_enqueue_style( 'coFSS-style_lightbox' );
		wp_enqueue_style( 'coFSS-style' );
		wp_enqueue_script( 'coFSS-script_lightbox' );
	}

	function menu()
	{
		$settings = $this->getOptions ();
		$this->nPostsPerPage = $settings['posts_per_page'];
		$this->bViewAll = $settings['viewall'];
		$this->bSort = $settings['sort'];
		$this->sCat = $settings['cat'];
		$this->sPageID =$settings['page_id'];
		$this->nUserLevel =$settings['user_level'];

		$page = add_submenu_page( 'index.php', 'Facebook Share Statistics', 'FB Share Stats', $this->nUserLevel, 'coFSS-page', array($this,'doPage') );
		add_action( 'admin_print_styles-'.$page, array($this,'style') );
		add_action( 'admin_init', array($this,'init') );
	}

	function getOptions() {
		$options = $this->validateSettings ( get_option('coFSS-settings') );
		return $options;
	}

	function validateSettings( $array = array() )
	{
		if ( isset($array['posts_per_page']) )
			$array['posts_per_page'] = intval((string)$array['posts_per_page']);
		else
			$array['posts_per_page'] = 15;

		if ( isset($array['viewall']) )
			$array['viewall'] = (intval((string)$array['viewall']) != 0);
		else
			$array['viewall'] = false;

		if ( isset($array['sort']) )
			$array['sort'] = (intval((string)$array['sort']) != 0);
		else
			$array['sort'] = false;

		if ( isset($array['cat']) && (string)$array['cat']!='' )
			$this->coSanitizeNumbersList( &$array['cat'] );
		else
		{
			$cat = get_all_category_ids ();
			sort( $cat, SORT_NUMERIC );
			$cat = implode( ',' , $cat );
			$array['cat'] = $cat;
		}

		if ( isset($array['page_id']) )
			$this->coSanitizeNumbersList( &$array['page_id'] );
		else
			$array['page_id'] = '';

		if ( isset($array['user_level']) )
			$array['user_level'] = intval((string)$array['user_level']);
		else
			$array['user_level'] = 10;

		return $array;
	}

	function coSanitizeNumbersList( &$sList, $sSeparator = ',' )
	{
		if ( (string)$sList != '' )
		{
			$list = explode( $sSeparator, (string)$sList );
			foreach ( $list as $k => $v )
				$list[$k] = (string)intval( $v );
			$sList = implode( $sSeparator, $list );
		}
		else
		{
			$sList = '';
		}
	}
}

if ( defined('WP_PLUGIN_URL') )
	new coFSS();

?>
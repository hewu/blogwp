<?php
/*
Plugin Name: myeasydb
Plugin URI: http://myeasydb.com
Description: Edit and handle your MySQL tables in the easiest and quickest way.
Version: 0.0.6
Author: Ugo Grandolini aka "camaleo"
Author URI: http://myeasydb.com
*/
/*	Edit and handle your MySQL tables in the easiest and quickest way.
	Copyright (C) 2010 Ugo Grandolini  (email : info@myeasydb.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

/**
 * Package main script
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.6
 */

require_once('med-config.php');

#
#	http://codex.wordpress.org/Function_Reference/wp_enqueue_style
#
//wp_enqueue_style( $handle, $src, $deps, $ver, $media );
wp_enqueue_style( 'med_calendar', PLUGIN_LINK.'css/dhtmlgoodies_calendar.css', '', '20070830', 'screen' );
wp_enqueue_style( 'med_style', PLUGIN_LINK.'css/screen.css', '', '20100122', 'screen' );

#
#	http://codex.wordpress.org/Function_Reference/wp_enqueue_script
#
//wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
wp_enqueue_script( 'med_core_js', PLUGIN_LINK.'js/myeasydb.js.php', '', '20100123', false );
wp_enqueue_script( 'med_ajax_js', PLUGIN_LINK.'js/ajax_ro.js', '', '20100123', false );

wp_enqueue_script( 'med_calendar_js', PLUGIN_LINK.'js/dhtmlgoodies_calendar.js', '', '20070830', false );
//wp_enqueue_script( 'med_tooltip_js', PLUGIN_LINK.'js/dhtmlgoodies_tooltip.js', '', '20060311', false );	#	0.0.6: included in screen.css


//wp_enqueue_script( 'med_bubblett_js', PLUGIN_LINK.'js/dhtmlgoodies_bubble-tooltip.js', '', '20060830', false );


#
#	hook for adding admin menus
#
add_action('admin_menu', 'myeasydb_add_pages');


///*
//	Plugin Name: Menu Test
//	*/
//
//	//add_action( 'admin_menu', menu_test_add_pages );
//
//	function menu_test_add_pages() {
//		$page_ref = add_menu_page( '', 'Menu Test', 10, 'menu-test', 'menu_test_index' );
//		add_action( 'load-' . $page_ref, 'menu_test_set_title' );
//	}
//
//	function menu_test_index() {
//		echo "<h3>Menu Test</h3>";
//	}
//
//	function menu_test_set_title() {
//		global $title;
//		$title = 'Menu Test';
//	}



// http://codex.wordpress.org/I18n_for_WordPress_Developers
load_plugin_textdomain( MED_LOCALE, 'wp-content/plugins/' . $myeasydb_dir, $myeasydb_dir );


define('MYEASYDB_POPWIN', '<div id="myeasydb_popWin" class="myeasydb_popWin" style="display:none;"></div>');


#
#	action function for above hook
#
function myeasydb_add_pages() {
	#
	#	settings submenu
	#
	add_options_page(__( 'myEASYdb', MED_LOCALE ), __( 'myEASYdb', MED_LOCALE ), 'administrator', 'med_options', 'myeasydb_options_page');

	#
	#	tools submenu
	#
	add_management_page(__( 'myEASYdb', MED_LOCALE ), __( 'myEASYdb', MED_LOCALE ), 'administrator', 'med_tools', 'myeasydb_manage_page');

	#
	#	top level menu
	#
	add_menu_page(__( 'myEASYdb', MED_LOCALE ), __( 'myEASYdb', MED_LOCALE ), 'administrator', 'med_admin', 'myeasydb_toplevel_page', PLUGIN_LINK.'img/myEASYdb-16.png');

//$page_ref = add_menu_page( '', 'Menu Test', 10, 'menu-test', 'menu_test_index' );
//add_action( 'load-' . $page_ref, 'menu_test_set_title' );

//	$page_ref =  add_menu_page('', __( 'MySQL Admin', MED_LOCALE ), 'administrator', 'med_adminAAA', 'myeasydb_toplevel_page', PLUGIN_LINK.'img/mysql.png');
//	add_action( 'load-' . $page_ref, 'set_title' );
//    function set_title() {
//        global $title;
//        $title = 'Menu Test';
//    }


//add_submenu_page( 'my-top-level-handle', 'Page title', 'Sub-menu title', 'administrator', 'my-submenu-handle', 'my_magic_function');
//add_submenu_page('med_admin', __( 'Sublevel', MED_LOCALE ), __( 'Sublevel', MED_LOCALE ), 'administrator', 'med_sub-page', 'myeasydb_toplevel_page');

//add_menu_page(__( 'MySQL Admin', MED_LOCALE ), '', 'administrator', 'med_admin', 'myeasydb_toplevel_page', PLUGIN_LINK.'img/mysql.png');

//add_menu_page(__( 'Start Here', MED_LOCALE ), 'myeasydb', 'administrator', 'med_admin', 'myeasydb_toplevel_page', PLUGIN_LINK.'img/mysql.png');


//$this->_pageRef = add_menu_page( 'Start Here', $wp_theme_name, 'edit_themes', $this->_page, array( &$this, 'index' ) );
//add_submenu_page( $this->_page, 'Start Here', 'Start Here', 'edit_themes', $this->_page, array( &$this, 'index' ) );


	#
	#	edit page
	#
	add_pages_page(__( 'myEASYdb Edit', MED_LOCALE ), '', 'administrator', 'med_edit', 'myeasydb_edit_page');

	//// Add a submenu to the custom top-level menu:
	//add_submenu_page('med_admin', __( 'Sublevel', MED_LOCALE ), __( 'Sublevel', MED_LOCALE ), 'administrator', 'med_sub-page', 'myeasydb_sublevel_page');
	//
	//// Add a second submenu to the custom top-level menu:
	//add_submenu_page('med_admin', __( 'Sublevel 2', MED_LOCALE ), __( 'Sublevel 2', MED_LOCALE ), 'administrator', 'med_sub-page2', 'myeasydb_sublevel_page2');

	#
	#	available tables
	#
	if(defined('MAINSITE_DB') && MAINSITE_DB!='')					#	0.0.5
	{
		$rows = table_get_tables(MAINSITE_DB);
		$t = count($rows);
		for($i=0;$i<$t;$i++)
		{

//var_dump($rows[$i]);echo '<hr>';

			if($rows[$i]['Comment']!='' && substr($rows[$i]['Comment'], 0, 8)!='*PRIVATE')
			{
//echo $rows[$i]['Name'].' => '.$rows[$i]['Comment'].'<br>';
//echo '<a href="'.PLUGIN_LINK.'table_editor_adm.php?table='.$rows[$i]['Name'].'">'.$rows[$i]['Name'].' => '.$rows[$i]['Comment'].'</a><br>';

				add_submenu_page('med_admin', $rows[$i]['Comment'], $rows[$i]['Comment'], 'administrator', $rows[$i]['Name'], 'myeasydb_table_handler_page');
			}
			else
			{
				add_submenu_page('med_admin', $rows[$i]['Name'], $rows[$i]['Name'], 'administrator', $rows[$i]['Name'], 'myeasydb_table_handler_page');
			}
		}

/*
	#	0.0.6

		//if(!isset($_SESSION['myeasydbVersion']) && (!defined('AJAX_CALLER') || AJAX_CALLER==false))
		//{
		//	#	check the myEASYdb version only once per session
		//	#
		//	?><script type="text/javascript">function get_med_information() {
		//		sndReq('get_med_information','med','<?php echo 'username' . AJAX_PARMS_SPLITTER . 'pwd'; ?>');
		//		}
		//		setTimeout('get_med_information()', 500);
		//	</script><?php
		//}
*/
	}
}



#-------------------------------------------------------------------------------

function myeasydb_toplevel_page() {
	#
	#	Start here page
	#
	echo '<div class="wrap">'
		.'<div id="icon-main-page" class="icon32"><br /></div>'
		.'<h2>' . __( 'myEASYdb', MED_LOCALE ) . '</h2>'
	;
	if(defined('MED_UPGRADE_STRING'))
	{
		echo MED_UPGRADE_STRING;
	}

	require(MED_PATH.'med_startHere.php');


	echo '</div>';
}


function myeasydb_table_handler_page() {
	#
	#	Table editor page
	#
	echo '<div class="wrap">';

//echo '<code>myeasydb_table_handler_page['.$_GET['page'].','.$_GET['id'].']</code>';	#	debug

	define('IN_TABLE_DEFS', true);
	define('EDIT_TABLE_NAME', $_GET['page']);

//echo __FILE__.'|'.EDIT_TABLE_NAME.'|'.time().'<br>';


	require(MED_PATH.'table_editor_adm.php');


	echo '</div>';
}


function myeasydb_edit_page() {
	#
	#	Edit table page
	#
	echo '<div class="wrap">'
		.MYEASYDB_POPWIN
		.'<h2>' . __( 'myEASYdb: Edit Record', 'myeasydb' ) . '</h2>'
		//.MED_UPGRADE_STRING
	;

	define('IN_TABLE_DEFS', true);
	define('EDIT_TABLE_NAME', $_GET['table']);

//echo __FILE__.'|'.EDIT_TABLE_NAME.'|'.time().'<br>';


	require(MED_PATH.'table_editor.php');


	echo '</div>';
}


function myeasydb_options_page() {
	#
	#	Settings page
	#
	global $med_dates_locale_ary;

	echo '<div class="wrap">'
		.'<div id="icon-options-general" class="icon32"><br /></div>'
		.'<h2>' . __( 'myEASYdb: Settings', MED_LOCALE ) . '</h2>'
		//.MED_UPGRADE_STRING
	;


	require(MED_PATH.'med_settings.php');


	echo '</div>';
}


function myeasydb_manage_page() {
	#
	#	Tools page
	#
	echo '<div class="wrap">'
		.'<div id="icon-tools" class="icon32"><br /></div>'
		.'<h2>myEASYdb: Tools</h2>'
		//.MED_UPGRADE_STRING
	;


	require(MED_PATH.'med_tools.php');


	echo '</div>';
}


//function get_med_information($username, $password) {
//	#
//	#	get the latest version number
//	#
//	$fp = fsockopen('myeasydb.com', 80, $errno, $errstr, 10);
//
////echo '$fp['.$fp.']<br>';
////echo '$errno['.$errno.']<br>';
////echo '$errstr['.$errstr.']<br>';
//
//	if (!$fp) {
//		#
//		#	HTTP ERROR
//		#
//		$version = 'HTTP ERROR';
//		$subscription = 'HTTP ERROR';
//
//	} else {
//		#
//		#	get the latest version number
//		#
//		$header = "GET /commands/getVersion.php HTTP/1.1\r\n"
//					."Host: myeasydb.com\r\n"
//					."Connection: Close\r\n\r\n"
//		;
//		fwrite($fp, $header);
//
//		$result = '';
//		while (!feof($fp)) {
//			$result .= fgets($fp, 1024);
//		}
//
////echo '$result['.$result.']<br>';
//
//		$version = MED_VERSION;
//		$p = strpos($result, 'myEASYdb[', 0);
//		if($p!==false)
//		{
//			$beg = $p + 9;
//			$end = strpos($result, ']', $p);
//			$version = substr($result, $beg, ($end-$beg));
//		}
//
//		fclose($fp);
//		$fp = fsockopen('myeasydb.com', 80, $errno, $errstr, 10);
//
////echo '$fp['.$fp.']<br>';
////echo '$errno['.$errno.']<br>';
////echo '$errstr['.$errstr.']<br>';
//
//		#
//		#	get the subscription info
//		#
//		$req = 'u=' . urlencode(stripslashes($username)) . '&p=' . urlencode(stripslashes($password));
//
//		$header = "POST /commands/getSubscription.php HTTP/1.0\r\n"
//					."Host: myeasydb.com\r\n"
//					."Content-Type: application/x-www-form-urlencoded\r\n"
//					."Content-Length: " . strlen($req) . "\r\n\r\n"
//		;
//		fwrite($fp, $header . $req);
//
//		$result = '';
//		while (!feof($fp)) {
//			$result .= fgets($fp, 1024);
//		}
//
////echo '$req['.$req.']<br>';
////echo '$result['.$result.']<br>';
//
//		$subscription = '';
//		$p = strpos($result, 'myEASYdb[', 0);
//		if($p!==false)
//		{
//			$beg = $p + 9;
//			$end = strpos($result, ']', $p);
//			$subscription = substr($result, $beg, ($end-$beg));
//		}
//
//		fclose($fp);
//	}
//
////echo '$version['.$version.']<br>';
//
//	$info = array();
//	$info['version'] = $version;
//	$info['subscription'] = $subscription;
//
//	return $info;
//}



// myeasydb_sublevel_page() displays the page content for the first submenu
// of the custom Test Toplevel menu
//function myeasydb_sublevel_page() {
//
//	echo '<div class="wrap">';
//	echo '<h2>MySQL Admin: Sublevel</h2>';
//
//	echo '</div>';
//}


// myeasydb_sublevel_page2() displays the page content for the second submenu
// of the custom Test Toplevel menu
//function myeasydb_sublevel_page2() {
//
//	echo '<div class="wrap">';
//	echo '<h2>MySQL Admin: Sublevel 2</h2>';
//
//	echo '</div>';
//}

?>
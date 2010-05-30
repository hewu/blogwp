<?php
/**
 * Initialize configuration variables
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.6
 */

define('MED_VERSION', '0.0.6');		#	this plugin version

define('MED_LOCALE', 'myEAASYdb');	#	the locale for translations

session_start();

define('MED_PATH', dirname(__FILE__) . '/');

$myeasydb_dir = basename(dirname(__FILE__));

//echo '$myeasydb_dir['.$myeasydb_dir.']<br>';
//echo 'MED_PATH['.MED_PATH.']<br>';
//echo 'ABSPATH['.ABSPATH.']<br>';


if(AJAX_CALLER==true)
{
	#	when its called from ajax we need to load the wordpress configuration
	#
	#
	#	define needed paths and directories
	#
	$tmp = dirname(__FILE__);
	$tmp = str_replace('\\','/', $tmp);		#	0.0.6: Windows
	$tpath = explode('/',$tmp);
	$t = count($tpath) - 3;
	$wp_path = '';
	for($i=0;$i<$t;$i++)
	{
		$wp_path .= $tpath[$i] . '/';
	}

//echo '$wp_path['.$wp_path.']<br>';

	require_once($wp_path.'wp-config.php');
}

require_once(MED_PATH.'inc/MySQL.inc.php');
require_once(MED_PATH.'inc/handle_critical_errors.inc.php');


#
#	text used to split ajax parameters
#
define('AJAX_PARMS_SPLITTER', '|-ajax-parms-|');

#
#	med_settings buttons
#
define('SAVE_BTN', __('Update Options', MED_LOCALE ));
define('CREATE_BTN', __('Add missing tables', MED_LOCALE ));

#
#	myEASYdb own tables
#
define('MED_TABLE_DEFS', 'med_tables_definitions');
define('MED_FIELDS_DEFS', 'med_fields_definitions');
define('MED_TABLE_VALIDATE', 'med_validation_definitions');
define('MED_TABLE_ERRORS_LOG', 'med_critical_errors_log');

define('MED_OWN_TABLES', MED_TABLE_DEFS
						.'|'.MED_FIELDS_DEFS
						.'|'.MED_TABLE_VALIDATE
						.'|'.MED_TABLE_ERRORS_LOG
);


#
#	myEASYdb supported field types
#
define('MED_FLD_TYPE_NUMERIC',

			'TINYINT'
			.'|'.'INT'
			.'|'.'SMALLINT'
			.'|'.'MEDIUMINT'
			.'|'.'BIGINT'
			.'|'.'DECIMAL'
			//.'|'.'NUMERIC'
			//.'|'.'FLOAT'
			//.'|'.'REAL'
			//.'|'.'DOUBLE'
);

define('MED_FLD_TYPE_TIME',

			'DATETIME'
			.'|'.'DATE'
			.'|'.'TIMESTAMP'
			.'|'.'TIME'
			.'|'.'YEAR'
);

define('MED_FLD_TYPE_STRING',

			'CHAR'
			.'|'.'VARCHAR'
			//.'|'.'BINARY'
			//.'|'.'VARBINARY'
			//.'|'.'TINYBLOB'
			//.'|'.'BLOB'
			//.'|'.'MEDIUMBLOB'
			//.'|'.'LONGBLOB'
			.'|'.'TINYTEXT'
			.'|'.'TEXT'
			.'|'.'MEDIUMTEXT'
			.'|'.'LONGTEXT'
			//.'|'.'ENUM'
			//.'|'.'SET'
);

define('MED_FLD_TYPES', MED_FLD_TYPE_NUMERIC . '|' . MED_FLD_TYPE_TIME . '|' . MED_FLD_TYPE_STRING);
define('MED_FLD_DEFAULTS', '|NULL|CURRENT_TIMESTAMP|Empty String');

#
#	myEASYdb supported validation types
#
define('MED_VALIDATION_TYPES', '|EMAIL|URL|PWD');


#------------------------------------------------
#
#	plugin options (from the wordpress database)
#
#------------------------------------------------
#
#	connect to the wordpress database
#
global $dbh;
$dbh = db_connect(array(DB_HOST, DB_USER, DB_PASSWORD),__LINE__,__FILE__);
//echo $dbh.'<br>';

#
#	the database to edit
#
define('MAINSITE_DB', get_option( 'med_database' ));
//echo 'MAINSITE_DB['.MAINSITE_DB.']<br>';

#
#	the myeasydb database
#
define('MED_OWN_DB', get_option( 'med_own_database' ));
//echo 'MED_OWN_DB['.MED_OWN_DB.']<br>';

#
#	email address where to send error notifications
#
define('MED_WEBMASTER', get_option( 'med_webmaster' ));

#
#	on the production server avoid to show errors, etc.
#
define('is_PRODSERVER', get_option( 'med_isPRODUCTION' ));

#
#	switch to show/hide debug code
#
define('is_DEBUG', get_option( 'med_isDEBUG' ));

#
#	table list formatting
#
//define('TBL_LIST_CELLSPACING', '2');
//define('TBL_LIST_TR_HIGHLIGHT_ON', 'background:#eee;');
//define('TBL_LIST_TR_HIGHLIGHT_OFF', 'background:#fff;');
//define('TBL_LIST_TD_STYLE', 'padding:6px;');
//define('TBL_LIST_MAX_LENGTH', 30);

define('TBL_LIST_CELLSPACING',		get_option( 'med_tbl_list_cellspacing' ));
define('TBL_LIST_TR_HIGHLIGHT_ON',	get_option( 'med_tbl_list_tr_highlight_on' ));
define('TBL_LIST_TR_HIGHLIGHT_OFF',	get_option( 'med_tbl_list_tr_highlight_off' ));
define('TBL_LIST_TD_STYLE',			get_option( 'med_tbl_list_td_style' ));
define('TBL_LIST_MAX_LENGTH',		get_option( 'med_tbl_list_max_length' ));

#
#	link to the plugin folder (eg. http://example.com/wordpress-2.9.1/wp-content/plugins/MySQLAdmin/)
#
define(PLUGIN_LINK, get_option('siteurl').'/wp-content/plugins/' . $myeasydb_dir . '/');
//echo 'PLUGIN_LINK:'.PLUGIN_LINK.'<br>';

#
#	how many rows to show on the table list page
#
$tmp = get_option( 'med_items_per_page' );
if((int)$tmp==0) { $tmp = 10; }
define('ITEMS_PER_PAGE', $tmp);

#
#	how to represent dates
#
global $med_dates_locale_ary;
$med_dates_locale_ary = array();
$med_dates_locale_ary['e'] = 'European (d/m/y)';
$med_dates_locale_ary['a'] = 'American (m/d/y)';

$tmp = get_option( 'med_dates_locale' );
switch($tmp=='e')
{
	case 'e';
		#
		#	European
		#
		define('DATE_FORMAT', 'd/m/Y');
		define('DATE_TIME_FORMAT', 'd/m/Y H:i:s');
		define('DATE_CALENDAR', 'dd/mm/yyyy');
		define('DATETIME_CALENDAR', 'dd/mm/yyyy hh:ii');
		break;
		#
	case 'a';
	default:
		#
		#	American
		#
		define('DATE_FORMAT', 'm/d/Y');
		define('DATE_TIME_FORMAT', 'm/d/Y H:i:s');
		define('DATE_CALENDAR', 'mm/dd/yyyy');
		define('DATETIME_CALENDAR', 'mm/dd/yyyy hh:ii');
		break;
		#
}


///////////////////////////
// TODO: MOVE TO OPTIONS //
///////////////////////////
#
#	handle tables differently if they depend on a specific language (to be implemented)
#
define('IS_LANG_INDEPENDENT', true);
///////////////////////////
// TODO: MOVE TO OPTIONS //
///////////////////////////


#
#	handling error messages
#
if(is_PRODSERVER==true)
{
	ini_set('log_errors','On');
	ini_set('display_errors','0');
}
else
{
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('log_errors','Off');
	ini_set('display_errors','1');
}

//var_dump($_SERVER);


#
#	help
#
define('RELATIONS_ADD', '<b>Immagini</b>.<br />'
	.'Utilizzare i seguenti formati:<ul>'
		.'<li>larghezza=200px, altezza=230px : per le immagini della pagina "elenco prodotti" </li>'
		.'<li>larghezza=223px, altezza=350px : per le immagini della pagina "prodotto"</li>'
	.'</ul>'
	.'Per immagini senza sfumature è possibile utilizzare il formato JPEG preparandole con uno sfondo completamente bianco,<br />'
	.'in alternativa è possibile utilizzare il formato PNG a 24 bit che consente di sfruttare la trasparenza ed inserire sfumature.'
);


?>
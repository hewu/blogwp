<?php
/**
 * Common functions
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.6
 */

global $isFilteredData;

define('FILTER_SPLITTER', '|-filter-|');

define('ARY_KEY2VAL_SPLITTER','|--K2V--|');
define('ARY_REC_SPLITTER','|--EOR--|');

define('TODO','<p class=todo>[[DATA]]</p>');

#
#	fields managed automatically by myeasydb
#
global $_RESERVED_FIELDS;
$_RESERVED_FIELDS = array(
	'*PRIVATE',
	'RRN',
	'ID',
	'LAST_USER',
	'LAST_UPDATE'
);

#
#	buttons labels
#
define('BTN_ADD',		__( 'Add a record', MED_LOCALE ));
define('_PREVPG_',		__( 'Previous page', MED_LOCALE ));		#	0.0.5
define('_NEXTPG_',		__( 'Next page', MED_LOCALE ));			#	0.0.5
define('EDIT',  		__( 'Edit', MED_LOCALE ));				#	0.0.5
define('DELETE', 		__( 'Delete', MED_LOCALE ));			#	0.0.5
define('RELOAD',		__( 'Reload', MED_LOCALE ));			#	0.0.5
define('SAVE',  		__( 'Save', MED_LOCALE ));				#	0.0.5
define('TO_TBL_LIST',	__( 'Table contents', MED_LOCALE ));	#	0.0.5
define('FILTER',  		__( 'Filter', MED_LOCALE ));			#	0.0.6

if(!defined('EDIT_MENU_SEPARATOR'))
{
	define(EDIT_MENU_SEPARATOR, '<img src="'.PLUGIN_LINK.'img/modules/separator.png" alt="|" align="absmiddle" />');
}

define('MISSING_TABLE_DEFS',
	'<div class="warningBox" style="padding:12px;">'
//	.'<p>NO definitions found for table "'.$TABLE.'"</p>'
//	.'<br />'
	.'<p>Definitions for the generic table administrator found on this system (¹):</p>'
	.'[[TABLES_LIST]]'
	.'<p class="sml" style="margin-top:8px;">(¹) Click on the definition link(s) to edit the table</p>'
	.'<p style="margin-top:12px;">Definitions are searched in the following order:</p>'
	.'<ul>'
		.'<li style="margin-bottom:6px;">Skin own definitions:<br />'.SITE_ROOT.SKIN_PATH.'tables/*.inc.php</li>'
		.'<li>phpCAMALEO shared definitions:<br />'.SITE_ROOT.'common/tables/*.inc.php</li>'
	.'</ul>'
	.'<p style="margin-top:12px;margin-bottom:8px;">Be sure that the definitions you are trying to use includes the following PHP statement:</p>'
	.'<code style="color:#333;background:#fff;padding:6px;font-size:12px;">define(\'IN_TABLE_DEFS\', true);</code>'
.'</div>');


define('TABLE_ADM_ROWS_BUTTONS',
	'<td width="1%" nowrap style="'.TBL_LIST_TD_STYLE.'">'
		.'<img width="16" title="'.EDIT.'" alt="'.EDIT.'" src="'.PLUGIN_LINK.'img/modules/edit-off.png" '
			.'onclick="javascript:'
						//.'window.location=\'/?page=table_editor&table=[[EDIT_TABLE_NAME]]&id=[[RRN]]\';" '
						.'window.location=\'?page=med_edit&table=[[EDIT_TABLE_NAME]]&id=[[RRN]]\';" '
			.'style="cursor:pointer;" '
			.'onmouseover="javasript:this.src=\''.PLUGIN_LINK.'img/modules/edit-on.png\';" '
			.'onmouseout="this.src=\''.PLUGIN_LINK.'img/modules/edit-off.png\'" '
		.' />'
	.'<img width="16" title="'.DELETE.'" alt="'.DELETE.'" src="'.PLUGIN_LINK.'img/modules/delete-off.png" '
		.'onclick="javascript:'
						.'if(confirm(\''.__( 'Are you sure that you want to delete this record?', MED_LOCALE ).'\')==false) {'
							.'return false;'
						.'};'
						.'document.table_editor_adm._action.value=\''.DELETE.'\';'
						.'document.table_editor_adm.id.value=\'[[RRN]]\';'
						.'document.table_editor_adm.submit();'
					.'" '
			.'style="cursor:pointer;margin-left:8px;" '
			.'onmouseover="javasript:this.src=\''.PLUGIN_LINK.'img/modules/delete-on.png\';" '
			.'onmouseout="this.src=\''.PLUGIN_LINK.'img/modules/delete-off.png\'" '
		.' />'
	.'</td>'
);

global $tr_sel_bg, $tr_2move_bg, $btn_move2_bg, $btn_moveo_bg;


// todo: implement ordering and move in the options
//
$tr_sel_bg = '#eee';		#	color of the table rows
$tr_2move_bg = '#bbb';		#	color of the row marked as to be moved
$btn_move2_bg = '#ef931d';	#	color of the selected move to button
$btn_moveo_bg = '#106d03';	#	color of the other move to buttons


//if(file_exists(SITE_ROOT.SKIN_PATH.'tables/'.$TABLE.'.inc.php'))
//{
//	include(SITE_ROOT.SKIN_PATH.'tables/'.$TABLE.'.inc.php');
//}
//elseif(file_exists(SITE_ROOT.'common/tables/'.$TABLE.'.inc.php'))
//{
//	include(SITE_ROOT.'common/tables/'.$TABLE.'.inc.php');
//}

#
#	fields definitions for this table
#
if(defined('EDIT_TABLE_NAME'))
{
	global $_TBL_DEFS, $_T_TBL_DEFS, $_FLD_DEFS, $_T_FLD_DEFS, $_FIELDS, $_T_FIELDS;

	$_T_TBL_DEFS = 0;
	$_TBL_DEFS = get_table_defs(EDIT_TABLE_NAME);
	if(is_array($_TBL_DEFS))
	{
		$_T_TBL_DEFS = count($_TBL_DEFS);
	}

	$_T_FLD_DEFS = 0;
	$_FLD_DEFS = get_fields_defs(EDIT_TABLE_NAME);
	if(is_array($_FLD_DEFS))
	{
		$_T_FLD_DEFS = count($_FLD_DEFS);
	}

	$_FIELDS = table_get_fields(MAINSITE_DB, EDIT_TABLE_NAME);
	$_T_FIELDS = count($_FIELDS);

	$i = 0;
	$autoincrement = '';
	$table_key = '';							#	0.0.4
	$isLAST_USER = false;						#	0.0.2
	//while($i<$_T_FIELDS && $table_key=='')	#	0.0.2
	while($i<$_T_FIELDS)						#	0.0.2
	{
		#	let's get the autoincremented field, usually the record ID (or, as I use to call it, the RRN -- Relative Record Number)
		#

		#
		#	0.0.4: BEG
		#-------------
		if($_FIELDS[$i]['Extra']=='auto_increment')
		{
			$autoincrement = $_FIELDS[$i]['Field'];
		}
		if($_FIELDS[$i]['Key']!='')
		{
			$table_key .= $_FIELDS[$i]['Field'].'|';
		}
		#-------------
		#	0.0.4: END
		#

		if($_FIELDS[$i]['Field']=='LAST_USER')		#	0.0.2
		{
			$isLAST_USER = true;
		}

		$i++;
	}
	#
	#	0.0.4: BEG
	#-------------
	$table_key = substr($table_key,0,-1);
	if(strlen($autoincrement)>0)
	{
		$table_key = $autoincrement;
	}
	#-------------
	#	0.0.4: END
	#

	define('EDIT_TABLE_RRN_FIELD', $table_key);
	define('EDIT_TABLE_IS_LAST_USER', $isLAST_USER);	#	0.0.2
	array_push($_RESERVED_FIELDS, $table_key);
}

//echo '$autoincrement['.$autoincrement.']<br>';
//echo '$table_key['.$table_key.']<br>';

//echo '<code>The record key in use for this table is: <b>'.EDIT_TABLE_RRN_FIELD.'</b></code><br />';	#debug

if(!defined('EDIT_TABLE_DESC'))
{
	$info = table_get_table_info(MAINSITE_DB, EDIT_TABLE_NAME);
	$desc = $info[0]['Comment'];
	if($desc=='')
	{
		$desc = $info[0]['Table'];
	}
	define('EDIT_TABLE_DESC', $desc);
}

//var_dump($info);


//echo __FILE__.'|'.EDIT_TABLE_NAME.'|'.$_T_FIELDS.'|'.time().'<br>';

//include_once(SITE_ROOT.'common/fun2inc/get_last_table_update.inc');
//include_once(SITE_ROOT.'common/fun2inc/get_site_languanges.inc');

if(!defined('IN_TABLE_DEFS'))
{
//	#	prepare a list with the available definitions
//	#
//	include_once(SITE_ROOT.'common/fun2inc/get_dir_list.inc');
//	#
//	$tmp1 = get_dir_list(SITE_ROOT.SKIN_PATH.'tables');
//	if(is_array($tmp1))
//	{
//		foreach($tmp1 as $key => $data)
//		{
//			if(substr($data,-4)=='.php')
//			{
////echo $data.'<br>';
//				$tmp1[$key] = SKIN_PATH.'tables/'.$data;
//			}
//		}
//	}
//	#
//	$tmp2 = get_dir_list(SITE_ROOT.'common/tables');
//	if(is_array($tmp2))
//	{
//		foreach($tmp2 as $key => $data)
//		{
//			if(substr($data,-4)=='.php')
//			{
////echo $data.'<br>';
//				$tmp2[$key] = 'common/tables/'.$data;
//			}
//		}
//	}
//	#
//	if(is_array($tmp1) && is_array($tmp2))
//	{
//		$tmp = array_merge($tmp1, $tmp2);
//	}
//	if(is_array($tmp1) && !is_array($tmp2))
//	{
//		$tmp = $tmp1;
//	}
//	if(!is_array($tmp1) && is_array($tmp2))
//	{
//		$tmp = $tmp2;
//	}
//	#
//	$tables_list = '';
//	if(is_array($tmp))
//	{
//		rsort($tmp);
//
//		$last_dir = '';
//		$one_dir = false;
//		foreach($tmp as $key => $data)
//		{
//			if($last_dir!=dirname($data))
//			{
//				if($one_dir==true) { $tables_list .= '</ul>'; }
//				$tables_list .= '<ul style="margin-top:8px;">';
//				$last_dir = dirname($data);
//				$one_dir = true;
//			}
//			if(substr($data,-4)=='.php')
//			{
//				$tables_list .= '<li><a href="/?page=table_editor_adm&table='.basename($data,'.inc.php').'">'.$data.'</a></li>';
//			}
//		}
//		$tables_list .= '</ul>';
//	}
//	if($tables_list=='') { $tables_list = 'No definitions found.'; }
//	echo str_replace('[[TABLES_LIST]]', $tables_list, MISSING_TABLE_DEFS);

	//#
	//#	available tables
	//#
	//$rows = table_get_tables(MAINSITE_DB);
	//$t = count($rows);
	//for($i=0;$i<$t;$i++)
	//{
	//	if($rows[$i]['Comment']!='' && substr($rows[$i]['Comment'], 0, 8)!='*PRIVATE')
	//	{
	//		echo $rows[$i]['Name'].' => '.$rows[$i]['Comment'].'<br>';
	//	}
	//}

}


if(!defined('EDIT_TABLE_RRN_FIELD'))
{
	#	name of the unique id table field
	#
	//define('EDIT_TABLE_RRN_FIELD', 'RRN');				#	0.0.4
	die('Huston, we do not know they key for this table');	#	0.0.4
}

if(!defined('IS_LANG_INDEPENDENT'))
{
	#	true = the table data does not depend on language
	#
	define('IS_LANG_INDEPENDENT', false);
}

#===============================================================================
#
#	the following functions are the core myeasydb functions, common to the
#	entire system
#
#===============================================================================

#-------------------------------------------------------------------------------
#
#	handling dates
#
#-------------------------------------------------------------------------------
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function bld_checkbox($value, $check, $name) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	build a checkbox
	#
	$tmp = 0;
	$checked = '';

	if($value==$check) { $tmp = 1; $checked = ' checked="checked"'; }
	$html = '<input type="checkbox" name="'.$name.'" value="'.$tmp.'"'.$checked.' />';

	return $html;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function show_calendar($form_name,			#	the form name
					   $field_name,			#	the field name
					   $isTime = false,		#	add time controls
					   $reset = false,		#	add reset button
					   $fromto = ''			#	0.0.6 if there is a related date (like a from/to date on filters page)
											#	set this to the related field name
					   ) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	show a date in a readonly field with a calendar icon letting the user
	#	to enter the date by selecting it from the calendar
	#
	#	works on a modified version of DTHMLGoodies Calendar
	#	original version available at:
	#	http://www.dhtmlgoodies.com/scripts/js_calendar/js_calendar.html
	#
	if($isTime==false)
	{
		$type = DATE_CALENDAR;
		$size = 10;
		$displayTime = 'false';
	}
	else
	{
		$type = DATETIME_CALENDAR;
		$size = 16;
		$displayTime = 'true';
	}

	if($fromto=='')				#	0.0.6
	{
		$html = ''
			.'<img src="'.PLUGIN_LINK.'img/calendar/cal.png" style="cursor:pointer;" align="absmiddle" title="'.__( 'Choose the date', MED_LOCALE ).'" '
					.'onclick="javascript:displayCalendar(document.'.$form_name.'.'.$field_name.',\''.$type.'\',this,'.$displayTime.');" /> '
			.' <input class="readonly" readonly tabindex="-1" type="text" id="'.$field_name.'" name="'.$field_name.'" style="cursor:pointer;" '	#	0.0.6
					.'onclick="javascript:displayCalendar(document.'.$form_name.'.'.$field_name.',\''.$type.'\',this,'.$displayTime.');" '		#	0.0.6
					.'size="'.$size.'" value="'.$_POST[$field_name].'" />'
		;
	}
	else						#	0.0.6
	{
		$html = ''
			.'<img src="'.PLUGIN_LINK.'img/calendar/cal.png" style="cursor:pointer;" align="absmiddle" title="'.__( 'Choose the date', MED_LOCALE ).'" '
					.'onclick="javascript:displayCalendar(document.'.$form_name.'.'.$field_name.',\''.$type.'\',this,'.$displayTime.');'
										.'set_date(\''.$field_name.'\',\''.$fromto.'\');'
										//.'document.getElementById(\''.$fromto.'\').value=this.value;'
							.'" /> '
			.' <input class="readonly" readonly tabindex="-1" type="text" id="'.$field_name.'" name="'.$field_name.'" style="cursor:pointer;" '
					.'onclick="javascript:displayCalendar(document.'.$form_name.'.'.$field_name.',\''.$type.'\',this,'.$displayTime.');'
										.'set_date(\''.$field_name.'\',\''.$fromto.'\');'
										//.'document.getElementById(\''.$fromto.'\').value=this.value;'
							.'" '
					.'size="'.$size.'" value="'.$_POST[$field_name].'" />'
		;
	}

	if($reset==true)
	{
		if($fromto=='')			#	0.0.6
		{
			$html .= '<input type="button" class="button-secondary" style="margin-left:8px;" value="' . __('Reset', MED_LOCALE ) . '" '
							.'onclick="javascript:document.getElementById(\''.$field_name.'\').value=\'\';" '
						.' />'
			;
		}
		else					#	0.0.6
		{
			$html .= '<input type="button" class="button-secondary" style="margin-left:8px;" value="' . __('Reset', MED_LOCALE ) . '" '
							.'onclick="javascript:document.getElementById(\''.$field_name.'\').value=\'\';'
												.'document.getElementById(\''.$fromto.'\').value=\'\';" '
						.' />'
			;
		}
	}

	return $html;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function format_date($date) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	input:	date from a table record
	#	output:	date formatted on the user preferences
	#
	if($date=='' || $date=='0000-00-00')
	{
		return false;
	}
	return date(DATE_FORMAT, strtotime($date));
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function format_datetime($datetime) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	input:	datetime from a table record
	#	output:	datetime formatted on the user preferences
	#
	if($datetime=='' || $datetime=='0000-00-00 00:00:00')
	{
		return false;
	}
	return date(DATE_TIME_FORMAT, strtotime($datetime));
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function format_calendar_to_date($date) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	input:	date formatted for the screen
	#	output:	date ready to be inserted into a table record
	#
	#	DATE_CALENDAR must be defined using the following constants for every
	#	language -- meaning do NOT localize!:
	#
	#		dd			= to represent the position of the day
	#		mm			= to represent the position of the month
	#		yyyy		= to represent the position of the year
	#		{separator}	= any separator the user wants to use
	#
	#	Examples:
	#		define('DATE_CALENDAR', 'dd/mm/yyyy');	# European
	#		define('DATE_CALENDAR', 'dd.mm.yyyy');	# European
	#		define('DATE_CALENDAR', 'mm/dd/yyyy');	# American
	#		define('DATE_CALENDAR', 'mm-dd-yyyy');	# American
	#
	if($date=='' || $date=='0000-00-00')
	{
		return false;
	}

	$d = -1;
	$m = -1;
	$y = -1;

	$p = strpos(DATE_CALENDAR, 'dd', 0);
	if($p!==false)
	{
		$d = $p;
	}
	$p = strpos(DATE_CALENDAR, 'mm', 0);
	if($p!==false)
	{
		$m = $p;
	}
	$p = strpos(DATE_CALENDAR, 'yyyy', 0);
	if($p!==false)
	{
		$y = $p;
	}

	if($d==-1 || $m==-1 || $y==-1)
	{
		return false;
	}

	$date = substr($date, $y, 4).'-'.substr($date, $m, 2).'-'.substr($date, $d, 2);

	return date('Y-m-d', strtotime($date));
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function format_calendar_to_datetime($datetime) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	input:	datetime formatted for the screen
	#	output:	datetime ready to be inserted into a table record
	#
	#	DATE_CALENDAR must be defined using the following constants for every
	#	language -- meaning do NOT localize!:
	#
	#		dd			= to represent the position of the day
	#		mm			= to represent the position of the month
	#		yyyy		= to represent the position of the year
	#		{separator}	= any separator the user wants to use
	#
	#	Examples:
	#		define('DATE_CALENDAR', 'dd/mm/yyyy');	# European
	#		define('DATE_CALENDAR', 'dd.mm.yyyy');	# European
	#		define('DATE_CALENDAR', 'mm/dd/yyyy');	# American
	#		define('DATE_CALENDAR', 'mm-dd-yyyy');	# American
	#
	#	TODO: need to finalize this one!
	#
	if($datetime=='' || $datetime=='0000-00-00 00:00:00')
	{
		return false;
	}

	$d = -1;
	$m = -1;
	$y = -1;

	$p = strpos(DATE_CALENDAR, 'dd', 0);
	if($p!==false)
	{
		$d = $p;
	}
	$p = strpos(DATE_CALENDAR, 'mm', 0);
	if($p!==false)
	{
		$m = $p;
	}
	$p = strpos(DATE_CALENDAR, 'yyyy', 0);
	if($p!==false)
	{
		$y = $p;
	}

	if($d==-1 || $m==-1 || $y==-1)
	{
		return false;
	}

	$date = substr($date, $y, 4).'-'.substr($date, $m, 2).'-'.substr($date, $d, 2);

	return date('Y-m-d h:i:s', strtotime($date));
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function show_tip($text) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	show a tooltip
	#
	if(defined($text))
	{
		$text = constant($text);
	}

	$inp = array();		$out = array();
	$inp[] = '"';		$out[] = '\"';
	$inp[] = "'";		$out[] = "\'";
	$inp[] = "\r\n";	$out[] = '<br />';
	$inp[] = "\n";		$out[] = '';
	$tip = str_replace($inp, $out, $text);

	$tip = htmlspecialchars($tip);

	$html = '<span style="cursor:pointer;" onmouseover="javacript:showTooltip(event,\''.$tip.'\');" onmouseout="javacript:hideTooltip();">'
				.'{tip}'
		.'</span>'
	;
	return $html;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function get_field_type_clean($field_type) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	return the field type cleaning its length
	#	example: "varchar(8)" is returned as "varchar"
	#
	$p = strpos($field_type, '(', 0);
	if($p!==false)
	{
		return strtoupper(substr($field_type, 0, $p));
	}
	return strtoupper($field_type);
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function is_field_numeric($field_type) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	return true if the field is numeric
	#	@since 0.0.4
	#
	$p = strpos(MED_FLD_TYPE_NUMERIC, strtoupper($field_type), 0);
	if($p!==false)
	{
		return true;
	}
	return false;
}
#-------------------------------------------------------------------------------
#
#	pagination
#
#-------------------------------------------------------------------------------
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function prep_prev_next($page,
						$tot_pages,
						$page_link,
						$hist_link = '',
						$items_per_page = ''	#	0.0.4
		) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	global $lang, $TABLE, $filters, $filters_relations;					#	0.0.6

	if($items_per_page=='') { $items_per_page = ITEMS_PER_PAGE; }		#	0.0.4

//echo '<hr>';
//echo '<p class="todo">page='.$page.'</p>';
//echo '<p class="todo">tot_pages='.$tot_pages.'</p>';
//echo '<hr>';


	#	prepare the string to handle pagination links
	#	$page start with 0 (MySQL needs) but the first value shown is 1
	#
	$page_disp = '';
	if($page>0)				{ $prev_link = $page_link; }
	if($page+1<$tot_pages)	{ $next_link = $page_link; }


	//if($hist_link)
	//{
	//	#	return to caller page button
	//	#
	//	$hist_link = '&middot;&nbsp;'
	//		.'<img id="hand" style="margin:0 0 0 2px;" src="'.PLUGIN_LINK.'img/paginate/hmnu_rld.png" title="'._BACKPG_.'" width="19" height="18" '
	//			.'onmouseover="javasript:this.src=\''.PLUGIN_LINK.'img/paginate/hmnu_rld-on.png\';" '
	//			.'onmouseout="this.src=\''.PLUGIN_LINK.'img/paginate/hmnu_rld.png\'" '
	//			.'onmousedown="this.src=\''.PLUGIN_LINK.'img/paginate/hmnu_rld-cl.png\';" '
	//			.'onclick="javascript:'.$hist_link.'" '
	//		.'align="absmiddle" target="main" />'
	//	;
	//}


	if(	$page <= 0              && $tot_pages > 1 ||
		$page > 0               && $page + 1 < $tot_pages ||
		$page + 1 >= $tot_pages && $tot_pages > 1 ||
		$page + 1 > 1           && $tot_pages > 1 && $page_disp=='' )
	{
		$page_disp = '<span class="paginate_bar" style="font-weight:bold;font-size:12px;">' . ($page + 1) . '/' . $tot_pages . '</span>';
	}
	else
	{
		#	there is only one page
		#
		//$page_disp = '1/1';
		return;
	}

	#
	#	no ajax
	#
	#	$js_prev = htmlentities("javascript:window.location='$prev_link"."&paginate=prev';return false;");
	#	$js_next = htmlentities("javascript:window.location='$next_link"."&paginate=next';return false;");

	#
	#	ajax
	#
	//$js_prev = htmlentities("javascript:sndReq('table_adm','recordList','".$lang.AJAX_PARMS_SPLITTER.$TABLE.AJAX_PARMS_SPLITTER.'prev'.AJAX_PARMS_SPLITTER.$filters."');return false;");	#	0.0.4
	//$js_next = htmlentities("javascript:sndReq('table_adm','recordList','".$lang.AJAX_PARMS_SPLITTER.$TABLE.AJAX_PARMS_SPLITTER.'next'.AJAX_PARMS_SPLITTER.$filters."');return false;");	#	0.0.4

	$js_prev = htmlentities("javascript:sndReq('table_adm','recordList','"	.$lang.AJAX_PARMS_SPLITTER
																			.$TABLE.AJAX_PARMS_SPLITTER
																			.'prev'.AJAX_PARMS_SPLITTER
																			.$filters.AJAX_PARMS_SPLITTER
																			.$items_per_page.AJAX_PARMS_SPLITTER		#	0.0.4
																			.$filters_relations."');return false;");	#	0.0.6

	$js_next = htmlentities("javascript:sndReq('table_adm','recordList','"	.$lang.AJAX_PARMS_SPLITTER
																			.$TABLE.AJAX_PARMS_SPLITTER
																			.'next'.AJAX_PARMS_SPLITTER
																			.$filters.AJAX_PARMS_SPLITTER
																			.$items_per_page.AJAX_PARMS_SPLITTER		#	0.0.4
																			.$filters_relations."');return false;");	#	0.0.6

	#
	#	previous page button
	#
	if($page > 0)
	{
		$page_disp = '<img style="margin:0 3px 0 0;cursor:pointer;" src="'.PLUGIN_LINK.'img/paginate/hmnu_prv.png" title="'._PREVPG_.'" width="19" height="18" '
						.'onmouseover="this.src=\''.PLUGIN_LINK.'img/paginate/hmnu_prv-on.png\'" '
						.'onmouseout="this.src=\''.PLUGIN_LINK.'img/paginate/hmnu_prv.png\'" '
						.'onmousedown="this.src=\''.PLUGIN_LINK.'img/paginate/hmnu_prv-cl.png\';" '
						.'onclick="'.$js_prev.'" '
					.'align="absmiddle" />'.$page_disp
		;
	}
	else
	{
		$page_disp = '<img src="'.PLUGIN_LINK.'img/paginate/hmnu_prv-na.png" width="19" height="18" style="margin:0 3px 0 0;" align="absmiddle" />'.$page_disp;
	}
	#
	#	next page button
	#
	if($page + 1 < $tot_pages)
	{
		$page_disp .= '<img style="margin:0 3px 0 4px;cursor:pointer;" src="'.PLUGIN_LINK.'img/paginate/hmnu_nxt.png" title="'._NEXTPG_.'" width="19" height="18" '
						.'onmouseover="this.src=\''.PLUGIN_LINK.'img/paginate/hmnu_nxt-on.png\'" '
						.'onmouseout="this.src=\''.PLUGIN_LINK.'img/paginate/hmnu_nxt.png\'" '
						.'onmousedown="this.src=\''.PLUGIN_LINK.'img/paginate/hmnu_nxt-cl.png\';" '
						.'onclick="'.$js_next.'" '
					.'align="absmiddle" />'
		;
	}
	else
	{
		$page_disp .= '<img src="'.PLUGIN_LINK.'img/paginate/hmnu_nxt-na.png" width="19" height="18" style="margin:0 3px 0 4px;" align="absmiddle" />';
	}

	$html = '<div style="clear:both;height:26px;width:auto;text-align:center;background:url('.PLUGIN_LINK.'img/fav.png) repeat-x #89877b;-moz-border-radius:3px;border-radius:3px;">'
				.'<div style="padding:3px 6px 0 6px;">'

					.'<div style="float:left;">'

						//.' [jump to first page] '
						.$page_disp
						//.'[jump to last page] '

						.$hist_link

					.'</div>'
					.'<div style="float:right;">'

						.'<span class="paginate_bar">' . __( 'Go to page', MED_LOCALE ) . '</span> <span class="paginate_bar">&raquo;</span> '

						.'<select class="paginate_bar" name="goto" id="gotoPage" onchange="'
									.htmlentities("javascript:sndReq('table_adm','recordList','"
													.$lang.AJAX_PARMS_SPLITTER
													.$TABLE.AJAX_PARMS_SPLITTER
													.'\'+(this.value-1)+\''.AJAX_PARMS_SPLITTER
													.$filters.AJAX_PARMS_SPLITTER			#	0.0.4
													.$items_per_page."');return false;")	#	0.0.4
								.';">'
	;
						for($i=1;$i<=$tot_pages;$i++)
						{
							$selected = '';
							if($i==($page+1))
							{
								$selected = ' selected="selected"';
							}
							$html .= '<option value="'.$i.'"'.$selected.'>'.$i.'&nbsp;</option>';
						}
	$html .= ''
						.'</select>'

						.'<select class="paginate_bar" style="margin-left:12px;" name="lines" '
								.'onchange="'
									//.'alert(\'todo\')'								#	0.0.4
									.'document.getElementById(\'gotoPage\').value=0;'	#	0.0.5
									.htmlentities("javascript:sndReq('table_adm','recordList','"
													.$lang.AJAX_PARMS_SPLITTER
													.$TABLE.AJAX_PARMS_SPLITTER
													//.'\'+(document.getElementById(\'gotoPage\').value-1)+\''.AJAX_PARMS_SPLITTER	#	0.0.5
													.'0'.AJAX_PARMS_SPLITTER			#	0.0.5
													.$filters.AJAX_PARMS_SPLITTER		#	0.0.4
													.'\'+this.value'					#	0.0.4
												.");return false;")
							.';">'
	;
						for($i=10;$i<110;$i=$i+10)
						{
							$selected = '';
							if($i==$items_per_page)
							{
								$selected = ' selected="selected"';
							}
							$html .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
						}
	$html .= ''
						.'</select>'

						. ' <span class="paginate_bar">&laquo;</span> <span class="paginate_bar">' . __( 'Records per page', MED_LOCALE ) . '</span>'

					.'</div>'

				.'</div>'
		.'</div>'
	;
	return $html;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//function calc_query_limits($thisPage, $paginate, $TABLE) {					#	0.0.4
function calc_query_limits($thisPage, $paginate, $TABLE, $items_per_page='') {	#	0.0.4
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	recalculate the query limits to paginate
	#
	#	get the total number of records in the table and calculate the total number of pages
	#
	global $filters, $TABLE_ADM_PARAM, $filters_relations;				#	0.0.6

	if($items_per_page=='') { $items_per_page = ITEMS_PER_PAGE; }		#	0.0.4

//echo '<p class="todo">tot_pages='.$_SESSION[EDIT_TABLE_NAME.'_paginate']['tot_pages'].'</p>';

	if($TABLE_ADM_PARAM)
	{
		$rows = get_table_data('*info', '', $TABLE, $filters, $TABLE_ADM_PARAM);
	}
	else
	{
		//$rows = get_table_data('*info', '', $TABLE, $filters);					#	0.0.6
		$rows = get_table_data('*info', '', $TABLE, $filters, $filters_relations);	#	0.0.6
	}

	//$tot_pages = (int)($rows / ITEMS_PER_PAGE + 0.999);	#	0.0.4
	$tot_pages = (int)($rows / $items_per_page + 0.999);	#	0.0.4

//echo '<p class="todo">$rows='.$rows.'</p>';
//echo '<p class="todo">$items_per_page='.$items_per_page.'</p>';
//echo '<p class="todo">$tot_pages='.$tot_pages.'</p>';

	#
	#	manage pagination
	#
	$act_page	= $_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage'];

//echo '<p class="todo">IN $act_page='.$act_page.'</p>';

	//$limits 	= '0,'.ITEMS_PER_PAGE;	#	0.0.4
	$limits 	= '0,'.$items_per_page;	#	0.0.4

	if($paginate=='next' && $act_page<($tot_pages-1))
	{
		$act_page++;
		//$limits = ($act_page * ITEMS_PER_PAGE).','.ITEMS_PER_PAGE;	#	0.0.4
		$limits = ($act_page * $items_per_page).','.$items_per_page;	#	0.0.4
	}
	elseif($paginate=='prev' && $act_page>0)
	{
		$act_page--;
		//$limits = ($act_page * ITEMS_PER_PAGE).','.ITEMS_PER_PAGE;	#	0.0.4
		$limits = ($act_page * $items_per_page).','.$items_per_page;	#	0.0.4
	}
	elseif(is_numeric($paginate))
	{
		$act_page = $paginate;
		//$limits = ($act_page * ITEMS_PER_PAGE).','.ITEMS_PER_PAGE;	#	0.0.4
		$limits = ($act_page * $items_per_page).','.$items_per_page;	#	0.0.4
	}

	$_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage']	= $act_page;
	$_SESSION[EDIT_TABLE_NAME.'_paginate']['tot_pages']	= $tot_pages;

//echo '<p class="todo">OUT $act_page='.$act_page.'</p>';
//echo '<p class="todo">$thisPage='.$thisPage.'</p>';
//echo '<p class="todo">items_per_page='.ITEMS_PER_PAGE.'</p>';
//echo '<p class="todo">thispage='.$_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage'].'</p>';
//echo '<p class="todo">tot_pages='.$_SESSION[EDIT_TABLE_NAME.'_paginate']['tot_pages'].'</p>';
//echo '<p class="todo">$limits='.$limits.'</p>';


	return $limits;
}

#-------------------------------------------------------------------------------
#
#	handling fields data
#
#-------------------------------------------------------------------------------
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function get_array_from_string($string) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	split a constants delimited string into an array
	#
//optionsGroupID|--K2V--|1|--EOR--|

	$tmp = explode(ARY_REC_SPLITTER, $string);
	#
	#	remove the last, empty, element
	#
	$t = count($tmp);
	unset($tmp[($t-1)]);
	#
	$t = count($tmp);
	$ajax_posted = array();
	for($i=0;$i<$t;$i++)
	{
		$data = explode(ARY_KEY2VAL_SPLITTER, $tmp[$i]);
//echo '$data:<br>';var_dump($data);echo '<hr>';

		$tt = count($data);
		for($ii=0;$ii<$tt;$ii=$ii+2)
		{
//echo $ii.'||$ajax_posted['.$data[$ii].'] = '.$data[($ii+1)].'<br>';

			$ajax_posted[$data[$ii]] = $data[($ii+1)];
		}
	}
//echo '$ajax_posted:<br>';var_dump($ajax_posted);echo '<hr>';
	return $ajax_posted;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function array2post($array) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	load an array into the form
	#
	if(!is_array($array)) { return false; }
	#
	if(is_array($array))
	{
		foreach($array as $key => $val)
		{
			$_POST[$key] = $val;
		}
	}
	return true;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function post2session($t) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	save post data into the session
	#
	if(!is_numeric($t)) { return false; }
	#
	for($i=1;$i<=$t;$i++)
	{
		$_SESSION['table_editor']['tab_'.$i] = set_post2string($i);
	}
	return true;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function clear_tab_data($t) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	clear session tab data
	#
	if(!is_numeric($t)) { return false;}
	#
	for($i=1;$i<=$t;$i++)
	{
		unset($_SESSION['table_editor']['tab_'.$i]);
	}
	return true;
}

#-------------------------------------------------------------------------------
#
#	handling fields informations
#
#-------------------------------------------------------------------------------
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function table_editor_get_field_len_info($field_type) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	#	determine some useful info about a field
	#
	$beg = 0;
	$end = 0;
	#
	#	search for the "(", the field definition is something like: varchar(64), int(3), decimal(6,2), etc.
	#
	$p = strpos($field_type, '(', 0);
	if($p!==false)
	{
		$beg = $p+1;
		$p = strpos($field_type, ')', $beg);
		if($p!==false)
		{
			$end = $p-1;
		}
	}

	$len = 0;
	$int = 0;				#	field length
	$dec = 0;				#	field decimals (if any, otherwise is equal zero)
	$isTEXT = false;		#	true when the field is a text, tinytext, etc.
	$isSTRING = false;		#	true when the field is a varchar, char, etc.
	$isUNSIGNED = false;	#	true when the field is an unsigned

	if($beg>0)
	{
		$len = substr($field_type, $beg, ($end-$beg+1));
//echo $field_type.' {'.$beg.','.$end.'} len='.$len.' &raquo; ';#.'<br>';

		list($int, $dec) = explode(',', $len);

		$dec = (int)$dec;

		$p = strpos($field_type, 'char', 0);
		if($p!==false)
		{
			$isSTRING = true;
		}
//echo 'int='.$int.', dec='.$dec.'<br>';
	}
	else
	{
		#	length is not defined, check to be sure its a text field
		#
		$p = strpos($field_type, 'text', 0);
		if($p!==false)
		{
			$isTEXT = true;
		}

//echo $field_type.' {'.$beg.','.$end.'} len=? isTEXT='.$isTEXT.' isSTRING='.$isSTRING.'<br>';

	}
//echo $field_type.' {'.$beg.','.$end.'} len=? isTEXT='.$isTEXT.' isSTRING='.$isSTRING.'<br>';

	$p = strpos($field_type, 'unsigned', 0);
	if($p!==false)
	{
		$isUNSIGNED = true;
	}



	$field_info = array();
	$field_info['len'] = $int;
	$field_info['dec'] = $dec;
	$field_info['isUNSIGNED'] = $isUNSIGNED;
	$field_info['isTEXT'] = $isTEXT;
	$field_info['isSTRING'] = $isSTRING;

//var_dump($field_info);

	return $field_info;
}

#-------------------------------------------------------------------------------
#
#	getting table data
#
#-------------------------------------------------------------------------------
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function get_filters($string) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
//echo '<p class="todo">$string='.$string.'</p>';

	$tmp = explode(FILTER_SPLITTER, $string);

//array(4) { [0]=>  string(11) "filter_nome" [1]=>  string(5) "irel*" [2]=>  string(14) "filter_nazione" [3]=>  string(4) "uni*" }

	if(is_array($tmp))
	{
		$filters = array();
		$t = count($tmp);
		for($i=0;$i<$t;$i++)
		{
			$filters[$tmp[$i]] = $tmp[($i+1)];
			$i++;
		}

//array(2) { ["filter_nome"]=>  string(5) "irel*" ["filter_nazione"]=>  string(4) "uni*" }

		return $filters;
	}
	return false;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function get_table_defs($table) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	if(!table_exists(MED_TABLE_DEFS, MED_OWN_DB))
	{
		return;
	}

	$sql = 'SELECT * '
				//.'`'.MED_TABLE_DEFS.'`.`referenceField`, '
				//.'`'.MED_TABLE_DEFS.'`.`referencedTable`, '
				//.'`'.MED_TABLE_DEFS.'`.`referencedID`, '
				//.'`'.MED_TABLE_DEFS.'`.`referencedDesc` '

			.'FROM '.MED_OWN_DB.'.`'.MED_TABLE_DEFS.'` '

			.'WHERE `'.MED_TABLE_DEFS.'`.`table` = \''.mysql_real_escape_string($table).'\' '
	;
	$sth = db_query($sql,__LINE__,__FILE__);
//echo $sql.' =>'.$sth[1];

	if($sth[1]==0)
	{
		return false;
	}
	$rows = db_fetch($sth[0],false);
//var_dump($rows);

	return $rows;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function get_fields_defs($table) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	if(!table_exists(MED_FIELDS_DEFS, MED_OWN_DB))
	{
		return;
	}

	$sql = 'SELECT * '
				//.'`'.MED_TABLE_DEFS.'`.`referenceField`, '
				//.'`'.MED_TABLE_DEFS.'`.`referencedTable`, '
				//.'`'.MED_TABLE_DEFS.'`.`referencedID`, '
				//.'`'.MED_TABLE_DEFS.'`.`referencedDesc` '

			.'FROM '.MED_OWN_DB.'.`'.MED_FIELDS_DEFS.'` '

			.'WHERE `'.MED_FIELDS_DEFS.'`.`table` = \''.mysql_real_escape_string($table).'\' '
	;
	$sth = db_query($sql,__LINE__,__FILE__);
//echo $sql.' =>'.$sth[1];

	if($sth[1]==0)
	{
		return false;
	}
	$rows = db_fetch($sth[0],false);
//var_dump($rows);

	return $rows;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function get_validation_defs($table, $field='') {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	if(!table_exists(MED_FIELDS_DEFS, MED_OWN_DB))
	{
		return;
	}

	$sql = 'SELECT * '

			.'FROM '.MED_OWN_DB.'.`'.MED_TABLE_VALIDATE.'` '

			.'WHERE `'.MED_TABLE_VALIDATE.'`.`table` = \''.mysql_real_escape_string($table).'\' '
	;
	if(strlen($field)>0)
	{
		$sql .= 'AND `'.MED_TABLE_VALIDATE.'`.`field` = \''.mysql_real_escape_string($field).'\' ';
	}
	$sth = db_query($sql,__LINE__,__FILE__);
//echo $sql.' =>'.$sth[1];

	if($sth[1]==0)
	{
		return false;
	}
	$rows = db_fetch($sth[0],false);
//var_dump($rows);

	return $rows;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function get_not_filtered_fields() {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	@since 0.0.6
	#
	global $_T_FIELDS, $_FIELDS, $_T_FLD_DEFS, $_FLD_DEFS, $_RESERVED_FIELDS;
	$not_filtered_fields = array();

	$i = 0;
	if($_T_FLD_DEFS>0)
	{
		while($i<$_T_FLD_DEFS)
		{
			#	let's get the fields that are not defined as filters
			#
			if($_FLD_DEFS[$i]['isFILTER']==0)
			{
				for($ii=0;$ii<$_T_FIELDS;$ii++)
				{
					if(!in_array($_FIELDS[$ii]['Field'], $not_filtered_fields)
						&& $_FIELDS[$ii]['Comment']!='*PRIVATE'
						&& !in_array($_FIELDS[$ii]['Field'], $_RESERVED_FIELDS)
					) {
						$not_filtered_fields[] = $_FIELDS[$ii]['Field'];
					}
				}
			}
			$i++;
		}
	}

	#
	#	let's get all the fields that are not set as filters yet
	#
	for($ii=0;$ii<$_T_FIELDS;$ii++)
	{
		$i = 0;
		$isFILTER = false;
		while($i<$_T_FLD_DEFS)
		{
			if($_FLD_DEFS[$i]['field']==$_FIELDS[$ii]['Field']
				&& $_FLD_DEFS[$i]['isFILTER']==1
			) {
				$isFILTER = true;
			}
			$i++;
		}

//echo $_FIELDS[$ii]['Field'].'&raquo;'.$isFILTER.'<br>';

		if($isFILTER==false
			&& !in_array($_FIELDS[$ii]['Field'], $not_filtered_fields)
			&& $_FIELDS[$ii]['Comment']!='*PRIVATE'
			&& !in_array($_FIELDS[$ii]['Field'], $_RESERVED_FIELDS)
		) {
			$not_filtered_fields[] = $_FIELDS[$ii]['Field'];
		}
	}

//var_dump($not_filtered_fields);echo '<hr>';

	return $not_filtered_fields;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function get_not_referenced_fields() {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	global $_T_FIELDS, $_FIELDS;
	$not_referenced_fields = array();

	$ii = 0;
	$label = '';
	while($ii<$_T_FIELDS && $label=='')
	{
		#	let's get the unreferenced fields
		#
		$field = $_FIELDS[$ii]['Field'];

		$result = is_referenced_field($field);
		if($result==false)
		{
			$not_referenced_fields[] = $field;
		}
		$ii++;
	}
//var_dump($not_referenced_fields);echo '<hr>';

	return $not_referenced_fields;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function is_referenced_field($field) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	if(!table_exists(MED_TABLE_DEFS, MED_OWN_DB))
	{
		return;
	}

	$sql = 'SELECT * '
				//.'`'.MED_TABLE_DEFS.'`.`referenceField`, '
				//.'`'.MED_TABLE_DEFS.'`.`referencedTable`, '
				//.'`'.MED_TABLE_DEFS.'`.`referencedID`, '
				//.'`'.MED_TABLE_DEFS.'`.`referencedDesc` '

			.'FROM '.MED_OWN_DB.'.`'.MED_TABLE_DEFS.'` '

			.'WHERE `'.MED_TABLE_DEFS.'`.`table` = \''.EDIT_TABLE_NAME.'\' '
			.'AND   `'.MED_TABLE_DEFS.'`.`referenceField` = \''.mysql_real_escape_string($field).'\' '
	;
	$sth = db_query($sql,__LINE__,__FILE__);
//echo $sql.' =>'.$sth[1];

	if($sth[1]==0)
	{
		return false;
	}
	$row = db_fetch($sth[0],false);
//var_dump($row);

	return $row;
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function get_referenced_data($ID, $table, $refID, $descField) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	if(!table_exists($table, MAINSITE_DB))
	{
		return;
	}

	$sql = 'SELECT `'.$table.'`.`'.$descField.'` '

			.'FROM '.MAINSITE_DB.'.`'.$table.'` '

			.'WHERE `'.$table.'`.`'.$refID.'` = '.(int)$ID.' '
	;
	$sth = db_query($sql,__LINE__,__FILE__);
//echo $sql.' =>'.$sth[1];

	if($sth[1]==0)
	{
		return '';
	}
	$row = db_fetch($sth[0],false);
//var_dump($row);

	return $row[0][$descField];
}
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function get_referenced_options($ID, $table, $refID, $descField, $post_name, $showEmpty = true) {
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#
	if(!table_exists($table, MAINSITE_DB))
	{
		return;
	}

	$sql = 'SELECT '
				.'`'.$table.'`.`'.$refID.'`, '
				.'`'.$table.'`.`'.$descField.'` '

			.'FROM '.MAINSITE_DB.'.`'.$table.'` '
	;
	$sth = db_query($sql,__LINE__,__FILE__);
//echo $sql.' =>'.$sth[1].'('.$ID.')';

	if($sth[1]==0)
	{
		return '';
	}
	$rows = db_fetch($sth[0],false);
//var_dump($rows);

	$html = '<select name="'.$post_name.'">';
	if($showEmpty==true)
	{
		$html .= '<option value="">'
					.__( 'Please select an option...', MED_LOCALE )
				.'</option>'
		;
	}

	for($i=0;$i<$sth[1];$i++)
	{
		$selected = '';
		if($rows[$i][$refID]==$ID)
		{
			$selected = ' selected="selected"';
		}
		$html .= '<option value="'.$rows[$i][$refID].'"'.$selected.'>'
					//.$rows[$i][$descField]								#	0.0.5
					.stripslashes($rows[$i][$descField])					#	0.0.5
				.'</option>'
		;
	}
	$html .= '</select>';

	return $html;
}


#===============================================================================
#
#	the following functions are generic
#
#	for specific needs you need to redefine one or more of the following
#	functions and add the code you need
#
#===============================================================================
if(!function_exists('get_table_data'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//function get_table_data($RRN, $lang='', $limit='', $filters_string='') {						#	0.0.6
	function get_table_data($RRN, $lang='', $limit='', $filters_string='', $filters_relations='') {	#	0.0.6
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	gets data from the table
		#
		#	possible values for $RRN:
		#		*all		= to get all the records (pending $limit value)
		#		*info		= to know how many records the table contains
		#		{string}	= to get the record by its key RRN (ID)			#	0.0.4
		#
		//if($RRN!='*all' && $RRN!='*info' && (int)$RRN==0) { return; }	#	0.0.4
		if($RRN!='*all' && $RRN!='*info' && $RRN=='') { return; }		#	0.0.4
		#
		global $isFilteredData, $_FLD_DEFS, $_T_FLD_DEFS, $_FIELDS, $_T_FIELDS;

//var_dump($_FIELDS);
//echo '<br>'.$_T_FIELDS;
//var_dump($_FLD_DEFS);
//echo '<br>'.$_T_FLD_DEFS;

//echo '<code><b>get_table_data(</b>'.$RRN.', '.$lang.', '.$limit.', '.$filters_string.'<b>)</b></code><br>';
//return;

		$sql = 'SELECT ';
		for($i=0;$i<$_T_FIELDS;$i++)
		{
			$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$_FIELDS[$i]['Field'].'` AS r_'.$_FIELDS[$i]['Field'].', ';
		}
		$sql = substr($sql, 0, -2).' '

				.'FROM `'.MAINSITE_DB.'`.`'.EDIT_TABLE_NAME.'` '

				.'WHERE 1=1 '
		;

		if(is_numeric($RRN))
		{
			$sql .= 'AND `'.EDIT_TABLE_NAME.'`.`'.EDIT_TABLE_RRN_FIELD.'` = '.(int)$RRN.' ';
		}
		#
		#	0.0.4: BEG
		#-------------
		elseif(is_string($RRN) && $RRN!='*all' && $RRN!='*info')
		{
			$key_name = explode('|', EDIT_TABLE_RRN_FIELD);
			$key_value = explode('|', $RRN);

			foreach($key_name as $k => $value)
			{
				$sql .= 'AND `'.EDIT_TABLE_NAME.'`.`'.$value.'` = \''.mysql_real_escape_string($key_value[$k]).'\' ';
			}
		}
		#-------------
		#	0.0.4: END
		#
		elseif(strlen($filters_string)>0)
		{
			$filters = get_filters($filters_string);

			#
			#	0.0.6: BEG
			#-------------
			$tf = count($filters);
			$filters_rel = get_filters($filters_relations);
			$sql .= ' AND';
			$trf = count($filters_rel);
			$f = 0;
			$filter_name_const_inp = array();		$filter_name_const_out = array();
			$filter_name_const_inp[] = 'filter_';	$filter_name_const_out[] = '';
			$filter_name_dates_inp = array();		$filter_name_dates_out = array();
			$filter_name_dates_inp[] = 'from_';		$filter_name_dates_out[] = '';
			$filter_name_dates_inp[] = 'to_';		$filter_name_dates_out[] = '';
			#-------------
			#	0.0.6: END
			#

//echo '$filters_string=>'.$filters_string.'<hr>';
//echo '$filters:<br>';var_dump($filters);echo '<hr>';
//echo '$filters_relations=>'.$filters_relations.'<hr>';
//echo '$filters_rel:<br>';var_dump($filters_rel);echo '<hr>';
//echo '$tf=>'.$tf.'<hr>';

//	$_SESSION[EDIT_TABLE_NAME.'_filters']['filter_00box']	= $_POST['filter_00box'];
//	$_SESSION[EDIT_TABLE_NAME.'_filters']['filter_nome']	= $_POST['filter_nome'];
//	$_SESSION[EDIT_TABLE_NAME.'_filters']['filter_descrizione']	= $_POST['filter_descrizione'];

			foreach($filters as $name => $value)
			{
				if($value!='')
				{
					$isFilteredData = true;

					//$sql .= 'AND `'.EDIT_TABLE_NAME.'`.`'.str_replace('filter_', '', $name).'` '
					//			.'LIKE \''.mysql_real_escape_string(str_replace('*','%',$value)).'\' '
					//;


					//$name = str_replace('filter_', '', $name);								#	0.0.6
					$name = str_replace($filter_name_const_inp, $filter_name_const_out, $name);	#	0.0.6

					$value = str_replace('*','%',$value);

					$ii = 0;
					$type = '';
					while($ii<$_T_FIELDS && $type=='')
					{
						#	let's get the field type
						#
						//if($_FIELDS[$ii]['Field']==$name)																#	0.0.6
						if($_FIELDS[$ii]['Field']==str_replace($filter_name_dates_inp, $filter_name_dates_out, $name))	#	0.0.6
						{
							$type = $_FIELDS[$ii]['Type'];
						}
						$ii++;
					}
					$field_len_info = table_editor_get_field_len_info($type);	#	0.0.6


//echo '$name['.$name.']<br>';
//echo '$value['.$value.']<br>';
//echo '$type['.$type.']<br>';
//var_dump($field_len_info);echo '<hr>';

//var_dump($_SESSION[EDIT_TABLE_NAME.'filters']);echo '<hr>';
//var_dump($_SESSION[EDIT_TABLE_NAME.'_rel_filters']);echo $name. '<hr>';


					#
					#	0.0.6: BEG
					#-------------
					$filter_relation = 'AND';
					if(isset($filters_rel['_rel_filter_'.$name.'_']))
					{
						$filter_relation = $filters_rel['_rel_filter_'.$name.'_'];
//var_dump($filters);echo '<br>';
//var_dump($filters_rel);echo $name.','.$filter_relation.'<hr>';
					}
					$f++;
					if($f>1 && $f==$trf) { $filter_relation = ''; }
					#-------------
					#	0.0.6: END
					#

//echo 'rel['.$filters_rel['_rel_filter_'.$name.'_'].']<br>';
//echo '$filter_relation['.$filter_relation.'], $f['.$f.'], tf['.$tf.'], trf['.$trf.']<br>';


					if($field_len_info['dec']>0)
					{
						#	MySQL does not like the comma used as the decimal separator...
						#
						$value = str_replace(',', '.', $value);
					}

					if($type=='' || $field_len_info['isSTRING']==true || $field_len_info['isTEXT']==true)
					{
						//$sql .= 'AND `'.EDIT_TABLE_NAME.'`.`'.$name.'` = \''.mysql_real_escape_string($value).'\' ';					#	0.0.6

						$string = str_replace('*', '%', $value);
						//$sql .= 'AND `'.EDIT_TABLE_NAME.'`.`'.$name.'` LIKE \''.mysql_real_escape_string($value).'\' ';				#	0.0.6
						$sql .= ' `'.EDIT_TABLE_NAME.'`.`'.$name.'` LIKE \''.mysql_real_escape_string($value).'\' '.$filter_relation;	#	0.0.6
					}
					else
					{
						if($type=='date')
						{

							#
							#	0.0.6: BEG
							#-------------
 							//$ii = 0;
							//$hasEndDate = '';
							//while($ii<$_T_FLD_DEFS && $hasEndDate=='')
							//{
							//	#	let's get the end date field
							//	#
							//	if($_FLD_DEFS[$ii]['field']==$name && strlen($_FLD_DEFS[$ii]['hasEndDate'])>0)
							//	{
							//		$hasEndDate = $_FLD_DEFS[$ii]['hasEndDate'];
							//	}
							//	$ii++;
							//}

							//if($hasEndDate=='')
							//{
							//	$sql .= 'AND `'.EDIT_TABLE_NAME.'`.`'.$name.'` <= \''.format_calendar_to_date($value).'\' ';				#	0.0.6
							//}
							//else
							//{
							//	$sql .= 'AND `'.EDIT_TABLE_NAME.'`.`'.$name.'` >= \''.format_calendar_to_date($value).'\' ';				#	0.0.6
							//}

							if(substr($name,0,5)=='from_')
							{
								$sql .= ' (`'.EDIT_TABLE_NAME.'`.`'.substr($name,5).'` >= \''.format_calendar_to_date($value).'\' AND ';
							}
							else if(substr($name,0,3)=='to_')
							{
								$sql .= ' `'.EDIT_TABLE_NAME.'`.`'.substr($name,3).'` <= \''.format_calendar_to_date($value).'\') '.$filter_relation;
							}
							#-------------
							#	0.0.6: END
							#
						}
						elseif($type=='datetime' || $type=='timestamp')
						{
							//$sql .= 'AND `'.EDIT_TABLE_NAME.'`.`'.$name.'` = \''.format_calendar_to_datetime($value).'\' ';				#	0.0.6
							$sql .= ' `'.EDIT_TABLE_NAME.'`.`'.$name.'` = \''.format_calendar_to_datetime($value).'\' '.$filter_relation;	#	0.0.6
						}
						elseif((int)$field_len_info['dec']>0)
						{
							//$sql .= 'AND `'.EDIT_TABLE_NAME.'`.`'.$name.'` = '.$value.' ';												#	0.0.6
							$sql .= ' `'.EDIT_TABLE_NAME.'`.`'.$name.'` = '.$value.' '.$filter_relation;									#	0.0.6
						}
						else
						{
							//$sql .= 'AND `'.EDIT_TABLE_NAME.'`.`'.$name.'` = '.(int)$value.' ';											#	0.0.6
							$sql .= ' `'.EDIT_TABLE_NAME.'`.`'.$name.'` = '.(int)$value.' '.$filter_relation;								#	0.0.6
						}
					}
				}
			}
			if(substr($sql, -3)=='AND') { $sql = substr($sql, 0, -3); }		#	0.0.6
			if(substr($sql, -2)=='OR')  { $sql = substr($sql, 0, -2); }		#	0.0.6
		}

		$sql .= ''
				//.'GROUP BY `'.EDIT_TABLE_NAME.'`.`RRN` '
				//.'ORDER BY `'.EDIT_TABLE_NAME.'`.`ordine` ASC '
				//.'       , `'.EDIT_TABLE_NAME.'`.`codice` ASC '
		;

		if($RRN=='*all' && strlen($limit)>0)
		{
			$sql .= 'LIMIT '.$limit.' ';
		}

//echo '['.$sql.']<br />';

		$sth = db_query($sql,__LINE__,__FILE__);

//echo '=>'.$sth[1].'<br />';

		if($RRN=='*info')
		{
			return $sth[1];
		}

		if($sth[1]==0)
		{
			return false;
		}

		$row = db_fetch($sth[0],false);

		return $row;
	}
}
if(!function_exists('show_filters'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function show_filters($show_type = '') {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	show the table filters box
		#	@since 0.0.6
		#
		global $isFilteredData,	$filters, $_TBL_DEFS, $_T_TBL_DEFS, $_FLD_DEFS, $_T_FLD_DEFS, $_FIELDS, $_T_FIELDS, $_RESERVED_FIELDS;

		if($show_type=='setup')
		{
			$toggler_label = __( 'Filters setup', MED_LOCALE );
			$toggler_handle = '_filter_setup';
		}
		else
		{
			$toggler_label = __( 'Filter data', MED_LOCALE );
			$toggler_handle = '_filter_data';
		}


//var_dump($_TBL_DEFS);echo '<hr>';
//var_dump($_FLD_DEFS);echo '<hr>';
//var_dump($_FIELDS);echo '<hr>';

//unset($_SESSION[EDIT_TABLE_NAME.'_filters']);#debug


//$html = "<div class='wp-submenu'>".
//		"<div class='wp-submenu-head'>myEASYdb</div>".
//		'<ul id="adminmenu">'
//			.'<li class="wp-first-item"><a href=\'admin.php?page=med_admin\' class="wp-first-item" tabindex="1">myEASYdb</a></li>'
//			.'<li><a href=\'admin.php?page=citta\' tabindex="1">Citt&agrave;</a></li>'
//			.'<li class="current"><a href=\'admin.php?page=corsi_calendario\' class="current" tabindex="1">Calendario dei corsi</a></li>'
//			.'<li><a href=\'admin.php?page=corsi_cat\' tabindex="1">Categorie dei corsi</a></li>'
//			.'<li><a href=\'admin.php?page=corsi_desc\' tabindex="1">Descrizioni dei corsi</a></li>'
//		.'</ul>'
//		.'</div>';
//return $html;


		$not_filtered_fields = get_not_filtered_fields();

//var_dump($not_filtered_fields);echo '<hr>';

//var_dump($_FLD_DEFS);
//var_dump($_FIELDS[0]);


		$html = '';
		$total_filters_on_screen = 0;

		$_filters = array();
		for($i=0;$i<$_T_FLD_DEFS;$i++)
		{
			#	search for fields defined as filters
			#
			if($_FLD_DEFS[$i]['isFILTER']==true)
			{
				#	this field must be used to filter results
				#
				$total_filters_on_screen++;
				$ii = 0;
				$label = '';
				while($ii<$_T_FIELDS && $label=='')
				{
					#	let's get the field label
					#
					if($_FIELDS[$ii]['Field']==$_FLD_DEFS[$i]['field'])
					{
						if(strlen($_FIELDS[$ii]['Comment'])>0)
						{
							$label = $_FIELDS[$ii]['Comment'];
						}
						else
						{
							$label = $_FIELDS[$ii]['Field'];
						}

						$field = $_FIELDS[$ii]['Field'];
						$type = $_FIELDS[$ii]['Type'];
					}
					$ii++;
				}

//echo 'type['.$type.']<br>';
//$filterSuffix = '';
//if($type=='date')
//{
//	$filterSuffix = '_dt';
//}

				#
				#	if the input field value is empty, fill it with saved data
				#
				if(!isset($_POST['filter_'.$_FLD_DEFS[$i]['field']]))
				{
					$_POST['filter_'.$_FLD_DEFS[$i]['field']] = $_SESSION[EDIT_TABLE_NAME.'_filters']['filter_'.$_FLD_DEFS[$i]['field']];
				}

				#
				#	if the filter relation is empty, fill it with saved data
				#
				if(!isset($_POST['_rel_filter_'.$_FLD_DEFS[$i]['field'].'_']))
				{
					$_POST['_rel_filter_'.$_FLD_DEFS[$i]['field'].'_'] = $_SESSION[EDIT_TABLE_NAME.'_rel_filters']['_rel_filter_'.$_FLD_DEFS[$i]['field']];
				}

				#
				#	prepare the input field
				#
				$iii = 0;
				$isREFERENCED = false;

				while($iii<$_T_TBL_DEFS && $isREFERENCED==false)
				{
					#	if the field is a referenced in another table, prepare a list of options
					#
					if($_TBL_DEFS[$iii]['referenceField']==$_FLD_DEFS[$i]['field'])
					{
						$isREFERENCED = true;
						$input = get_referenced_options($_POST['filter_'.$_FLD_DEFS[$i]['field']],
														$_TBL_DEFS[$iii]['referencedTable'],
														$_TBL_DEFS[$iii]['referencedID'],
														$_TBL_DEFS[$iii]['referencedDesc'],
														'filter_'.$_FLD_DEFS[$i]['field']);
					}
					$iii++;
				}

				if($isREFERENCED==false)
				{
					#	the field is a simple input, format it as needed
					#
					if($type=='date' || $type=='datetime')
					{
						$input = '<table>'
									.'<tr>'
										.'<td nowrap>'
											.__('From', MED_LOCALE ).' &raquo;'
										.'</td>'
										.'<td>'
											.show_calendar('table_editor_adm', 'filter_from_'.$_FLD_DEFS[$i]['field'], false, true, 'filter_to_'.$_FLD_DEFS[$i]['field'])
										.'</td>'
									.'</tr>'
									.'<tr>'
										.'<td nowrap>'
											.__('To', MED_LOCALE ).' &raquo;'
										.'</td>'
										.'<td>'
											.show_calendar('table_editor_adm', 'filter_to_'.$_FLD_DEFS[$i]['field'], false, false)
										.'</td>'
									.'</tr>'
							.'</table>'
						;
					}
					else
					{
						$field_len_info = table_editor_get_field_len_info($type);

						if($field_len_info['len']==0 && $field_len_info['isTEXT']==true)
						{
							$input = '<textarea class="form" name="filter_'.$_FLD_DEFS[$i]['field'].'" rows="'.(4).'" cols="'.(20).'" style="/*width:100%;*/">'.$_POST['filter_'.$_FLD_DEFS[$i]['field']].'</textarea>';
						}
						else
						{
							$input = '<input class="form" type="text" name="filter_'.$_FLD_DEFS[$i]['field'].'" value="'.$_POST['filter_'.$_FLD_DEFS[$i]['field']].'" size="20" maxlength="255" />';
						}
					}
				}

				$input = '<td width="90%">'.$input.'</td>';

				$selected_and = '';
				$selected_or = '';
				switch($_POST['_rel_filter_'.$field.'_'])
				{
					case 'OR':	$selected_or = ' selected="selected"';	break;
					case 'AND':
					default:
						$selected_and = ' selected="selected"';
				}

				$filter_relation = ''
					.'<td width="1%">'
						.'<select id="_rel_filter_'.$field.'" name="_rel_filter_'.$field.'_">'
							.'<option value="AND"'.$selected_and.'>'.'AND'.'</option>'
							.'<option value="OR"'.$selected_or.'>'.'OR'.'</option>'
						.'</select>'
					.'</td>'
				;

				if($i==($_T_FLD_DEFS-1))
				{
					$filter_relation = '';
				}


				$setup_only = '';
				if($show_type=='setup')
				{
					$input = '';
					$filter_relation = '';
					$setup_only = '<td width="1%">'

									.'<div style="display:none;border:0px dotted green;">'
										.'<input id="__field_'.$i.'" name="__field_'.$i.'_" type="hidden" value="'.$field.'" />'
//.'<input id="__referencedTable_'.$i.'" name="__referencedTable_'.$i.'_" type="text" value="'.$referencedTable.'" />'
//.'<input id="__referencedID_'.$i.'" name="__referencedID_'.$i.'_" type="text" value="'.$referencedID.'" />'
									.'</div>'

									.'<div>'
										.'<input tabindex="-1" type="button" class="button-secondary" style="margin:4px 0px 0 0;" name="btn" value="&nbsp;'
												.__( 'Remove this filter', MED_LOCALE )
												.'&nbsp;" onclick="javascript:'
																.'if(confirm(\''.__( 'Are you sure that you want to remove this filter?', MED_LOCALE ).'\')==false) {'
																	.'return false;'
																.'};'
																.'var t=document.getElementById(\'__total_filters_on_screen_\');'
																.'t.value=t.value-1;'
																.'sndReq(\'delete_filter\',\'update_filters_info\',\''.$i.AJAX_PARMS_SPLITTER.EDIT_TABLE_NAME.'\');'
															.'" />'
									.'</div>'

							.'</td>'
					;
				}


				$html .= ''
						.'<tr>'
							.'<td width="1%" valign="top" nowrap>'
								.$label
								//.':'
							.'</td>'

							//.'<td width="90%">'
								.$input
							//.'</td>'

							//.'<td width="1%">'
								.$filter_relation
							//.'</td>'

							.$setup_only

						.'</tr>'
				;

				$_SESSION[EDIT_TABLE_NAME.'_filters']['filter_'.$_FLD_DEFS[$i]['field']]			= $_POST['filter_'.$_FLD_DEFS[$i]['field']];
				$_SESSION[EDIT_TABLE_NAME.'_rel_filters']['_rel_filter_'.$_FLD_DEFS[$i]['field']]	= $_POST['_rel_filter_'.$_FLD_DEFS[$i]['field'].'_'];

//var_dump($_POST);echo '<hr>';

			}
		}

//var_dump($_SESSION[EDIT_TABLE_NAME.'_filters']);
//	if(strlen($lang)==0)	{ $lang = $_SESSION['table_editor']['lang']; }
//	if(strlen($lang)==0)	{ $lang = $_SESSION['sitelanguage']; }

		$html_not_filter = '';
		$buttons = '';

		if($show_type=='setup')
		{
			$html_not_filter = ''
				.'<div id="not_ref_fields_container" style="float:right;">'
					.'<select id="not_ref_fields" name="not_filtered_fields">'
					.'<option value="">' . __( 'Select a new field to use as a filter and click on this button &raquo;', MED_LOCALE ) . '</option>'
			;

					foreach($not_filtered_fields as $field)
					{
						$html_not_filter .= '<option value="'.$field.'">'.$field.'</option>';
					}

			$html_not_filter .= '</select>'
				.'</div>'
			;

			$buttons = ''
						.'<input type="button" class="button-secondary" style="margin-left:8px;" name="btn" value="&nbsp;'
								.__( 'Add filter', MED_LOCALE )
								.'&nbsp;" onclick="javascript:'
										.'if(document.getElementById(\'not_ref_fields\').value==\'\'){'
											.'alert(\'' . __( 'Please select the new field you want to set as a filter!', MED_LOCALE ) . '\');'
											.'return false;'
										.'}'
										.'sndReq(\'filter_add\',\'_filters_add\',\'\'+document.getElementById(\'not_ref_fields\').value+\'' . AJAX_PARMS_SPLITTER
													. '\'+document.getElementById(\'__total_filters_on_screen_\').value+\'' . AJAX_PARMS_SPLITTER
													. EDIT_TABLE_NAME
										. '\');'
						.'" />'
						.'<input type="button" class="button-primary" style="margin-left:8px;" name="btn" value="&nbsp;'
								.__( 'Apply', MED_LOCALE )
								.'&nbsp;" onclick="javascript:'
//.'alert(document.getElementById(\'__total_referenced_on_screen_\').value);'
//.'var el=document.getElementById(\'__total_referenced_on_screen_\');'
										.'sndReq(\'update_filters\',\'update_filters_info\',\'\'+document.getElementById(\'__total_filters_on_screen_\').value+\'' . AJAX_PARMS_SPLITTER
													. EDIT_TABLE_NAME
										.'\');'
						.'" />'
			;

		}

		if(strlen($html)==0)
		{
			$html = '<tr><td>'
						.'<h3><i>' . __( 'No filters are set for this table yet', MED_LOCALE ) . '</i></h3>'
					.'</td></tr>'
			;
			$info_wildcards = '';
		}
		else
		{
			if($show_type!='setup')
			{
				$buttons = ''
							.'&nbsp;&nbsp;&nbsp;'
							.'<input type="submit" class="button-primary" id="button-primary-green" name="btn" value="'
								.FILTER
							.'" />'
				;
				$info_wildcards = 'Use an * as a wildcard on strings (example: "Tom*" will find "Tommy" and "Tomato")';
			}
		}

		$html = ''
				#
				#	filter data
				#
				.'<div id="'.$toggler_handle.'" class="top_tab_contents">'
					.'<table cellspacing="6" cellpadding="0" border="0" width="98%" align="right">'
					.'<tr><td colspan="99" align="right"><i>'.$info_wildcards.'</i></td></tr>'
					.$html
		;

		if($show_type=='setup')
		{
			$html .= ''
					.'<tr>'
						.'<td id="_filters_add" colspan="99" align="right" style="padding:0;margin:0;background:transparent;">'
						.'</td>'
					.'</tr>'
					.'<tr>'
						.'<td id="_filters_addChildren" colspan="99" align="right" class="updated" style="border-color:#21759b;background:#298cba;">'
						.'</td>'
					.'</tr>'
			;
		}

		$html .= ''
					.'<tr>'
						.'<td colspan="99" width="1%" align="right">'

							.'<div>'
								.'<div style="float:right;">'
									//.$btn_setup
									//.$btn_filter
									.$buttons
								.'</div>'

								//.'<div id="not_ref_fields_container" style="float:right;">'
									.$html_not_filter
								//.'</div>'

								.'<input id="__total_filters_on_screen_" name="__total_filters_on_screen_" type="hidden" value="'.$total_filters_on_screen.'" />'
							.'</div>'
						.'</td>'
					.'</tr>'

					.'<tr>'
						.'<td colspan="99">'
							.'<div id="update_filters_info"></div>'
						.'</td>'
					.'</tr>'
					.'</table>'
				.'</div>'

				.'<div style="float:right;margin-right:8px;background:url('.PLUGIN_LINK.'img/screen-options-left.gif) repeat-x;cursor:pointer;" '
						.'onclick="javascript:el_display_toggler(\''.$toggler_handle.'\',\'_filter_TogglerImg'.$toggler_handle.'\');">'

						.'<span class="vert-scrolling-tab" style="line-height:22px;">' . $toggler_label . ''
							.'<img id="_filter_TogglerImg'.$toggler_handle.'" style="float:right;margin:0;" src="'.PLUGIN_LINK.'img/screen-options-right.gif" valign="top" />'
						.'</span>'

				.'</div>'

				.'<script type="text/javascript">'
					.'if(getCookie(\'myeasydb'.$toggler_handle.'\')==1) { '
							.'document.getElementById(\''.$toggler_handle.'\').style.display=\'block\';'
							.'document.getElementById(\'_filter_TogglerImg'.$toggler_handle.'\').src=\''.PLUGIN_LINK.'img/screen-options-right-up.gif\';'
					.'} else {'
						.'document.getElementById(\''.$toggler_handle.'\').style.display=\'none\';'
						.'document.getElementById(\'_filter_TogglerImg'.$toggler_handle.'\').src=\''.PLUGIN_LINK.'img/screen-options-right.gif\';'
					.'}'
				.'</script>'
		;

		return $html;
	}
}
if(!function_exists('show_relations'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function show_relations() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	show the table relations box
		#
		global $isFilteredData,	$filters, $_TBL_DEFS, $_T_TBL_DEFS, $_FLD_DEFS, $_T_FLD_DEFS, $_FIELDS, $_T_FIELDS, $_RESERVED_FIELDS;

//var_dump($_TBL_DEFS);echo '<hr>';
//var_dump($_FLD_DEFS);echo '<hr>';
//var_dump($_FIELDS);echo '<hr>';


		$html = '';
		$ajax = '';

		$_filters = array();
		$not_referenced_fields = get_not_referenced_fields();


//echo '$_T_TBL_DEFS['.$_T_TBL_DEFS.']<br>';
//var_dump($_TBL_DEFS);echo '<hr>';

//echo '$_T_FLD_DEFS['.$_T_FLD_DEFS.']<br>';


		$total_referenced_on_screen = 0;

//for($i=0;$i<$_T_FLD_DEFS;$i++)

		for($i=0;$i<$_T_TBL_DEFS;$i++)
		{
			#	list main table fields
			#
			$ii = 0;
			$referencedTable = '';
			while($ii<$_T_FIELDS && $referencedTable=='')
			{
				#	let's get the field label
				#
				if($_FIELDS[$ii]['Field']==$_TBL_DEFS[$i]['referenceField'])
				{
					if(strlen($_FIELDS[$ii]['Comment'])>0)
					{
						$label = $_FIELDS[$ii]['Comment'];
					}
					else
					{
						$label = $_FIELDS[$ii]['Field'];
					}

					$field = $_FIELDS[$ii]['Field'];
					$type = $_FIELDS[$ii]['Type'];
					$referencedTable = $_TBL_DEFS[$i]['referencedTable'];
					$referencedID    = $_TBL_DEFS[$i]['referencedID'];
					$referencedDesc  = $_TBL_DEFS[$i]['referencedDesc'];
				}
				$ii++;
			}

//echo '$field['.$field.']<br>';
//echo '$type['.$type.']<br>';
//echo '$referencedTable['.$referencedTable.']<br>';
//echo '$referencedID['.$referencedID.']<br>';
//echo '$referencedDesc['.$referencedDesc.']<br>';

//echo '$total_referenced_on_screen['.$total_referenced_on_screen.']<br>';


			if($referencedTable!='' && $type!='date' && $type!='datetime' && $type!='timestamp')
			{

				$total_referenced_on_screen++;

				$html .= '<tr>'
							.'<td valign="top" align="right">'
								.'<span '
#
#	do we really need this? | BEG
#
								//.'<span style="text-decoration:underline;cursor:pointer;" '
										//.'onclick="javascript:sndReq(\'tables_list\',\'med_'.$field.'_relation_table\',\''
										//							. MAINSITE_DB . AJAX_PARMS_SPLITTER
										//							. '' . AJAX_PARMS_SPLITTER
										//							. EDIT_TABLE_NAME . AJAX_PARMS_SPLITTER
										//							. (1) . AJAX_PARMS_SPLITTER
										//							. 'link2fields' . AJAX_PARMS_SPLITTER
										//							. $field
										//						. '\');"'
#
#	do we really need this? | END
#
									.'>'
									.$label
								.'</span>'
								//.' {'.$type.'}'

								.'<div style="display:none;border:0px dotted green;">'
									//.'<input id="__field_'.$ii.'" name="__field_'.$ii.'_" type="hidden" value="'.$field.'" />'
									//.'<input id="__referencedTable_'.$ii.'" name="__referencedTable_'.$ii.'_" type="hidden" value="'.$referencedTable.'" />'
									//.'<input id="__referencedID_'.$ii.'" name="__referencedID_'.$ii.'_" type="hidden" value="'.$referencedID.'" />'
									.'<input id="__field_'.$i.'" name="__field_'.$i.'_" type="hidden" value="'.$field.'" />'
									.'<input id="__referencedTable_'.$i.'" name="__referencedTable_'.$i.'_" type="hidden" value="'.$referencedTable.'" />'
									.'<input id="__referencedID_'.$i.'" name="__referencedID_'.$i.'_" type="hidden" value="'.$referencedID.'" />'
								.'</div>'

								.'<div>'
									.'<input type="button" class="button-secondary" style="margin:4px -14px 0 0;" name="btn" value="&nbsp;'
											.__( 'Remove this relation', MED_LOCALE )
											.'&nbsp;" onclick="javascript:'
															.'if(confirm(\''.__( 'Are you sure that you want to remove this relation?', MED_LOCALE ).'\')==false) {'
																.'return false;'
															.'};'
															.'var t=document.getElementById(\'__total_referenced_on_screen_\');'
															.'t.value=t.value-1;'
															.'sndReq(\'delete_reference\',\'update_references_info\',\''.$i.AJAX_PARMS_SPLITTER.EDIT_TABLE_NAME.'\');'
												#	0.0.6	//.'sndReq(\'update_references\',\'update_references_info\',\'\'+document.getElementById(\'__total_referenced_on_screen_\').value+\'' . AJAX_PARMS_SPLITTER
												#	0.0.6	//			. EDIT_TABLE_NAME
												#	0.0.6	//.'\');'
														.'" />'
								.'</div>'
							.'</td>'
							.'<td valign="top">'
								.'&rsaquo;'
							.'</td>'
							.'<td valign="top">'
								.'<div id="med_'.$field.'_relation_table"><img src="'.PLUGIN_LINK.'img/loading.gif" /></div>'
							.'</td>'
							.'<td valign="top">'
								.'&raquo;'
							.'</td>'
							.'<td valign="top">'
								.'<div id="med_'.$field.'_relation_field"><img src="'.PLUGIN_LINK.'img/loading.gif" /></div>'
							.'</td>'
						.'</tr>'
				;

				#
				#	build the code necessary to fill the relations top tab contents
				#
				$ajax .= 'sndReq(\'tables_list\',\'med_'. $field . '_relation_table\',\''

								. MAINSITE_DB . AJAX_PARMS_SPLITTER
								. '' . AJAX_PARMS_SPLITTER
								. EDIT_TABLE_NAME . AJAX_PARMS_SPLITTER
								. (1) . AJAX_PARMS_SPLITTER
								. 'link2fields' . AJAX_PARMS_SPLITTER
								. $field . AJAX_PARMS_SPLITTER
								. $referencedTable . AJAX_PARMS_SPLITTER
								. $referencedID . AJAX_PARMS_SPLITTER
								//. $ii . AJAX_PARMS_SPLITTER
								. $i . AJAX_PARMS_SPLITTER
								. $referencedDesc

					.'\');'
				;

			}
		}

//var_dump($_SESSION[EDIT_TABLE_NAME.'_filters']);
//	if(strlen($lang)==0)	{ $lang = $_SESSION['table_editor']['lang']; }
//	if(strlen($lang)==0)	{ $lang = $_SESSION['sitelanguage']; }


		//if(strlen($html)>0)
		//{
			$html_not_referenced = '<select id="not_ref_fields" name="not_referenced_fields">'
				.'<option value="">' . __( 'Select a new field to reference and click on this button &raquo;', MED_LOCALE ) . '</option>'
			;
			foreach($not_referenced_fields as $field)
			{
				$html_not_referenced .= '<option value="'.$field.'">'.$field.'</option>';
			}
			$html_not_referenced .= '</select>';


			$html = ''
					.'<div id="_relations_Data" class="top_tab_contents">'
						.'<table cellspacing="6" cellpadding="0" border="0" width="98%" align="right">'

						.'<tr>'
							.'<th align="right" colspan="2">'
								.'This field is related'
							.'</th>'
							//.'<th>'
							//	//.'&rsaquo;'
							//.'</th>'
							.'<th>'
								.'&rsaquo; to this table &raquo;'
							.'</th>'
							//.'<th>'
							//	//.'&raquo;'
							//.'</th>'
							.'<th align="left" colspan="2">'
								.'on this field'
							.'</th>'
						.'</tr>'

						.'<tr>'
							.'<td>'
								.$html
							.'</td>'
						.'</tr>'

						.'<tr>'
							.'<td id="_relations_add" colspan="99" align="right" style="padding:0;margin:0;background:transparent;">'
							.'</td>'
						.'</tr>'
						.'<tr>'
							.'<td id="_relations_addChildren" colspan="99" align="right" class="updated" style="border-color:#21759b;background:#298cba;">'
							.'</td>'
						.'</tr>'

						.'<tr>'
							.'<td colspan="99" width="1%" align="right">'

								.'<div>'
									.'<div style="float:right;">'
										.'<input type="button" class="button-secondary" style="margin-left:8px;" name="btn" value="&nbsp;'
													.__( 'Add relation', MED_LOCALE )
													.'&nbsp;" onclick="javascript:'
															.'if(document.getElementById(\'not_ref_fields\').value==\'\'){'
																.'alert(\'' . __( 'Please select the new field to reference!', MED_LOCALE ) . '\');'
																.'return false;'
															.'}'
															.'sndReq(\'relation_add\',\'_relations_add\',\'\'+document.getElementById(\'not_ref_fields\').value+\'' . AJAX_PARMS_SPLITTER
																		. '\'+document.getElementById(\'__total_referenced_on_screen_\').value+\'' . AJAX_PARMS_SPLITTER
																		. EDIT_TABLE_NAME
															. '\');'
										.'" />'
										.'<input type="button" class="button-primary" style="margin-left:8px;" name="btn" value="&nbsp;'
													.__( 'Apply', MED_LOCALE )
													.'&nbsp;" onclick="javascript:'

//.'alert(document.getElementById(\'__total_referenced_on_screen_\').value);'
//.'var el=document.getElementById(\'__total_referenced_on_screen_\');'

															.'sndReq(\'update_references\',\'update_references_info\',\'\'+document.getElementById(\'__total_referenced_on_screen_\').value+\'' . AJAX_PARMS_SPLITTER
																		. EDIT_TABLE_NAME
															.'\');'
										.'" />'
									.'</div>'

									.'<div id="not_ref_fields_container" style="float:right;">'
										.$html_not_referenced
									.'</div>'

									.'<input id="__total_referenced_on_screen_" name="__total_referenced_on_screen_" type="hidden" value="'.$total_referenced_on_screen.'" />'
								.'</div>'
							.'</td>'
						.'</tr>'
						.'<tr>'
							.'<td colspan="99">'
								.'<div id="update_references_info"></div>'
							.'</td>'
						.'</tr>'
						.'</table>'
					.'</div>'

					.'<div style="float:right;margin-right:8px;background:url('.PLUGIN_LINK.'img/screen-options-left.gif) repeat-x;cursor:pointer;" '
							.'onclick="javascript:if(getCookie(\'myeasydb_relations_Data\')!=1){ __get_tables_relations(); };'
//.'alert(\'bob\');'
													.'el_display_toggler(\'_relations_Data\',\'_relations_TogglerImg\');'
							.'">'

							.'<span class="vert-scrolling-tab" style="line-height:22px;">' . __( 'Relations', MED_LOCALE ) . ''
								.'<img id="_relations_TogglerImg" style="float:right;margin:0;" src="'.PLUGIN_LINK.'img/screen-options-right.gif" valign="top" />'
							.'</span>'

					.'</div>'

					.'<script type="text/javascript">'
						.'function __get_tables_relations() {'
							.$ajax
						.'};'
						.'if(getCookie(\'myeasydb_relations_Data\')==1) { '
								.'document.getElementById(\'_relations_Data\').style.display=\'block\';'
								.'document.getElementById(\'_relations_TogglerImg\').src=\''.PLUGIN_LINK.'img/screen-options-right-up.gif\';'
								//.'__get_tables_relations();'
								.'setTimeout(\'__get_tables_relations()\',250);'
						.'} else {'
							.'document.getElementById(\'_relations_Data\').style.display=\'none\';'
							.'document.getElementById(\'_relations_TogglerImg\').src=\''.PLUGIN_LINK.'img/screen-options-right.gif\';'
						.'}'
					.'</script>'
			;
		//}

		return $html;
	}
}
if(!function_exists('show_validations_checks'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function show_validations_checks() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	show the table validation form
		#
		global $isFilteredData,	$filters, $_TBL_DEFS, $_T_TBL_DEFS, $_FLD_DEFS, $_T_FLD_DEFS, $_FIELDS, $_T_FIELDS, $_RESERVED_FIELDS;

//var_dump($_TBL_DEFS);echo '<hr>';
//var_dump($_FLD_DEFS);echo '<hr>';
//var_dump($_FIELDS);echo '<hr>';

//		$VALIDATION = get_validation_defs(EDIT_TABLE_NAME);
//
//var_dump($VALIDATION);echo '<hr>';

		#
		#	supported field validation checks
		#
		$FLD_TYPES = explode('|', MED_VALIDATION_TYPES);

		#
		#	list main table fields
		#
		$ii = 0;
		while($ii<$_T_FIELDS)
		{
			#	let's get the field definitions
			#
			$field = $_FIELDS[$ii]['Field'];
			$label = $_FIELDS[$ii]['Comment'];
			$type = $_FIELDS[$ii]['Type'];

			$field_len_info = table_editor_get_field_len_info($type);
			list($field_type, $tmp) = explode('(', $type);
			$field_type = strtoupper($field_type);

			$VALIDATION = get_validation_defs(EDIT_TABLE_NAME, $field);

//var_dump($VALIDATION);echo '<hr>';
/*
  `RRN` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `table` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,

  `isMANDATORY` tinyint(1) unsigned DEFAULT '0',
  `isCHECK` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isRADIO` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,

  `smallerTHAN` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `minVAL` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `maxVAL` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `minLEN` int(12) unsigned DEFAULT '0',
  `isPWD` tinyint(1) unsigned DEFAULT '0',
  `isURL` tinyint(1) unsigned DEFAULT '0',
  `isEMAIL` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`RRN`),
  UNIQUE KEY `chiave` (`table`,`field`)
  */

//echo $type.'<br>';
//var_dump($field_len_info);
//var_dump($_FIELDS[$ii]);echo '<hr>';

			$CBOX_mandatory	= bld_checkbox($VALIDATION[0]['isMANDATORY'], 1, $_FIELDS[$ii]['Field'].'_mandatory');

			$fld_types_menu = '<select name="'.$_FIELDS[$ii]['Field'].'_type">';
			foreach($FLD_TYPES as $val)
			{
				$selected = '';
				if($VALIDATION[0]['specialCHECK']==$val)
				{
					$selected = ' selected="selected"';
				}
				$fld_types_menu .= '<option value="'.$val.'"'.$selected.'>'.$val.'</option>';
			}
			$fld_types_menu .= '</select>';

			$html .= ''
				.'<tr>'
					.'<td align="left">'
						.$field
					.'</td>'
					.'<td align="left">'
						.$label
					.'</td>'
					.'<td align="left">'
						.$field_type
					.'</td>'
					.'<td align="right">'
						.$field_len_info['len']
					.'</td>'
					.'<td align="right">'
						.$field_len_info['dec']
					.'</td>'
					.'<td align="center">'
						.$CBOX_mandatory
					.'</td>'
					.'<td align="center">'
						.$fld_types_menu
					.'</td>'
					//.'<td align="center">'
					//	.$CBOX_url
					//.'</td>'
					//.'<td align="center">'
					//	.$CBOX_email
					//.'</td>'
// checkbox
// radio
					.'<td align="left">'
						.'<input type="text" size="8" maxlength="128" name="'.$_FIELDS[$ii]['Field'].'_minval" value="'.$VALIDATION[0]['minVAL'].'" />'
					.'</td>'
					.'<td align="left">'
						.'<input type="text" size="8" maxlength="128" name="'.$_FIELDS[$ii]['Field'].'_maxval" value="'.$VALIDATION[0]['maxVAL'].'" />'
					.'</td>'
					.'<td align="left">'
						.'<input type="text" size="8" maxlength="128" name="'.$_FIELDS[$ii]['Field'].'_minlen" value="'.$VALIDATION[0]['maxLEN'].'" />'
					.'</td>'
					//.'<td align="left">'
					//	.$CBOX_key
					//.'</td>'

//#	debug
//.'<td>'
//	.'('.$type.','.$field_len_info['len'].','.$field_len_info['dec'].','.$field_type.')'
//.'</td>'

				.'</tr>'
			;

			$ii++;
		}



		$html = ''

					.'<div id="_validation_Data" class="top_tab_contents">'

						.'<table cellspacing="6" cellpadding="0" border="0"  align="right">'
								.'<th align="left">' . __( 'Name', MED_LOCALE ) .'</th>'
								.'<th align="left">' . __( 'Label', MED_LOCALE ) .'</th>'
								.'<th align="left">' . __( 'Type', MED_LOCALE ) .'</th>'
								.'<th align="right">' . __( 'Length', MED_LOCALE ) .'</th>'
								.'<th align="right">' . __( 'Decimals', MED_LOCALE ) .'</th>'
								.'<th align="center">' . __( 'Mandatory', MED_LOCALE ) .'</th>'
								.'<th align="center">' . __( 'Special check', MED_LOCALE ) .'</th>'
								.'<th align="left">' . __( 'MIN val', MED_LOCALE ) .'</th>'
								.'<th align="left">' . __( 'MAX val', MED_LOCALE ) .'</th>'
								.'<th align="left">' . __( 'MIN len', MED_LOCALE ) .'</th>'
//.'<th align="left">' . __( 'Key', MED_LOCALE ) .'</th>'
//.'<th align="left">' . __( 'Key length', MED_LOCALE ) .'</th>'
//.'<th align="left">' . __( 'Zerofill', MED_LOCALE ) .'</th>'
//.'<th align="left">' . __( 'Char set', MED_LOCALE ) .'</th>'
//.'<th align="left">' . __( 'Collation', MED_LOCALE ) .'</th>'

								.$html

								.'<tr>'
									.'<td colspan="99" width="1%" align="right">'
										.'&nbsp;&nbsp;&nbsp;'
											.'<input type="button" class="button-secondary" name="btn" '
																.'onclick="alert(\'TODO\');" '
																.'value="&nbsp;'
																	.__( 'Update', MED_LOCALE )
																.'&nbsp;" />'
									.'</td>'
								.'</tr>'
						.'</table>'

					.'</div>'

					.'<div style="float:right;margin-right:8px;background:url('.PLUGIN_LINK.'img/screen-options-left.gif) repeat-x;cursor:pointer;" '
							.'onclick="javascript:el_display_toggler(\'_validation_Data\',\'_validation_TogglerImg\');">'

							.'<span class="vert-scrolling-tab" style="line-height:22px;">' . __( 'Validation checks', MED_LOCALE ) . ''
								.'<img id="_validation_TogglerImg" style="float:right;margin:0;" src="'.PLUGIN_LINK.'img/screen-options-right.gif" valign="top" />'
							.'</span>'

					.'</div>'

					.'<script type="text/javascript">'
						.'if(getCookie(\'myeasydb_validation_Data\')==1) { '
								.'document.getElementById(\'_validation_Data\').style.display=\'block\';'
								.'document.getElementById(\'_validation_TogglerImg\').src=\''.PLUGIN_LINK.'img/screen-options-right-up.gif\';'
							.'} else {'
								.'document.getElementById(\'_validation_Data\').style.display=\'none\';'
								.'document.getElementById(\'_validation_TogglerImg\').src=\''.PLUGIN_LINK.'img/screen-options-right.gif\';'
							.'}'
					.'</script>'

		;
		return $html;
	}
}
if(!function_exists('show_design_table'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function show_design_table() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	show the table design form
		#
		global $isFilteredData,	$filters, $_TBL_DEFS, $_T_TBL_DEFS, $_FLD_DEFS, $_T_FLD_DEFS, $_FIELDS, $_T_FIELDS, $_RESERVED_FIELDS;

//var_dump($_TBL_DEFS);echo '<hr>';
//var_dump($_FLD_DEFS);echo '<hr>';
//var_dump($_FIELDS);echo '<hr>';

		#
		#	supported field types, defaults, etc.
		#
		$FLD_TYPES = explode('|', MED_FLD_TYPES);
		$FLD_DEFAULTS = explode('|', MED_FLD_DEFAULTS);

		#
		#	list main table fields
		#
		$ii = 0;
		while($ii<$_T_FIELDS)
		{
			#	let's get the field definitions
			#
			$field = $_FIELDS[$ii]['Field'];
			$label = $_FIELDS[$ii]['Comment'];
			$type = $_FIELDS[$ii]['Type'];

			$field_len_info = table_editor_get_field_len_info($type);
			list($field_type, $tmp) = explode('(', $type);
			$field_type = strtoupper($field_type);

//echo $type.'<br>';
//var_dump($field_len_info);
//var_dump($_FIELDS[$ii]);echo '<hr>';

			$fld_types_menu = '<select name="'.$_FIELDS[$ii]['Field'].'_type">';
			foreach($FLD_TYPES as $val)
			{
				$selected = '';
				if($field_type==$val)
				{
					$selected = ' selected="selected"';
				}
				$fld_types_menu .= '<option value="'.$val.'"'.$selected.'>'.$val.'</option>';
			}
			$fld_types_menu .= '</select>';

			$defaults_menu = '<select name="'.$_FIELDS[$ii]['Field'].'_default">';
			foreach($FLD_DEFAULTS as $val)
			{
				$selected = '';
				if($_FIELDS[$ii]['Default']==$val)
				{
					$selected = ' selected="selected"';
				}
				$defaults_menu .= '<option value="'.$val.'"'.$selected.'>'.$val.'</option>';
			}
			$defaults_menu .= '</select>';

			$CBOX_unsigned		= bld_checkbox($field_len_info['isUNSIGNED'], true, $_FIELDS[$ii]['Field'].'_unsigned');
			$CBOX_allownull		= bld_checkbox($_FIELDS[$ii]['Null'], 'YES', $_FIELDS[$ii]['Field'].'_allownull');
			$CBOX_key			= bld_checkbox($_FIELDS[$ii]['Key'], 'PRI', $_FIELDS[$ii]['Field'].'_key');
			$CBOX_autoincrement	= bld_checkbox($_FIELDS[$ii]['Extra'], 'auto_increment', $_FIELDS[$ii]['Field'].'_autoincrement');
			$CBOX_updatetime	= bld_checkbox($_FIELDS[$ii]['Extra'], 'on update CURRENT_TIMESTAMP', $_FIELDS[$ii]['Field'].'_updatetime');


			$html .= ''
				.'<tr>'
					.'<td align="left">'
						.$field
					.'</td>'
					.'<td align="left">'
						.'<input type="text" size="8" maxlength="256" name="'.$_FIELDS[$ii]['Field'].'_comment" value="'.$label.'" />'
					.'</td>'
					.'<td align="left">'
						.$fld_types_menu
					.'</td>'
					.'<td align="right">'
						.'<input type="text" size="3" maxlength="12" name="'.$_FIELDS[$ii]['Field'].'_len" value="'.$field_len_info['len'].'" />'
					.'</td>'
					.'<td align="right">'
						.'<input type="text" size="3" maxlength="12" name="'.$_FIELDS[$ii]['Field'].'_dec" value="'.$field_len_info['dec'].'" />'
					.'</td>'
					.'<td align="center">'
						.$CBOX_unsigned
					.'</td>'
					.'<td align="center">'
						.$CBOX_allownull
					.'</td>'
					.'<td align="left">'
						.$defaults_menu
					.'</td>'
					.'<td align="center">'
						.$CBOX_updatetime
					.'</td>'
					.'<td align="center">'
						.$CBOX_autoincrement
					.'</td>'
					.'<td align="left">'
						.$CBOX_key
					.'</td>'

//#	debug
//.'<td>'
//	.'('.$type.','.$field_len_info['len'].','.$field_len_info['dec'].','.$field_type.')'
//.'</td>'

				.'</tr>'
			;

			$ii++;
		}


		$html = ''
					.'<div id="_design_Data" class="top_tab_contents">'

						.'<table cellspacing="6" cellpadding="0" border="0"  align="right">'
								.'<th align="left">' . __( 'Name', MED_LOCALE ) .'</th>'
								.'<th align="left">' . __( 'Label', MED_LOCALE ) .'</th>'
								.'<th align="left">' . __( 'Type', MED_LOCALE ) .'</th>'
								.'<th align="right">' . __( 'Length', MED_LOCALE ) .'</th>'
								.'<th align="right">' . __( 'Decimals', MED_LOCALE ) .'</th>'
								.'<th align="center">' . __( 'Unsigned', MED_LOCALE ) .'</th>'
								.'<th align="center">' . __( 'Allow null', MED_LOCALE ) .'</th>'
								.'<th align="left">' . __( 'Default', MED_LOCALE ) .'</th>'
								.'<th align="center">' . __( 'Update Time', MED_LOCALE ) .'</th>'
								.'<th align="center">' . __( 'Auto increment', MED_LOCALE ) .'</th>'
								.'<th align="center">' . __( 'Key', MED_LOCALE ) .'</th>'
								//.'<th align="left">' . __( 'Key length', MED_LOCALE ) .'</th>'
								//.'<th align="left">' . __( 'Zerofill', MED_LOCALE ) .'</th>'
								//.'<th align="left">' . __( 'Char set', MED_LOCALE ) .'</th>'
								//.'<th align="left">' . __( 'Collation', MED_LOCALE ) .'</th>'

								.$html

								.'<tr>'
									.'<td colspan="99" width="1%" align="right">'
										.'&nbsp;&nbsp;&nbsp;'
											.'<input type="button" class="button-secondary" name="btn" '
																.'onclick="alert(\'TODO\');" '
																.'value="&nbsp;'
																	.__( 'Update', MED_LOCALE )
																.'&nbsp;" />'
									.'</td>'
								.'</tr>'
						.'</table>'

					.'</div>'

					.'<div style="float:right;margin-right:8px;background:url('.PLUGIN_LINK.'img/screen-options-left.gif) repeat-x;cursor:pointer;" '
							.'onclick="javascript:el_display_toggler(\'_design_Data\',\'_design_TogglerImg\');">'

							.'<span class="vert-scrolling-tab" style="line-height:22px;">' . __( 'Design', MED_LOCALE ) . ''
								.'<img id="_design_TogglerImg" style="float:right;margin:0;" src="'.PLUGIN_LINK.'img/screen-options-right.gif" valign="top" />'
							.'</span>'

					.'</div>'

					.'<script type="text/javascript">'
						.'if(getCookie(\'myeasydb_design_Data\')==1) { '
								.'document.getElementById(\'_design_Data\').style.display=\'block\';'
								.'document.getElementById(\'_design_TogglerImg\').src=\''.PLUGIN_LINK.'img/screen-options-right-up.gif\';'
							.'} else {'
								.'document.getElementById(\'_design_Data\').style.display=\'none\';'
								.'document.getElementById(\'_design_TogglerImg\').src=\''.PLUGIN_LINK.'img/screen-options-right.gif\';'
							.'}'
					.'</script>'
		;
		return $html;
	}
}
if(!function_exists('show_maint_table'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function show_maint_table() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	show the table relations box
		#
		global $isFilteredData,	$filters, $_TBL_DEFS, $_T_TBL_DEFS, $_FLD_DEFS, $_T_FLD_DEFS, $_FIELDS, $_T_FIELDS, $_RESERVED_FIELDS;

//var_dump($_TBL_DEFS);echo '<hr>';
//var_dump($_FLD_DEFS);echo '<hr>';
//var_dump($_FIELDS);echo '<hr>';


		$html = ''

					.'<div id="_maintenance_Data" class="top_tab_contents">'

						.'<table cellspacing="6" cellpadding="0" border="0" width="98%" align="right">'
						.'<tr>'
							.'<td>'
								.'TODO: '.__FILE__.' @LINE: '.__LINE__
							.'</td>'
						.'</tr>'
						.'</table>'

					.'</div>'

					.'<div style="float:right;margin-right:8px;background:url('.PLUGIN_LINK.'img/screen-options-left.gif) repeat-x;cursor:pointer;" '
							.'onclick="javascript:el_display_toggler(\'_maintenance_Data\',\'_maintenance_TogglerImg\');">'

							.'<span class="vert-scrolling-tab" style="line-height:22px;">' . __( 'Maintenance', MED_LOCALE ) . ''
								.'<img id="_maintenance_TogglerImg" style="float:right;margin:0;" src="'.PLUGIN_LINK.'img/screen-options-right.gif" valign="top" />'
							.'</span>'

					.'</div>'

					.'<script type="text/javascript">'
						.'if(getCookie(\'myeasydb_maintenance_Data\')==1) { '
								.'document.getElementById(\'_maintenance_Data\').style.display=\'block\';'
								.'document.getElementById(\'_maintenance_TogglerImg\').src=\''.PLUGIN_LINK.'img/screen-options-right-up.gif\';'
							.'} else {'
								.'document.getElementById(\'_maintenance_Data\').style.display=\'none\';'
								.'document.getElementById(\'_maintenance_TogglerImg\').src=\''.PLUGIN_LINK.'img/screen-options-right.gif\';'
							.'}'
					.'</script>'

		;
		return $html;
	}
}
if(!function_exists('show_tools_table'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function show_tools_table() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	show the table relations box
		#
		global $isFilteredData,	$filters, $_TBL_DEFS, $_T_TBL_DEFS, $_FLD_DEFS, $_T_FLD_DEFS, $_FIELDS, $_T_FIELDS, $_RESERVED_FIELDS;

//var_dump($_TBL_DEFS);echo '<hr>';
//var_dump($_FLD_DEFS);echo '<hr>';
//var_dump($_FIELDS);echo '<hr>';


		$html = ''

					.'<div id="_tools_Data" class="top_tab_contents">'

						.'<table cellspacing="6" cellpadding="0" border="0" width="98%" align="right">'
						.'<tr>'
							.'<td>'
								.'TODO: '.__FILE__.' @LINE: '.__LINE__
							.'</td>'
						.'</tr>'
						.'</table>'

					.'</div>'

					.'<div style="float:right;margin-right:8px;background:url('.PLUGIN_LINK.'img/screen-options-left.gif) repeat-x;cursor:pointer;" '
							.'onclick="javascript:el_display_toggler(\'_tools_Data\',\'_tools_TogglerImg\');">'

							.'<span class="vert-scrolling-tab" style="line-height:22px;">' . __( 'Tools', MED_LOCALE ) . ''
								.'<img id="_tools_TogglerImg" style="float:right;margin:0;" src="'.PLUGIN_LINK.'img/screen-options-right.gif" valign="top" />'
							.'</span>'

					.'</div>'

					.'<script type="text/javascript">'
						.'if(getCookie(\'myeasydb_tools_Data\')==1) { '
								.'document.getElementById(\'_tools_Data\').style.display=\'block\';'
								.'document.getElementById(\'_tools_TogglerImg\').src=\''.PLUGIN_LINK.'img/screen-options-right-up.gif\';'
							.'} else {'
								.'document.getElementById(\'_tools_Data\').style.display=\'none\';'
								.'document.getElementById(\'_tools_TogglerImg\').src=\''.PLUGIN_LINK.'img/screen-options-right.gif\';'
							.'}'
					.'</script>'

		;
		return $html;
	}
}
if(!function_exists('get_table_data_list'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//function get_table_data_list($rows, $thisPage) {						#	0.0.4
	function get_table_data_list($rows, $thisPage, $items_per_page='') {	#	0.0.4
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	show the list of table data contents
		#
		global $isFilteredData, $filters, $_TBL_DEFS, $_T_TBL_DEFS, $_FLD_DEFS, $_T_FLD_DEFS, $_FIELDS, $_T_FIELDS, $_RESERVED_FIELDS;

		if($items_per_page=='') { $items_per_page = ITEMS_PER_PAGE; }		#	0.0.4

		$_ALLOWED_TYPES = explode('|', MED_FLD_TYPES);

//var_dump($_ALLOWED_TYPES);echo '<hr>';
//var_dump($_FIELDS);

		$t = 0;
		if(is_array($rows))
		{
			$t = count($rows);
		}

		$html = '';
		$paginate = prep_prev_next(	$_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage'],
									$_SESSION[EDIT_TABLE_NAME.'_paginate']['tot_pages'],
									'/?page=table_editor_adm&table='.EDIT_TABLE_NAME,	//	TODO
									'',										#	0.0.4
									$items_per_page							#	0.0.4
								);

		#
		#	create the table header
		#
		$html .= '<table align="center" cellspacing="'.TBL_LIST_CELLSPACING.'" cellpadding="0" width="90%">'
		//$html .= '<table align="center" cellspacing="4" cellpadding="4" width="50%">'
		//$html .= '<table align="center" cellspacing="4" cellpadding="4" width="auto">'
				.'<tr>'
					.'<th></th>'
		;

		for($i=0;$i<$_T_FIELDS;$i++)
		{

//var_dump($_FIELDS[$i]);echo '<hr>';

			$clean_type = get_field_type_clean($_FIELDS[$i]['Type']);
//echo $clean_type.'<br>';

			$is_numeric = is_field_numeric($clean_type);			#	0.0.4
//echo $_FIELDS[$i]['Type'].'&raquo;'.$is_numeric.'<br>';

			if($_FIELDS[$i]['Comment']!='*PRIVATE'
					&& !in_array($_FIELDS[$i]['Field'], $_RESERVED_FIELDS)
					&& in_array($clean_type, $_ALLOWED_TYPES)
			) {
				if($_FIELDS[$i]['Comment']!='')
				{
					$header = $_FIELDS[$i]['Comment'];
				}
				else
				{
					$header = $_FIELDS[$i]['Field'];
				}

				$align = 'left';									#	0.0.4
				if($is_numeric==true) { $align = 'right'; }			#	0.0.4

				#
				#	0.0.5: BEG
				#-------------
				$iii = 0;
				while($iii<$_T_TBL_DEFS)
				{
					if($_FIELDS[$i]['Field']==$_TBL_DEFS[$iii]['referenceField'])
					{
						$align = 'left';
					}
					$iii++;
				}
				#-------------
				#	0.0.5: END
				#

				//$html .= '<th>'.$header.'</th>';					#	0.0.4
				$html .= '<th align="'.$align.'">'.$header.'</th>';	#	0.0.4
			}
		}
		$html .= '</tr>';

		//$html .= '<tr><td colspan="99">'
		//			.$paginate
		//		.'</td></tr>'
		//;

		#
		#	fills it with the table data
		#
		if($t==0)
		{
			$html .= ''
					.'<tr>'
						.'<td colspan="99">'.'No records on file'.'</td>'
					.'</tr>'
			;
		}
		else
		{
			$e = 1;		# alternate rows background for readbility

			for($i=0;$i<$t;$i++)
			{
				//if(($e%2)>0) { $high='#eee'; } else { $high='#fff'; }
				if(($e%2)>0) { $high = TBL_LIST_TR_HIGHLIGHT_ON; } else { $high = TBL_LIST_TR_HIGHLIGHT_OFF; }
				$e++;

				$inp = array();					$out = array();
				$inp[] = '[[EDIT_TABLE_NAME]]';	$out[] = EDIT_TABLE_NAME;

				#
				#	0.0.4: BEG
				#-------------
				if(strpos(EDIT_TABLE_RRN_FIELD,'|',0)===false)
				{
					$inp[] = '[[RRN]]';			$out[] = $rows[$i]['r_'.EDIT_TABLE_RRN_FIELD];
				}
				else
				{
					$keys = explode('|', EDIT_TABLE_RRN_FIELD);
					$values = '';
					foreach($keys as $key)
					{
						$values .= $rows[$i]['r_'.$key].'|';
					}
					$values = substr($values,0,-1);

					$inp[] = '[[RRN]]';			$out[] = $values;
				}
				#-------------
				#	0.0.4: END
				#

//echo 'EDIT_TABLE_RRN_FIELD['.EDIT_TABLE_RRN_FIELD.']<br>';
//var_dump($rows[$i]);echo '<hr>';

				$html .= ''
					.'<tr style="'.$high.'" valign="top">'
						#
						#	actions buttons
						#
						.str_replace($inp, $out, TABLE_ADM_ROWS_BUTTONS)
				;

					for($ii=0;$ii<$_T_FIELDS;$ii++)
					{
						#	fields
						#
						$field = 'r_'.$_FIELDS[$ii]['Field'];
						$iii = 0;
						$isREFERENCED = false;

//$_TBL_DEFS, $_T_TBL_DEFS

						while($iii<$_T_TBL_DEFS && $isREFERENCED==false)
						{
							if($_FIELDS[$ii]['Field']==$_TBL_DEFS[$iii]['referenceField'])
							{
								$isREFERENCED = true;
								$description = get_referenced_data(	$rows[$i][$field],
																	$_TBL_DEFS[$iii]['referencedTable'],
																	$_TBL_DEFS[$iii]['referencedID'],
																	$_TBL_DEFS[$iii]['referencedDesc']);
							}
							$iii++;
						}
						if($isREFERENCED==true)
						{
							$html .= ''
									.'<td valign="top" style="'.TBL_LIST_TD_STYLE.'">'
										.stripslashes($description)						#	0.0.5
									.'</td>'
							;
							$align = 'left';											#	0.0.5
						}
						else
						{
							$clean_type = get_field_type_clean($_FIELDS[$ii]['Type']);
							$is_numeric = is_field_numeric($clean_type);				#	0.0.4

							if($_FIELDS[$ii]['Comment']!='*PRIVATE'
									&& !in_array($_FIELDS[$ii]['Field'], $_RESERVED_FIELDS)
									&& in_array($clean_type, $_ALLOWED_TYPES)
							) {
								$data = $rows[$i][$field];

//echo $_FIELDS[$ii]['Type'].'<br>';

								if($_FIELDS[$ii]['Type']=='date')
								{
									$data = format_date($data);
								}
								elseif($_FIELDS[$ii]['Type']=='datetime' || $_FIELDS[$ii]['Type']=='timestamp')
								{
									$data = format_datetime($data);
								}
								else
								{
									if(strlen($data)>TBL_LIST_MAX_LENGTH)
									{
										$data = substr($data, 0, TBL_LIST_MAX_LENGTH).' [...]'
												.show_tip($data)
										;
									}
								}

								$align = 'left';												#	0.0.4
								if($is_numeric==true) { $align = 'right'; }						#	0.0.4

								$html .= ''
										//.'<td valign="top" style="'.TBL_LIST_TD_STYLE.'">'					#	0.0.4
										.'<td align="'.$align.'" valign="top" style="'.TBL_LIST_TD_STYLE.'">'	#	0.0.4
											.stripslashes($data)	#	0.0.2
//.'get_magic_quotes_gpc:'.get_magic_quotes_gpc()
										.'</td>'
								;
							}
						}
					}

				$html .= '</tr>';
			}
		}

		if(strlen($paginate)==0)
		{
			$html .= '<tr><td colspan="99">'
						.'<code>'
							.__( '*END OF DATA*', MED_LOCALE )
						.'</code>'
					.'</td></tr>'
			;
		}
		else
		{
			$html .= '<tr><td colspan="99">'
						.$paginate
					.'</td></tr>'
			;
		}

		$html .= '</table>';


		//$html .= prep_prev_next($_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage'],
		//						$_SESSION[EDIT_TABLE_NAME.'_paginate']['tot_pages'],
		//						'/?page=table_editor_adm&table='.EDIT_TABLE_NAME
		//					);

		return $html;
	}
}
if(!function_exists('data2crt'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function data2crt($row) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	load one table data row into the form fields
		#
		global $_FIELDS, $_T_FIELDS, $_RESERVED_FIELDS;

		for($i=0;$i<$_T_FIELDS;$i++)
		{
			$post_name = $_FIELDS[$i]['Field'];

			//if($_FIELDS[$i]['Comment']!='*PRIVATE' && $post_name!='RRN' && $post_name!='LAST_USER' && $post_name!='LAST_UPDATE')
			//if($_FIELDS[$i]['Comment']!='*PRIVATE' && !in_array($post_name, $_RESERVED_FIELDS))	#	0.0.4
			//{																						#	0.0.4
				#	if is not a reserved field
				#
				$field_name = 'r_'.$_FIELDS[$i]['Field'];
//echo '{'.$post_name.'}<br>';

				$value = $row[0][$field_name];
																				#									#
				if(substr($_FIELDS[$i]['Type'],0,7)=='decimal')					#	float? numeric with decimals?	#
																				#									#
				{
					$tmp = substr($_FIELDS[$i]['Type'], 8, -1);
					list($int, $dec) = explode(',', $tmp);
					$dec = (int)$dec;

//echo '{'.$fields[$i]['Type'].'}<br>'
//	.'{'.$tmp.'}<br>'
//	.'{'.$int.'}<br>'
//	.'{'.$dec.'}<br>'
//;

					$value = number_format($row[0][$field_name], $dec, ',' ,'');
				}
				elseif($_FIELDS[$i]['Type']=='date')
				{
					$value = format_date($row[0][$field_name]);
				}
				elseif($_FIELDS[$i]['Type']=='datetime' || $_FIELDS[$i]['Type']=='timestamp')
				{
					$value = format_datetime($row[0][$field_name]);
				}

				$_POST[$post_name] = $value;
			//}			#	0.0.4
		}
//var_dump($_POST);

	}
}
if(!function_exists('draw'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function draw() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	draw the table editor screen
		#
		global $_TBL_DEFS, $_T_TBL_DEFS, $_FIELDS, $_T_FIELDS, $_RESERVED_FIELDS;

		$_ALLOWED_TYPES = explode('|', MED_FLD_TYPES);		#	0.0.4

		?><h3>Table: <?=EDIT_TABLE_DESC?></h3>
		<hr class="line" />

		<input name="_action" type="hidden" value="" />
		<input name="type" type="hidden" value="<?=$_POST['type']?>" />

		<div style="width:100%;margin:0;background:#fff;padding:0px;">
		<table cellspacing="6" cellpadding="4"><?php

		#
		#	prepare the data table contents pending on the fields definitions
		#
		for($i=0;$i<$_T_FIELDS;$i++)
		{
			#	for each field
			#
			$post_name = $_FIELDS[$i]['Field'];

			#
			#	0.0.4: BEG
			#-------------
			$clean_type = get_field_type_clean($_FIELDS[$i]['Type']);
			$readonly = '';

			//if($_FIELDS[$i]['Comment']!='*PRIVATE' && !in_array($post_name, $_RESERVED_FIELDS))

			if($_FIELDS[$i]['Comment']=='*PRIVATE'
				|| in_array($post_name, $_RESERVED_FIELDS)
				|| !in_array($clean_type, $_ALLOWED_TYPES)
			) {
				$readonly = ' readonly="readonly"';
			}
			#-------------
			#	0.0.4: END
			#

				#	if is not a reserved field
				#
				$label = $_FIELDS[$i]['Comment'];
				if($label=='')
				{
					#	if there is no comment, use the field name as the label
					#
					$label = strtoupper(substr($_FIELDS[$i]['Field'], 0 ,1)).substr($_FIELDS[$i]['Field'] , 1);	#	capitalize
					$label = str_replace('_', ' ', $label);														#	replace underscore
				}

				if($_FIELDS[$i]['Key']!='') { $label .= '<code style="margin-left:4px;">Key</code>'; }	#	0.0.4 - debug

				$field_len_info = table_editor_get_field_len_info($_FIELDS[$i]['Type']);
//var_dump($field_len_info);echo '<hr>';

				#
				#	write the table row for this field
				#
				echo '<tr>'
						.'<td valign="top">'.$label.'<div id="'.$post_name.'Msg"></div></td>'
						.'<td valign="top">'
				;

//$_TBL_DEFS, $_T_TBL_DEFS
				$iii = 0;
				$isREFERENCED = false;
				while($iii<$_T_TBL_DEFS && $isREFERENCED==false)
				{
					//if($_FIELDS[$i]['Field']==$table_defs[$iii]['referenceField'])
					if($_FIELDS[$i]['Field']==$_TBL_DEFS[$iii]['referenceField'])
					{
						$isREFERENCED = true;
						#
						#	build a selection item
						#
						$selectReferenced = get_referenced_options(	$_POST[$post_name],
																	$_TBL_DEFS[$iii]['referencedTable'],
																	$_TBL_DEFS[$iii]['referencedID'],
																	$_TBL_DEFS[$iii]['referencedDesc'],
																	$post_name);

					}
					$iii++;
				}
				if($isREFERENCED==true)
				{
					echo $selectReferenced;
				}
				else
				{

					if($_FIELDS[$i]['Type']=='date')
					{
						echo show_calendar('table_editor', $post_name);
					}
					//else if($_FIELDS[$i]['Type']=='timestamp')										#	0.0.4
					else if($_FIELDS[$i]['Type']=='timestamp' || $_FIELDS[$i]['Type']=='datetime')		#	0.0.4
					{
						echo show_calendar('table_editor', $post_name, true);
					}
					else
					{
						if($field_len_info['len']==0 && $field_len_info['isTEXT']==true)
						{
							echo '<textarea class="form"'.$readonly.' name="'.$post_name.'" rows="'.(4).'" cols="'.(50).'" style="/*width:100%;*/">'.stripslashes($_POST[$post_name]).'</textarea>';	#	0.0.2, 0.0.4
						}
						else
						{
							if($field_len_info['len']>50)
							{
								$size = 50;
							}
							else
							{
								$size = $field_len_info['len'];
							}

							echo '<input class="form"'.$readonly.' type="text" name="'.$post_name.'" value="'.stripslashes($_POST[$post_name]).'" size="'.$size.'" maxlength="'.$field_len_info['len'].'" />';	#	0.0.2, 0.0.4
						}
					}
				}

				echo	'</td>'
					.'</tr>'
				;
			//}		#	0.0.4 - ex closing: if($_FIELDS[$i]['Comment']!='*PRIVATE' && !in_array($post_name, $_RESERVED_FIELDS))
		}

		?></table>
		</div><?php

	}
}
if(!function_exists('validate'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function validate() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	validate the data
		#
		global $error_msg, $thisForm;

//echo 'INPUT['.$_POST['citta'].']';	#	debug

		//if(trim($_POST['ragione'])=='')			{ return setPostError('ragioneMsg',1011,$thisForm); }


//if(1==1)
//{
//	return setPostError('cittaMsg',9999,$thisForm);	#	debug
//}
		return('OKIDOKI');
	}
}

#-------------------------------------------------------------------------------
#
#	table I/O
#
#-------------------------------------------------------------------------------
if(!function_exists('insert_table_record'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function insert_table_record() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	add a record in the table
		#
		global $USER_RRN, $dbh, $_FIELDS, $_T_FIELDS;


//	if(strlen($lang)==0)	{ $lang = $_SESSION['table_editor']['lang']; }
//	if(strlen($lang)==0)	{ $lang = $_SESSION['sitelanguage']; }


		$sql = 'INSERT INTO '.MAINSITE_DB.'.`'.EDIT_TABLE_NAME.'` '

				.'SET '
					//.'`'.EDIT_TABLE_NAME.'`.`LAST_USER` = '.(int)$USER_RRN.', '	#	0.0.2
		;

		if(defined('EDIT_TABLE_IS_LAST_USER') && EDIT_TABLE_IS_LAST_USER==true)	#	0.0.2
		{
			$sql .= '`'.EDIT_TABLE_NAME.'`.`LAST_USER` = '.(int)$USER_RRN.', ';
		}

		for($i=0;$i<$_T_FIELDS;$i++)
		{
			$post_name  = $_FIELDS[$i]['Field'];

			if($post_name!='RRN' && $post_name!='LAST_USER' && $post_name!='LAST_UPDATE')
			{
				#	if is not a reserved field
				#
				$field_len_info = table_editor_get_field_len_info($_FIELDS[$i]['Type']);

//var_dump($field_len_info);echo '<hr>';

				$field_name = 'r_'.$_FIELDS[$i]['Field'];

				if($field_len_info['dec']>0)
				{
					#	MySQL does not like the comma used as the decimal separator...
					#
					$value = str_replace(',', '.', $_POST[$post_name]);
				}

				if($field_len_info['isSTRING']==true || $field_len_info['isTEXT']==true)
				{
					$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = \''.mysql_real_escape_string($_POST[$post_name]).'\', ';
				}
				else
				{
					if($_FIELDS[$i]['Type']=='date')
					{
						$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = \''.format_calendar_to_date($_POST[$post_name]).'\', ';
					}
					elseif($_FIELDS[$i]['Type']=='datetime' || $_FIELDS[$i]['Type']=='timestamp')
					{
						$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = \''.format_calendar_to_datetime($_POST[$post_name]).'\', ';
					}
					elseif((int)$field_len_info['dec']>0)
					{
						//$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = '.number_format($_POST[$post_name], (int)$field_len_info['dec'], '.', '').', ';
						$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = '.$value.', ';
					}
					else
					{
						$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = '.(int)$_POST[$post_name].', ';
					}
				}
			}
		}
		$sql = substr($sql, 0, -2);

//echo $sql.'<br><br>';
//return;

		$sth = db_query($sql,__LINE__,__FILE__);
		$RRN = db_last_id($dbh);
		#
		return $RRN;
	}
}
if(!function_exists('update_table_record'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function update_table_record($ID) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	update the record data
		#
		global $USER_RRN, $dbh, $_FIELDS, $_T_FIELDS, $_RESERVED_FIELDS;


//	if(strlen($lang)==0)	{ $lang = $_SESSION['table_editor']['lang']; }
//	if(strlen($lang)==0)	{ $lang = $_SESSION['sitelanguage']; }


		$sql = 'UPDATE '.MAINSITE_DB.'.`'.EDIT_TABLE_NAME.'` '

				.'SET '
					//.'`'.EDIT_TABLE_NAME.'`.`LAST_USER` = '.(int)$USER_RRN.', '	#	0.0.2
		;

		if(defined('EDIT_TABLE_IS_LAST_USER') && EDIT_TABLE_IS_LAST_USER==true)		#	0.0.2
		{
			$sql .= '`'.EDIT_TABLE_NAME.'`.`LAST_USER` = '.(int)$USER_RRN.', ';
		}

		for($i=0;$i<$_T_FIELDS;$i++)
		{
			$post_name  = $_FIELDS[$i]['Field'];

			//if($post_name!='RRN' && $post_name!='LAST_USER' && $post_name!='LAST_UPDATE')	# 0.0.2
			if(!in_array($post_name, $_RESERVED_FIELDS))									# 0.0.2
			{
				#	if is not a reserved field
				#
				$field_len_info = table_editor_get_field_len_info($_FIELDS[$i]['Type']);

//var_dump($field_len_info);echo '<hr>';

				$field_name = 'r_'.$_FIELDS[$i]['Field'];

				if($field_len_info['dec']>0)
				{
					#	MySQL does not like the comma used as the decimal separator...
					#
					$value = str_replace(',', '.', $_POST[$post_name]);
				}

				if($field_len_info['isSTRING']==true || $field_len_info['isTEXT']==true)
				{
					$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = \''.mysql_real_escape_string($_POST[$post_name]).'\', ';
				}
				else
				{
					if($_FIELDS[$i]['Type']=='date')
					{
						$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = \''.format_calendar_to_date($_POST[$post_name]).'\', ';
					}
					elseif($_FIELDS[$i]['Type']=='datetime' || $_FIELDS[$i]['Type']=='timestamp')
					{
						$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = \''.format_calendar_to_datetime($_POST[$post_name]).'\', ';
					}
					elseif((int)$field_len_info['dec']>0)
					{
						//$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = '.number_format($_POST[$post_name], (int)$field_len_info['dec'], '.', '').', ';
						$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = '.$value.', ';
					}
					else
					{
						$sql .= '`'.EDIT_TABLE_NAME.'`.`'.$post_name.'` = '.(int)$_POST[$post_name].', ';
					}
				}
			}
		}
		$sql = substr($sql, 0, -2).' ';

		#
		#	0.0.4: BEG
		#-------------
		//$sql .= 'WHERE `'.EDIT_TABLE_RRN_FIELD.'` = '.(int)$ID.' ';	#	0.0.2

		if(is_numeric($ID))
		{
			$sql .= 'WHERE `'.EDIT_TABLE_RRN_FIELD.'` = '.(int)$ID.' ';
		}
		elseif(is_string($ID))
		{
			$key_name = explode('|', EDIT_TABLE_RRN_FIELD);
			$key_value = explode('|', $ID);
			$clause = 'WHERE';
			foreach($key_name as $k => $value)
			{
				$sql .= $clause.' `'.$value.'` = \''.mysql_real_escape_string($key_value[$k]).'\' ';
				$clause = 'AND';
			}
		}
		#-------------
		#	0.0.4: END
		#

//echo $sql.'<br><br>';
//die();
//return;

		$sth = db_query($sql,__LINE__,__FILE__);

		return true;
	}
}
if(!function_exists('delete_table_record'))
{
	#	delete a record
	#
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function delete_table_record($RRN) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		if((int)$RRN<1)			{ return; }
		#
		$sql = 'DELETE FROM '.MAINSITE_DB.'.`'.EDIT_TABLE_NAME.'` '

				.'WHERE `'.EDIT_TABLE_NAME.'`.`'.EDIT_TABLE_RRN_FIELD.'` = '.(int)$RRN.' '
				.'LIMIT 1 '
		;
//echo $sql.'<br><br>';
//die();

		$sth = db_query($sql,__LINE__,__FILE__);

		return true;
	}
}
if(!function_exists('get_last_table_update'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function get_last_table_update($RRN, $table_users='') {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	return a string with the user name and last update for the given record
		#
		#	TODO: connect to wordpress users table
		#

return;	#	decide how to handle LAST_UPDATE when the field does not exhist...

		if((int)$RRN==0)	{ return; }
		if(!defined('EDIT_TABLE_RRN_FIELD'))	{ define('EDIT_TABLE_RRN_FIELD', 'RRN'); }

		//if(strlen($table_users)==0)
		//{
		//	$table_users = TABLE_CAMALEO_SITE_USERS;
		//}

		$sql = 'SELECT '
					.'`'.EDIT_TABLE_NAME.'`.`LAST_UPDATE`		AS LD '
					//.'`'.EDIT_TABLE_NAME.'`.`LAST_UPDATE`		AS LD, '
					//.'`'.$table_users.'`.`id`					AS LI, '
					//.'`'.$table_users.'`.`fname`				AS LU '

				.'FROM `'.MAINSITE_DB.'`.`'.EDIT_TABLE_NAME.'` '

				//.'LEFT JOIN `'.CAMALEO_DB.'`.`'.$table_users.'` ON ( '
				//	.'`'.CAMALEO_DB.'`.`'.$table_users.'`.RRN = `'.MAINSITE_DB.'`.`'.EDIT_TABLE_NAME.'`.LAST_USER'
				//.') '

				.'WHERE `'.EDIT_TABLE_NAME.'`.`'.EDIT_TABLE_RRN_FIELD.'` = '.(int)$RRN.' ';
		;
		$sth = db_query($sql,__LINE__,__FILE__);

		if($sth[1]==0)
		{
			return false;
		}
		$row = db_fetch($sth[0],false);
//echo $sql.' =>'.$sth[1].'<br>';
//var_dump($row);echo'<hr>';

		if((int)substr($row[0]['LD'],8,2)==0)
		{
			$last_user_string = '<p class="eip_last_update">'. __( 'Last update date unknown', MED_LOCALE ) .'</p>';
		}
		else
		{
			$LU = $row[0]['LU'];
			if($LU=='') { $LU = $row[0]['LI']; }
			if($LU=='') { $LU = 'MySQL Editor'; }

			$LU = 'by '.$LU;
			$LD = substr($row[0]['LD'],8,2).'/'.substr($row[0]['LD'],5,2).'/'.substr($row[0]['LD'],0,4).' '.substr($row[0]['LD'],11,2).':'.substr($row[0]['LD'],14,2);
			$last_user_string = '<code>'.$LU.', '.$LD.'</code>';
		}
		return $last_user_string;
	}
}
#-------------------------------------------------------------------------------
#
#	buttons
#
#-------------------------------------------------------------------------------
if(!function_exists('show_add_record'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function show_add_record() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	return the code to show the 'add record' button
		#
		$html = '<div style="text-align:left;margin-top:4px;background:transparent;padding:4px;border:0px solid red;">'
					.'<form method="post" action="?page='.EDIT_TABLE_NAME.'">'
						.'<input type="submit" class="button-primary" name="btn" value="'.BTN_ADD.'" />'
					.'</form>'
			.'</div>'
		;
		return $html;
	}
}
if(!function_exists('show_back_button'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function show_back_button() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	return the code to show the 'return to previous screen' button
		#
		$html = '<div style="text-align:left;margin-top:4px;background:transparent;padding:4px;border:0px solid red;">'
				.'<input type="submit" class="button-primary" name="btn" value="' .  __( 'Return', MED_LOCALE ) . '" />'
			.'</div>'
		;
		return $html;
	}
}
if(!function_exists('draw_edit_menu'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function draw_edit_menu($edit_menu) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	return the code to showw the edit buttons
		#
		$html = '<div style="padding:3px 0px 3px 0px;width:100%;">';
			foreach($edit_menu as $val)
			{
				$html .= $val.'&nbsp;';
			}
		$html .= '</div>';
		return $html;
	}
}

#-------------------------------------------------------------------------------
#
#	show error info to the user
#
#-------------------------------------------------------------------------------
if(!function_exists('setPostError'))
{
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function setPostError($tagId, $errNo, $formName, $fldId = '') {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	show the error message in the html tag $tagId
		#
//echo 'setPostError('.$tagId.', '.$errNo.', '.$formName.', '.$fldId.')<br>';

		if(strlen($tagId)==0 && strlen($fldId)==0)
		{
			$html = '<script type="text/javascript">'
						.'alert(\''.HTML_ERROR.'\');'
				.'</script>'
			;
			return $html;
		}
		#
//		if(strlen($fldId)>0
		#
		global $error_msg;
		if(!isset($error_msg[9010])) { $error_msg[9010] = 'Error'; }
		#
		if(is_numeric($errNo))
		{
//echo '(1a)';

			$message = addslashes($error_msg[$errNo]);
			if($message=='') { $message = 'Missing message n° '.$errNo; }

			$html = '<script type="text/javascript">'
				.'setTimeout(\'showPostError()\',250);'
				.'function showPostError(){'
			;

			$html .= 'var el = document.getElementById(\''.$tagId.'\');'
						.'if(el){'
							.'el.innerHTML=\'<p class="setPostError">'.$error_msg[9010].' ('.$errNo.')<br />'.$message.'</p>\';'
				;
				if(strlen($fldId)==0 || !isset($fldId))
				{
					$fieldId = str_replace('Msg','',$tagId);						#	14/01/2009: the field did not get focus...
					$html .= 'el = document.getElementsByName(\''.$fieldId.'\');'
/*
 .'alert(\''.$fieldId.'\'+\'\\n\'
	+el[0].value+\'\n\'
	//+el[1].value+\'\n\'
	);'
*/
							.'if(el[0]){'
								.'el[0].className = \'rev\';'
								.'document.forms[\''.$formName.'\'].elements[\''.str_replace('Msg','',$fieldId).'\'].focus();'
							.'}'
						.'}else{'
							.'alert(\''.HTML_ERROR.'\nUnable to write to tag '.str_replace('Msg','',$fieldId).'\');'
						.'}'
					;
				}
				else
				{
//echo '(1b)';
					$html .= 'var el = document.getElementById(\''.$fldId.'\');'
//.'alert(el);'
							.'if(el){'
								.'el.className = \'rev\';'
								.'document.forms[\''.$formName.'\'].elements[\''.$fldId.'\'].focus();'
//								.'document.'.$formName.'.'.$fldId.'.focus();'
							.'}'
						.'}'
					;
				}
			$html .= '}</script>';
		}
		else
		{
//echo '(2)';

			$html = '<script type="text/javascript">';

			if(strlen($fldId)==0)
			{
				$html .= 'var el = document.getElementById(\''.$tagId.'\');'
						.'if(el){'
							.'el.innerHTML=\'<p class="setPostError">'.$error_msg[9010].'<br />'.addslashes($errNo).'</p>\';'
							.'if(el[0]){'
								.'el[0].className = \'rev\';'
								.'document.forms[\''.$formName.'\'].elements[\''.$tagId.'\'].focus();'
							.'}'
						.'}else{'
							.'alert(\''.HTML_ERROR.'\nUnable to write to tag '.$tagId.'\');'
						.'}'
				;
			}
			else
			{
				$html .= 'var el = document.getElementById(\''.$fldId.'\');'
						.'if(el){'
							.'el.innerHTML=\'<p class="setPostError">'.$error_msg[9010].'<br />'.addslashes($errNo).'</p>\';'
							.'if(el[0]){'
								.'el[0].className = \'rev\';'
								.'document.forms[\''.$formName.'\'].elements[\''.$fldId.'\'].focus();'
							.'}'
						.'}else{'
							.'alert(\''.HTML_ERROR.'\nUnable to write to tag '.$fldId.'\');'
						.'}'
				;
			}
			$html .= '</script>';
		}
		return $html;
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function setAlertError($errNo) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	shows a message in an popup alert
		#
		global $error_msg;
		if(!isset($error_msg[9010])) { $error_msg[9010] = 'Error'; }
		#
		if(is_numeric($errNo))
		{
//echo '(1a)';

			$message = addslashes($error_msg[$errNo]);
			if($message=='') { $message = 'Missing message n° '.$errNo; }

			$html = '<script type="text/javascript">'
						.'alert(\''.$error_msg[9010].' ('.$errNo.')'.'\n\n'.$message.'\');'
					.'</script>'
			;
		}
		else
		{
//echo '(2)';

			$html = '<script type="text/javascript">'
						.'alert(\''.$error_msg[9010].'\n\n'.addslashes($errNo).'\');'
					.'</script>'
			;

		}
		return $html;
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function setPostInfo($tagId, $info) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		#
		#	....
		#
		if($tagId=='')
		{
			$html = '<script type="text/javascript">'
						.'alert(\''.HTML_ERROR.'\');'
				.'</script>'
			;
			return $html;
		}
		#
		$html = '<script type="text/javascript">'
				.'var el = document.getElementById(\''.$tagId.'\');'
				.'if(el){'
					.'el.innerHTML=\'<p class="setPostInfo">'.addslashes($info).'</p>\';'
				.'}else{'
					.'alert(\''.HTML_ERROR.'\nUnable to write to tag '.$tagId.'\');'
				.'}'
			.'</script>'
		;
		return $html;
	}
}
?>
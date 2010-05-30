<?php
/**
 * Table Administration
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.6
 *
 * @todo Filter data: handle timestamp
 * @todo Relations: code to be completed...
 * @todo Validation checks designer + execution
 * @todo Design
 * @todo Maintenance: analyze, optimize, check, repair
 * @todo Tools: rename table, duplicate table, delete table, export .sql, import .sql
 */

require_once('med-config.php');
//define('EDIT_TABLE_NAME', $_GET['table']);


#=========================================
#
#	input parameters
#
#=========================================
//$TABLE = $_GET['table'];
$TABLE = $_GET['page'];
define('EDIT_TABLE_NAME',$TABLE);

if(is_numeric($_GET['id']))
{
	$RRN = (int)$_GET['id'];
	$isEDIT = true;
}
elseif(is_string($_GET['id']))	#	0.0.4
{
	$RRN = $_GET['id'];
	$isEDIT = true;
}

#=========================================
#
#	table definitions and related code
#
#=========================================
//require(SITE_ROOT.'contents/table_editor.inc.php');
require_once(MED_PATH.'inc/table_editor.inc.php');
//if(!defined('IN_TABLE_DEFS'))
//{
//	return;
//}
////echo 'EDIT_TABLE_MIN_PRIV:'.EDIT_TABLE_MIN_PRIV.'<br>';
//if(!defined('EDIT_TABLE_MIN_PRIV'))
//{
//	define('EDIT_TABLE_MIN_PRIV', 80);
//}
//#=========================================
//#
//#	if the user has not enough privileges, redirect to the main site page
//#
//#=========================================
//check_access($USER_ID,$USER_PW,EDIT_TABLE_MIN_PRIV);


$thisPage = basename(__FILE__,'.tpl');


//var_dump($_POST);

#=========================================
#
#	set the edit language code
#
#=========================================
if(!isset($_SESSION['table_editor']['lang']) || $_SESSION['table_editor']['lang']=='')
{
	$_SESSION['table_editor']['lang'] = $_SESSION['sitelanguage'];
}

#=========================================
#
#	initialization
#
#=========================================
//$TABLE = $_GET['table'];
$TABLE = EDIT_TABLE_NAME;

//unset($_SESSION[EDIT_TABLE_NAME.'_paginate']); # debug
if(strlen($TABLE)>0)
{
	if((int)($_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage']==0))
	{
		$_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage'] = 0;
	}
	$items_per_page = ITEMS_PER_PAGE;

	#
	#	pagination commands parameters
	#
	$is_top		= '';
	$paginate	= '';

	if(isset($_GET['pagenum']))
	{
		$paginate = $_GET['paginate'];
	}

	if($_POST['btn']==FILTER)			#	0.0.6
	{
		$paginate = '';
		$_GET['top'] = 'yes';
	}
//echo $_POST['btn'].'='.FILTER.'<br>';

	if(	(isset($_GET['top']) && $_GET['top']=='yes')
		//	||
		//(count($_POST)==0)
		)
	{
		$_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage'] = 0;
	}
	#
//	require(SITE_ROOT.'contents/table_editor.inc.php');

	$limits = calc_query_limits($thisPage, $paginate, $TABLE);

//echo '<p class="todo">$limits='.$limits.'</p>'
//	.'<p class="todo">$thisPage='.$thisPage.'</p>'
//	.'<p class="todo">ITEMS_PER_PAGE='.ITEMS_PER_PAGE.'</p>'
//	.'<p class="todo">thispage='.$_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage'].'</p>'
//	.'<p class="todo">tot_pages='.$_SESSION[EDIT_TABLE_NAME.'_paginate']['tot_pages'].'</p>';

}
if(function_exists('clear_tab_data')) { echo clear_tab_data(EDIT_TABS); }

//echo '$_POST[action]:'.$_POST[action].'<br>';
//echo '$_POST['cat_']:'.$_POST['cat_'];
//var_dump($_POST);die;

#=========================================
#
#	get the site available languages
#
#=========================================
$lang = $_POST['lang'] ? $_POST['lang'] : $_SESSION['table_editor']['lang'];

#=========================================
#
#	get the query filters
#
#=========================================
$filters = '';
$filters_relations = '';														#	0.0.6
foreach($_POST as $key => $val)
{
	if(substr($key,0,6)=='filter')
	{
		$filters .= $key.FILTER_SPLITTER.$val.FILTER_SPLITTER;
	}
	else if(substr($key,0,12)=='_rel_filter_')									#	0.0.6
	{
		$filters_relations .= $key.FILTER_SPLITTER.$val.FILTER_SPLITTER;
	}
}
$filters = substr($filters, 0, -strlen(FILTER_SPLITTER));
$filters_relations = substr($filters_relations, 0, -strlen(FILTER_SPLITTER));	#	0.0.6

if($filters=='' && isset($_SESSION[EDIT_TABLE_NAME.'_filters']))
{
	foreach($_SESSION[EDIT_TABLE_NAME.'_filters'] as $key => $val)
	{
		$filters .= $key.FILTER_SPLITTER.$val.FILTER_SPLITTER;
	}
	$filters = substr($filters, 0, -strlen(FILTER_SPLITTER));
}

if($filters_relations=='' && isset($_SESSION[EDIT_TABLE_NAME.'_rel_filters']))	#	0.0.6
{
	foreach($_SESSION[EDIT_TABLE_NAME.'_rel_filters'] as $key => $val)
	{
		$filters_relations .= $key.FILTER_SPLITTER.$val.FILTER_SPLITTER;
	}
	$filters_relations = substr($filters_relations, 0, -strlen(FILTER_SPLITTER));
}


//var_dump($_SESSION[EDIT_TABLE_NAME.'_filters']);
//echo '<p class="todo">$filters='.$filters.'</p>';	#	filter_nome|-filter-|irel*|-filter-|filter_nazione|-filter-|uni*

//echo '$filters_relations:<br>';var_dump($filters_relations);echo '<hr>';


#=========================================
#
#	handle the button commands
#
#=========================================
/////////
/////////
/////////
unset(	$_SESSION[EDIT_TABLE_NAME.'_recmode'],
		$_SESSION[EDIT_TABLE_NAME.'_addtype']
);
/////////
/////////
/////////


//echo '$_POST<br>';
//var_dump($_POST);


if($_POST['_action']==BTN_ADD || $_POST['_action']==DELETE)
{
	$_POST['btn'] = $_POST['_action'];
}

switch($_POST['btn'])
{
	#-----------------------
	case BTN_ADD:
	case BTN_ADD_PRODUCT:
	#-----------------------
		//$_SESSION[EDIT_TABLE_NAME.'_recmode'] = '*add';
		//$_SESSION[EDIT_TABLE_NAME.'_addtype'] = '0';
		//session_write_close();
//var_dump($_SESSION);
//die();
		//header('Location: ?page=med_edit&table='.EDIT_TABLE_NAME);
		echo '<script type="text/javascript">window.location=\'?page=med_edit&table='.EDIT_TABLE_NAME.'&mode=add&type=0\';</script>';
		exit();
		break;
		#
	#-----------------------
	case BTN_ADD_OPTION:
	#-----------------------
		//$_SESSION[EDIT_TABLE_NAME.'_recmode'] = '*add';
		//$_SESSION[EDIT_TABLE_NAME.'_addtype'] = '1';
		//session_write_close();
		//header('Location: ?page=med_edit&table='.EDIT_TABLE_NAME);
		echo '<script type="text/javascript">window.location=\'?page=med_edit&table='.EDIT_TABLE_NAME.'&mode=add&type=1\';</script>';
		exit();
		break;
		#
//	#-----------------------
//	case B_COPY_LANG:
//	#-----------------------
//		$validated_language = site_available_language($_POST['copy_to_lang']);
//		if(function_exists('copy_to_lang')
//			&& (!defined('IS_LANG_INDEPENDENT') || IS_LANG_INDEPENDENT==false)
//			&& $validated_language['lang']==$_POST['copy_to_lang']
//		)
//		{
////			$rows = get_table_data('*all', $lang, $limits, $filters);
//			if($TABLE_ADM_PARAM)
//			{
//				$rows = get_table_data('*all', $lang, $limits, $filters, $TABLE_ADM_PARAM);
//			}
//			else
//			{
//				$rows = get_table_data('*all', $lang, $limits, $filters);
//			}
//			$result = copy_to_lang($rows, $_SESSION['table_editor']['lang'], $_POST['copy_to_lang']);
//			if($result>0)
//			{
//				$pop = '<script type="text/javascript">'
//							.'pop(\'Copiati '.$result.' records\',\'/index.php?page=table_editor_adm&table='.$TABLE.'\');'
//				.'</script>';
//				echo $pop;
//			}
//			exit();
//		}
//		break;
//		#
	#
	#-----------------------
	case DELETE:
	#-----------------------
		if(is_numeric($_POST['id']) && $_POST['id']>0)
		{
			$result = delete_table_record($_POST['id']);
			if($result==true)
			{
				echo '<script type="text/javascript">'
						.'pop(\'<h3>' .  __( 'Data Deleted', MED_LOCALE ) . '</h3>\',\''.'?page=' . EDIT_TABLE_NAME.'\');'
					.'</script>';
				exit();
				#~~~~~~~~~~~~~~~~~~~~
				//$pop='<script type="text/javascript">'
				//			.'pop(\'Dati eliminati\',\'/index.php?page=table_editor_adm&table='.$TABLE.'\');'
				//.'</script>';
				//echo $pop;
				//exit();
			}
		}
		break;
		#
	#-----------------------
	default:
	#-----------------------
}


?><form id="table_editor_adm" name="table_editor_adm" method="post" action="<?php

		$QUERY_STRING = $_SERVER['QUERY_STRING'];
		if($QUERY_STRING!='') { $QUERY_STRING = '?'.$QUERY_STRING; }
		#
		echo $_SERVER['PHP_SELF'].$QUERY_STRING;

?>">

<h2><?php

	echo 'Table contents for: ';

	if(defined('EDIT_TABLE_DESC') && EDIT_TABLE_DESC!='')
	{
		echo EDIT_TABLE_DESC;
	}

	//
	//	[now I am using the table name if I do not find the comment]
	//
	//else
	//{
	//	echo '<code>{missing the table <u>Comment</u> used for the description of table <b>'.EDIT_TABLE_NAME.'</b>}</code>';
	//}

	echo '<code style="margin-left:4px;">Key &raquo; <b>'.EDIT_TABLE_RRN_FIELD.'</b></code>';	#	0.0.4 - debug


?></h2>
<hr class="line" />
<input name="lang_" type="hidden" value="<?=$_POST['lang']?>" />
<input name="id" type="hidden" value="" />
<input name="_action" type="hidden" value="" />

<div style="/*width:100%;*/"><?php

	if(!defined('IS_LANG_INDEPENDENT') || IS_LANG_INDEPENDENT==false)
	{
		//include(SITE_ROOT.'common/fun2inc/get_site_languanges.inc');
		require(MED_PATH.'inc/get_site_languages.inc.php');
		$rows_lang = get_site_languages();
		$t = count($rows_lang);

		?><div style="float:left;width:30%;border-right:1px solid #ccc;padding-bottom:20px;margin-bottom:6px;">
			<h5>Site available languages</h5>
			<ul style="list-style-type:none;"><?php

				for($i=0;$i<$t;$i++)
				{
					$checked = '';
					if($rows_lang[$i]['lang']==$lang)
					{
						$checked = 'checked="checked" ';
					}
					echo '<li>'
							.'<input type="radio" name="lang" id="lang_'.$i.'" value="" '
									.'onclick="javascript:'
//												.'lang_'.$i.'.checked=true;'
												.'this.checked=true;'
												.'sndReq(\'table_adm\',\'recordList\',\''.$rows_lang[$i]['lang'].AJAX_PARMS_SPLITTER.$TABLE
															.AJAX_PARMS_SPLITTER.$limits.AJAX_PARMS_SPLITTER.$filters.AJAX_PARMS_SPLITTER.''.AJAX_PARMS_SPLITTER.$filters_relations.'\');" '	#	0.0.6
									.$checked.'/> '
							.'<label for="lang_'.$i.'" '
									.'onclick="javascript:'
//												.'lang_'.$i.'.checked=true;'
												.'document.getElementById(\'lang_'.$i.'\').checked=true;'
												.'sndReq(\'table_adm\',\'recordList\',\''.$rows_lang[$i]['lang'].AJAX_PARMS_SPLITTER.$TABLE
															.AJAX_PARMS_SPLITTER.$limits.AJAX_PARMS_SPLITTER.$filters.AJAX_PARMS_SPLITTER.''.AJAX_PARMS_SPLITTER.$filters_relations.'\');'		#	0.0.6
							.'">'
								.'<img src="/img/flags/'.$rows_lang[$i]['flag'].'.png" width="16" height="10" border="0" style="margin-right:8px;" />'
								.$rows_lang[$i]['descrizione']
							.'</label>'
					.'</li>';
				}

			?></ul><?php


		?></div><?php
////////
//TODO//
////////
			if(function_exists('show_filters'))		{ echo show_filters(); }
			if(function_exists('show_relations'))	{ echo show_relations(); }
			if(function_exists('show_add_record'))	{ echo show_add_record(); }
////////
//TODO//
////////

		?><div id="recordList" style="float:right;width:67%;">&nbsp;</div><?php
	}
	else
	{
		?><div style="clear:both;"><?php

			?><div style="float:right;margin-top:-7px;"><?php

				if(function_exists('show_filters'))				{ echo show_filters('setup'); }
				if(function_exists('show_filters'))				{ echo show_filters(); }
				if(function_exists('show_relations'))			{ echo show_relations(); }
////////
//TODO//
////////
				//if(function_exists('show_validations_checks'))	{ echo show_validations_checks(); }
				//if(function_exists('show_design_table'))		{ echo show_design_table(); }
				//if(function_exists('show_maint_table'))			{ echo show_maint_table(); }
				//if(function_exists('show_tools_table'))			{ echo show_tools_table(); }
////////
//TODO//
////////

			?></div><?php

			?><div style="margin-top:-10px;float:left;"><?php

				if(function_exists('show_add_record')) { echo show_add_record(); }

			?></div><?php


		?><div id="recordList" style="clear:both;width:100%;">&nbsp;</div><?php
	}


	#
	#	force restart from the last visited page
	#
	if(isset($_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage']))
	{
		$limits = $_SESSION[EDIT_TABLE_NAME.'_paginate']['thispage'];
	}

//var_dump($_SESSION[EDIT_TABLE_NAME.'_paginate']);echo '<hr>';
//echo 'limits['.$limits.']<br>';

?>
	<div style="clear:both;"></div>
</div>
</form>

<script type="text/javascript">
	sndReq('table_adm','recordList','<?=$lang.AJAX_PARMS_SPLITTER.$TABLE.AJAX_PARMS_SPLITTER.$limits.AJAX_PARMS_SPLITTER.$filters.AJAX_PARMS_SPLITTER.''.AJAX_PARMS_SPLITTER.$filters_relations?>');	//	0.0.6
</script>
<?php

#=========================================
#
#	build the icons menu
#
#=========================================
$edit_menu = array();
$edit_menu[] = '<img src="/img/modules/adm-off.png" alt="'.TO_TBL_LIST.'" title="'.TO_TBL_LIST.'" '
				.'onmouseover="javascript:imgSwap(this);" onmouseout="javascript:imgSwap(this);" align="absmiddle" style="cursor:pointer;" '
				.'onclick="javascript:window.location=\'/index.php?page=table_editor_adm\';" />'
;

if(defined('SET_TABLE_ORDER') && SET_TABLE_ORDER==true)
{
	$edit_menu[] = '<img src="/img/modules/order-off.png" alt="'.BTN_ORDER.'" title="'.BTN_ORDER.'" '
					.'onmouseover="javascript:imgSwap(this);" onmouseout="javascript:imgSwap(this);" align="absmiddle" style="cursor:pointer;" '
					.'onclick="javascript:window.location=\'/index.php?page=table_editor_ord&table='.$TABLE.'\';" />'
	;
}

if(function_exists('show_add_record'))
{
	$edit_menu[] = '<img src="/img/modules/add-off.png" alt="'.BTN_ADD.'" title="'.BTN_ADD.'" '
					.'onmouseover="javascript:imgSwap(this);" onmouseout="javascript:imgSwap(this);" align="absmiddle" style="cursor:pointer;" '
					.'onclick="javascript:document.table_editor_adm._action.value=\''.BTN_ADD.'\';document.table_editor_adm.submit();" />'
	;
//				.'<input type="submit" class="button_green" name="btn" value="'.BTN_ADD.'" />'
}

#=========================================
#
#	add the icon menu at the end of the EIP floating menu
#
#=========================================
//global $_EIP_FLOAT_MENU_XTRA;
//
//$_EIP_FLOAT_MENU_XTRA = '<div class="eip_xtra">'
//
//	.draw_edit_float_menu($edit_menu)
//
//.'</div>';

?>
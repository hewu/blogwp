<?php
/**
 * Table Editor
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.4
 */

#
#	$_SESSION['table_editor']['lang'] is the language the user wants to edit
#

//$thisForm = basename(__FILE__, '.tpl');
$thisForm = 'table_editor';


//echo '_recmode:'.$_SESSION[EDIT_TABLE_NAME.'_recmode'];
//echo '_addtype:'.$_SESSION[EDIT_TABLE_NAME.'_addtype'];
//var_dump($_SESSION);



#=========================================
#
#	input parameters
#
#=========================================
$TABLE = $_GET['table'];
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

$_REC_MODE = '';
if(isset($_GET['mode']) && $_GET['mode']=='add' && !isset($_GET['id']))
{
	$_REC_MODE = '*add';
}

//echo $_REC_MODE.'<br>';

#=========================================
#
#	table definitions and related code
#
#=========================================
require(MED_PATH.'inc/table_editor.inc.php');

if(!defined('IN_TABLE_DEFS'))
{
	return;
}
//echo 'EDIT_TABLE_MIN_PRIV:'.EDIT_TABLE_MIN_PRIV.'<br>';
if(!defined('EDIT_TABLE_MIN_PRIV'))
{
	define('EDIT_TABLE_MIN_PRIV', 80);
}
#=========================================
#
#	if the user has not enough privileges, redirect to the main site page
#
#=========================================
//check_access($USER_ID,$USER_PW,EDIT_TABLE_MIN_PRIV);

#=========================================
#
#	get the required record
#
#=========================================
if($_REC_MODE=='*add')
{
	$row_data = array();
}
//elseif(is_numeric($_GET['id']))	#	0.0.4
else								#	0.0.4
{
	$row_data = get_table_data($RRN, $_SESSION['table_editor']['lang']);
	if(!is_array($row_data))
	{
		?><div class="warningBox">

			Record ID "<?=$RRN?>" not found in table "<?=$TABLE?>"

		</div><?php
		return;
	}
}


//var_dump($row_data);

//echo '$_POST[action]:'.$_POST[action].'<br>';
//echo '$_POST['cat_']:'.$_POST['cat_'];
//var_dump($_POST);

#=========================================
#
#	editing languages
#
#=========================================
//$lang = $_POST['lang'] ? $_POST['lang'] : $_SESSION['table_editor']['lang'];
//$tmp = get_site_languanges($lang);
////var_dump($tmp);
//$flag = '<img border="0" width="16" height="10" src="/img/flags/'.$tmp[0]['flag'].'.png" valign="absmiddle" />';

#=========================================
#
#	if the table data is splitted in tabs
#	get the id of the active one
#
#=========================================
$tab = (int)$_GET['tab'];
if($tab<1 || $tab>4 || ($row_data[0]['isOption']==1 && ($tab==2 || $tab==3)))
{
	$tab = 1;
}


#=========================================
#
#	if the user reloads the page without
#	submitting the form (ie by pressing [F5])
#	data must be reloaded into the form
#
#=========================================
//if(count($_POST)==0 && $RRN>0)	#	0.0.4
if(count($_POST)==0 && $RRN!='')	#	0.0.4
{
	$_POST['_action'] = RELOAD.'*';
}
//echo '<br>'.count($_POST).'|'.$RRN.'|'.$_POST['_action'].'<br>';
//var_dump($_POST);

#=========================================
#
#	execute the required command
#
#=========================================
switch($_POST['_action'])
{
	#-----------------------
	case RELOAD:
	case RELOAD.'*':
	#-----------------------
		data2crt($row_data);
		break;
	#
	#-----------------------
	case SAVE:
	#-----------------------
		$feedback = validate();
//echo '<p class="todo">'.$feedback.'</p>';

		if($feedback=='OKIDOKI')
		{
			//if(isset($_COOKIE['camaleo_table_return2']) && strlen($_COOKIE['camaleo_table_return2'])>0)
			//{
			//	$return2 = $_COOKIE['camaleo_table_return2'];
			//	setcookie('camaleo_table_return2','',(time()-3600));
			//}
			//else
			//{
				$return2 = '?page='.EDIT_TABLE_NAME;
			//}

			if($_REC_MODE=='*add')
			{
				$result = insert_table_record($RRN);
				if($result==true)
				{
					echo '<script type="text/javascript">'
							.'pop(\'<h3>' .  __( 'Data Added', MED_LOCALE ) . '</h3>\',\''.$return2.'\');'
						.'</script>';
					exit();
					#~~~~~~~~~~~~~~~~~~~~
				}
			}
			else
			{
				$result = update_table_record($RRN);
				if($result==true)
				{
					echo '<script type="text/javascript">'
							.'pop(\'<h3>' .  __( 'Data Updated', MED_LOCALE ) . '</h3>\',\''.$return2.'\');'
						.'</script>';
					exit();
					#~~~~~~~~~~~~~~~~~~~~
				}
			}
		}
		else
		{
			$feedback_str = $feedback;
		}
		break;
	#
	#-----------------------
	case DELETE:
	#-----------------------
		$result = delete_table_record($RRN);
		if($result==true)
		{
			echo '<script type="text/javascript">'
					.'pop(\'<h3>' .  __( 'Data Deleted', MED_LOCALE ) . '</h3>\',\''.'?page='.EDIT_TABLE_NAME.'\');'
				.'</script>';
			exit();
			#~~~~~~~~~~~~~~~~~~~~
		}
		break;
		#
	#-----------------------
}

#=========================================
#
#	print the screen
#
#=========================================
/*
//?><form id="table_editor" name="table_editor" method="post" enctype="multipart/form-data" action="<?php
//
//		$inp = array();							$out = array();
//		$inp[] = '&skid='.$_GET['skid'];		$out[] = '';
//		$inp[] = 'skid='.$_GET['skid'];			$out[] = '';
//		#
//		$QUERY_STRING = str_replace($inp, $out, $_SERVER['QUERY_STRING']);
//		if($QUERY_STRING!='') { $QUERY_STRING = '?'.$QUERY_STRING; }
//		#
//		echo $_SERVER['PHP_SELF'].$QUERY_STRING;
//
//?>"><?php
*/
?><form id="table_editor" name="table_editor" method="post" enctype="multipart/form-data" action="<?php

		//$inp = array();							$out = array();
		//$inp[] = '&skid='.$_GET['skid'];		$out[] = '';
		//$inp[] = 'skid='.$_GET['skid'];			$out[] = '';

		$QUERY_STRING = str_replace($inp, $out, $_SERVER['QUERY_STRING']);
		if($QUERY_STRING!='') { $QUERY_STRING = '?'.$QUERY_STRING; }

		echo $_SERVER['PHP_SELF'].$QUERY_STRING;

?>"><?php

//echo $_SERVER['PHP_SELF'].$QUERY_STRING;

echo draw()
	.'<div align="right">'
		.get_last_table_update($RRN)
	.'</div>'
	.'<hr class="line" />'
;

?></form>

<div id="*none" style="display:none;"><!-- --></div><?php

#=========================================
#
#	print the error message
#
#=========================================
echo $feedback_str;

#=========================================
#
#	build the icons menu
#
#=========================================
if(!defined('isOVERRIDE_FLOAT_MENU') || constant('isOVERRIDE_FLOAT_MENU')==false)		#	30/11/2009
{
	$edit_menu = array();

	//if($RRN>0)	#	0.0.4
	if($RRN!='')	#	0.0.4
	{
		$edit_menu[] = '<img src="'.PLUGIN_LINK.'img/modules/reload-off.png" alt="'.RELOAD.'" title="'.RELOAD.'" '
						.'onmouseover="javasript:this.src=\''.PLUGIN_LINK.'img/modules/reload-on.png\';" '
						.'onmouseout="this.src=\''.PLUGIN_LINK.'img/modules/reload-off.png\'" '
						.'align="absmiddle" style="cursor:pointer;" '
						.'onclick="javascript:document.table_editor._action.value=\''.RELOAD.'\';'
											.'document.table_editor.submit();" />'
		;
	}

	$edit_menu[] = '<img src="'.PLUGIN_LINK.'img/modules/save-off.png" alt="'.SAVE.'" title="'.SAVE.'" '
						.'onmouseover="javasript:this.src=\''.PLUGIN_LINK.'img/modules/save-on.png\';" '
						.'onmouseout="this.src=\''.PLUGIN_LINK.'img/modules/save-off.png\'" '
						.'align="absmiddle" style="cursor:pointer;" '
						.'onclick="javascript:document.table_editor._action.value=\''.SAVE.'\';'
										.'document.table_editor.submit();" />';

	$edit_menu[] = EDIT_MENU_SEPARATOR;

	if($RRN>0 && function_exists('delete_table_record'))
	{
		$edit_menu[] = '<img src="'.PLUGIN_LINK.'img/modules/delete-off.png" alt="'.DELETE.'" title="'.DELETE.'" '
						.'onmouseover="javasript:this.src=\''.PLUGIN_LINK.'img/modules/delete-on.png\';" '
						.'onmouseout="this.src=\''.PLUGIN_LINK.'img/modules/delete-off.png\'" '
						.'align="absmiddle" style="cursor:pointer;" '
						.'onclick="javascript:'
										.'if(confirm(\''.__( 'Are you sure that you want to delete this record?', MED_LOCALE ).'\')==false) {'
											.'return false;'
										.'};'
										.'document.table_editor._action.value=\''.DELETE.'\';'
										.'document.table_editor.submit();'
										.'" />';
		$edit_menu[] = EDIT_MENU_SEPARATOR;
	}

	$edit_menu[] = '<img src="'.PLUGIN_LINK.'img/modules/adm-off.png" alt="'.TO_TBL_LIST.'" title="'.TO_TBL_LIST.'" '
						.'onmouseover="javasript:this.src=\''.PLUGIN_LINK.'img/modules/adm-on.png\';" '
						.'onmouseout="this.src=\''.PLUGIN_LINK.'img/modules/adm-off.png\'" '
						.'align="absmiddle" style="cursor:pointer;" '
						//.'onclick="javascript:window.location=\'/index.php?page=table_editor_adm&table='.$TABLE.'\';" />';
						.'onclick="javascript:window.location=\'?page='.$TABLE.'\';" />';

	#=========================================
	#
	#	add the icon menu at the end of the EIP floating menu
	#
	#=========================================
	//$menu = draw_edit_float_menu($edit_menu);
	global $_EIP_FLOAT_MENU_XTRA;

	$_EIP_FLOAT_MENU_XTRA = '<div class="eip_xtra">'

		.'<div style="float:left;">'
			.draw_edit_menu($edit_menu)
		.'</div>'

	.'</div>';
}
//echo draw_edit_float_menu($edit_menu)
echo $_EIP_FLOAT_MENU_XTRA;

?>
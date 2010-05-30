<?php
/**
 * Handle tables items ordering
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.5
 * @todo adaptation for myEASYdb
 */

#=========================================
#
#	initialization
#
#=========================================
$TABLE = $_GET['table'];

#=========================================
#
#	table definitions and related code
#
#=========================================
require_once(SITE_ROOT.'contents/table_editor.inc.php');
//require(SITE_ROOT.'contents/table_editor.inc.php');
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
check_access($USER_ID,$USER_PW,EDIT_TABLE_MIN_PRIV);


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
#	handle the button commands
#
#=========================================
//unset(	$_SESSION[EDIT_TABLE_NAME.'_recmode'],
//		$_SESSION[EDIT_TABLE_NAME.'_addtype']
//);

//var_dump($_POST);die();

$_DIR		= $_POST['dir'];
$_IDR		= $_POST['id'];
$_IDRafter	= $_POST['idAfter'];

//var_dump($_POST);
//echo '$_DIR:'.$_DIR.'<br>';
//echo '$_IDR:'.$_IDR.'<br>';


switch($_POST['_action'])
{
//	#-----------------------
//	case BTN_ORDER:
//	#-----------------------
//		$_SESSION[EDIT_TABLE_NAME.'_recmode'] = '*add';
//		$_SESSION[EDIT_TABLE_NAME.'_addtype'] = '0';
//		session_write_close();
//		header('Location: /?page=table_editor&table='.EDIT_TABLE_NAME);
//		exit();
//		break;
//		#
	#-----------------------
	case BTN_MOVE:
	#-----------------------
//echo '_action:'.BTN_MOVE.'<br>';

		if($_DIR=='after' && (int)$_IDR>-1 && (int)$_IDRafter>-1)
		{
			$result = move_after($_IDR, $_IDRafter);
		}
		if($result=='OKIDOKI')
		{
			$msg = 'Dati aggiornati';
			$pop = '<script type="text/javascript">pop('
						.'\''.str_replace("'","&rsquo;",$msg).'\','
						.'\'index.php?page=table_editor_ord&table='.$TABLE.'\','
						.'1'
			.');</script>';
			echo $pop;
			exit();
		}
		break;
		#
	#-----------------------
	default:
	#-----------------------
}


?><form id="table_editor_ord" name="table_editor_ord" method="post" action="<?php

		$inp = array();							$out = array();
		$inp[] = '&skid='.$_GET['skid'];		$out[] = '';
		$inp[] = 'skid='.$_GET['skid'];			$out[] = '';
		#
		$QUERY_STRING = str_replace($inp, $out, $_SERVER['QUERY_STRING']);
		if($QUERY_STRING!='') { $QUERY_STRING = '?'.$QUERY_STRING; }
		#
		echo $_SERVER['PHP_SELF'].$QUERY_STRING;

?>">
<h5><?php

	if(defined('EDIT_TABLE_DESC')) { echo EDIT_TABLE_DESC; } else { echo 'Generic table editor: setting show order'; }

?></h5>
<hr class="line" />
<input type="hidden" name="lang_" value="<?=$_POST['lang']?>" />
<input type="hidden" name="_action" value="" />
<input type="hidden" name="dir" value="" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="idAfter" value="" />


<div style="width:100%;"><?php

	if(!defined('IS_LANG_INDEPENDENT') || IS_LANG_INDEPENDENT==false)
	{
		include(SITE_ROOT.'common/fun2inc/get_site_languanges.inc');
		$rows_lang = get_site_languanges();
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
												.'sndReq(\'table_ord\',\'recordList\',\''.$rows_lang[$i]['lang'].AJAX_PARMS_SPLITTER.$TABLE
															.AJAX_PARMS_SPLITTER.$limits.AJAX_PARMS_SPLITTER.$filters.'\');" '
									.$checked.'/> '
							.'<label for="lang_'.$i.'" '
									.'onclick="javascript:'
//												.'lang_'.$i.'.checked=true;'
												.'document.getElementById(\'lang_'.$i.'\').checked=true;'
												.'sndReq(\'table_ord\',\'recordList\',\''.$rows_lang[$i]['lang'].AJAX_PARMS_SPLITTER.$TABLE
															.AJAX_PARMS_SPLITTER.$limits.AJAX_PARMS_SPLITTER.$filters.'\');'
							.'">'
								.'<img src="/img/flags/'.$rows_lang[$i]['flag'].'.png" width="16" height="10" border="0" style="margin-right:8px;" />'
								.$rows_lang[$i]['descrizione']
							.'</label>'
					.'</li>';
				}

			?></ul><?php


		?></div><?php

//			if(defined('SET_TABLE_ORDER') && SET_TABLE_ORDER==true) { echo show_set_order(); }

		?><div id="recordList" style="float:right;width:67%;">&nbsp;</div><?php
	}
	else
	{
//		if(defined('SET_TABLE_ORDER') && SET_TABLE_ORDER==true) { echo show_set_order(); }

		?><div id="recordList" style="float:right;width:100%;">&nbsp;</div><?php
	}

?>
	<div style="clear:both;"></div>
</div>
</form>

<script type="text/javascript">
	sndReq('table_ord','recordList','<?=$lang.AJAX_PARMS_SPLITTER.$TABLE?>');
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
					.'onclick="javascript:window.location=\'/index.php?page=table_editor_adm&table='.$TABLE.'\';" />'
;

#=========================================
#
#	add the icon menu at the end of the EIP floating menu
#
#=========================================
global $_EIP_FLOAT_MENU_XTRA;

$_EIP_FLOAT_MENU_XTRA = '<div class="eip_xtra">'

	.draw_edit_float_menu($edit_menu)

.'</div>';

$_EIP_FLOAT_MENU_XTRA .= '<div id="move2selector" '
								.'style="'
									.'clear:left;'
									.'text-align:center;'
									.'display:none;'
									.'background:'.$tr_2move_bg.';'
//									.'padding:4px 0;'
									.'padding:4px;'
									.'border-top:1px solid #999;'
									.'border-right:1px solid #444;'
									.'border-left:1px solid #999;'
									.'border-bottom:1px solid #444;'
									.'-moz-border-radius:3px;'
									.'border-radius:3px;'
									.'margin-top:3px;'
								.'">'

	.'<input id="rowID" type="hidden" name="rowID" value="" size="5" maxlength="5" />'
	.'<input id="thisRRN" type="hidden" name="thisRRN" value="" size="5" maxlength="5" />'
	.'<input id="destRRN" type="hidden" name="destRRN" value="" size="5" maxlength="5" />'
	.'<input type="button" class="button" value="'.BTN_MOVE.'" '
			.'onclick="javascript:document.table_editor_ord.dir.value=\'after\';'
								.'document.table_editor_ord._action.value=\''.BTN_MOVE.'\';'
								.'document.table_editor_ord.id.value=document.getElementById(\'thisRRN\').value;'
								.'document.table_editor_ord.idAfter.value=document.getElementById(\'destRRN\').value;'

//.'alert(document.table_editor_ord.id.value);'

								.'document.table_editor_ord.submit();" />'

	.' '
	.'<input type="button" class="button_green" style="font-size:9px;" value="'.B_CLOSE.'" '
			.'onclick="javascript:'
								#
								#	reset any eventual selection for a 'move to' operation
								#
								.'var el=document.getElementById(\'move2selector\');'
								.'el.style.display=\'none\';'
								.'var r=document.getElementById(\'rowID\');'
								.'el=document.getElementById(\'row_\'+r.value);'
								.'el.style.background=\''.$tr_sel_bg.'\';'
								.'el=document.getElementsByName(\'startSelector\');'
								.'for(var i=0;i<el.length;i++){'
									.'el[i].style.display=\'block\';'
								.'};'
								.'el=document.getElementsByName(\'destSelector\');'
								.'for(var i=0;i<el.length;i++){'
									.'el[i].style.display=\'none\';el[i].style.background=\''.$btn_moveo_bg.'\';'
								.'};" />'
.'</div>';

#~~~~~~~~~~~~~~~~~~~~~~~~~~
function move_after($RRN, $afterRRN, $lang='')
#~~~~~~~~~~~~~~~~~~~~~~~~~~
{
	global $USER_RRN, $TABLE;
	#
	#	move an element after the requested one
	#
	if((int)$afterRRN<1) { return; }
	#
	if(defined('SET_TABLE_ORDER_KEYS'))
	{
		$keys = explode('|', SET_TABLE_ORDER_KEYS);
		$kt = count($keys);
	}
	if($kt<1) { return; }
	#
//echo 'kt:'.$kt.'<br>';
//var_dump($keys);echo '<br>';

	#
//	if($lang=='')	{ $lang = $_SESSION['sitelanguage']; }
//	$lang = mysql_real_escape_string($lang);
	#
	#	get the position of the record after which we need to move
	#
	$sql = 'SELECT RRN, showOrd ';
	for($i=0;$i<$kt;$i++)
	{
		$sql .= ', '.$keys[$i].' ';
	}
	$sql .= 'FROM '.MAINSITE_DB.'.`'.$TABLE.'` '
			.'WHERE RRN  = '.$afterRRN.' '
	;
	$sth = db_query($sql,__LINE__,__FILE__);
	$row = db_fetch($sth[0]);
	$dest_rrn = $row[0]['RRN'];
	$dest_position = $row[0]['showOrd'];
//echo $sql.'<br>';
//echo '$dest_rrn:'.$dest_rrn.'<br>';
//echo '$dest_position:'.$dest_position.'<br>';
//var_dump($row);echo '<hr>';
//die();

	if($dest_rrn<1) { return; }

	#
	#	get all the following records
	#
	$sql = 'SELECT RRN, showOrd FROM `'.MAINSITE_DB.'`.`'.$TABLE.'` '
			.'WHERE showOrd > '.$dest_position.' '
			.'AND   RRN  <> '.$RRN.' '
	;
	for($i=0;$i<$kt;$i++)
	{
		$sql .= 'AND '.$keys[$i].' = \''.mysql_real_escape_string($row[0][$keys[$i]]).'\' ';
	}
	$sql .= 'ORDER BY showOrd ASC ';
	#
	$sth = db_query($sql,__LINE__,__FILE__);
	$rows = db_fetch($sth[0]);
	$t = $sth[1];
//echo $sql.', found:'.$sth[1].'<br>';
//var_dump($rows);echo '<hr>';
//die();

	#
//	if($dest_position!='' && $dest_rrn>0 && $rows[0]['RRN']>0)
	if($dest_position!='' && $dest_rrn>0)
	{
		$sql = 'LOCK TABLES `'.MAINSITE_DB.'`.`'.$TABLE.'` WRITE';
		$sth = db_query($sql,__LINE__,__FILE__);
		#
		#	move the requested record on the new position
		#
		$dest_position++;
		$sql = 'UPDATE `'.MAINSITE_DB.'`.`'.$TABLE.'` '
				.'SET '
					.'LAST_USER	='.$USER_RRN.', '
					.'showOrd	='.$dest_position.' '

				.'WHERE RRN='.$RRN
		;
//echo $sql.'<br>';

		$sth = db_query($sql,__LINE__,__FILE__);
		#
		for($i=0;$i<$t;$i++)
		{
			#	move all the records coming after
			#
			$dest_position++;
			$sql = 'UPDATE `'.MAINSITE_DB.'`.`'.$TABLE.'` '
					.'SET '
						.'LAST_USER	='.$USER_RRN.', '
						.'showOrd	='.$dest_position.' '

					.'WHERE RRN='.$rows[$i]['RRN']
			;
//echo $sql.'<br>';

			$sth = db_query($sql,__LINE__,__FILE__);
		}
		#
		#	get all the records with the same keys
		#
		$sql = 'SELECT RRN, showOrd FROM `'.MAINSITE_DB.'`.`'.$TABLE.'` '
				.'WHERE 1 = 1 '
		;
		for($i=0;$i<$kt;$i++)
		{
			$sql .= 'AND '.$keys[$i].' = \''.mysql_real_escape_string($row[0][$keys[$i]]).'\' ';
		}
		$sql .= 'ORDER BY showOrd ASC ';
//echo '<hr>'.$sql.'<br>';

		#
		$sth = db_query($sql,__LINE__,__FILE__);
		$rows = db_fetch($sth[0]);
		$t = $sth[1];
		for($i=0;$i<$t;$i++)
		{
			#	resequence the records
			#
			$dest_position++;
			$sql = 'UPDATE `'.MAINSITE_DB.'`.`'.$TABLE.'` '
					.'SET '
						.'LAST_USER	='.$USER_RRN.', '
						.'showOrd	='.$i.' '

					.'WHERE RRN='.$rows[$i]['RRN']
			;
//echo $sql.'<br>';

			$sth = db_query($sql,__LINE__,__FILE__);
		}
		#
		$sql = 'UNLOCK TABLES';
		$sth = db_query($sql,__LINE__,__FILE__);
		#
//die();
		return 'OKIDOKI';
	}
	#
	return;
}

?>

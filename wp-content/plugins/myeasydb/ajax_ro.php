<?php
/**
 * AJAX responder
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.6
 */

#-------------------------------------------------------------
#
#	first of all let's check if we are called by our server
#
#-------------------------------------------------------------
/*
//var_dump($_SERVER);

	["HTTP_REFERER"]=>string(71) "http://example.com/.../..."
	["HTTP_HOST"]=>   string(7) "example.com"
	["SERVER_NAME"]=> string(7) "example.com"

	["SERVER_ADDR"]=> string(9) "127.0.0.1"
	["REMOTE_ADDR"]=> string(9) "127.0.0.1"
*/
$tmp = explode('://', $_SERVER['HTTP_REFERER']);
$path = explode('/', $tmp[1]);
$referer = $path[0];

//echo '$tmp1['.$tmp[1].']';
//echo '$path0['.$path[0].']';
//echo '$referer['.$referer.']';

if(	($_SERVER['HTTP_HOST'] != $_SERVER['SERVER_NAME'])
		||
	($_SERVER['HTTP_HOST'] != $referer)
		||
	($_SERVER['SERVER_NAME'] != $referer) )
{

	echo '<div align="center">'

			.'There is an issue with the caller...'

		.'</div>'
	;
	die();
}

#
#	the caller is fine
#
$splitter_tag	= '|-ajax-tag-|';
$splitter_block	= '|-ajax-block-|';
$splitter_cmd	= '|-ajax-cmd-|';

#
#	initialize some variables
#
define('AJAX_CALLER', true);
require('med-config.php');

/*===========================================================

	The js caller can send parameters both as GET or POST.

	POST is generally considered more sure and it also allows
	for longer parameters to be sent.

	If you like to configure the js to pass the parameters
	by GET, you need to change the $_INPUT assignment few
	lines here below.

  ===========================================================*/
//echo '$_GET:';var_dump($_GET);echo "\n\n";
//echo '$_POST:';var_dump($_POST);echo "\n\n";

//$_INPUT = $_GET;
$_INPUT = $_POST;

if(!is_array($_INPUT) || count($_INPUT)==0)
{
	#	in any case we expect parameters to be sent as an array
	#	if not, better to quit...
	#
	exit();
}

if(strpos($_INPUT['parms'], AJAX_PARMS_SPLITTER) !== false)
{
	#	if there is more than one parameter, they are separated
	#	by the constant defined in AJAX_PARMS_SPLITTER
	#
	$parms = explode(AJAX_PARMS_SPLITTER, $_INPUT['parms']);
}
else
{
	#	there is only one parameter, to keep the same logic
	#	we create an array of parameters anyway
	#
	$parms = array();
	$parms[0] = $_INPUT['parms'];
}

#
#	$parms
#
#	{n} = parameters
#
//define(AJAX_DEBUG, true);	#	uncomment to see debug code

$parms_string = '';
if(defined('AJAX_DEBUG') && AJAX_DEBUG==true)
{
	$t = count($parms);
	$parms_string = '<p class="todo">';
	for($i=0;$i<$t;$i++)
	{
		$parms_string .= '$parms['.$i.']:'.$parms[$i].'<br />';
	}
	$parms_string .= '</p>';
}
//die();

#
#	we do not want the result to be cached
#
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: nocache');
header('Expires: Fri, 31 Dec 1999 23:59:59 GMT');


echo $_INPUT['tag']		#	the tag id we are going to write to
	.$splitter_tag		#	splitter for the remaining output
	.$parms_string		#	filled only for debug purpose
;


#================================
#
#	its time to prepare some
#	output data...
#
#================================
switch($_INPUT['action'])
{
	#---------------------------
	case 'get_med_information':
	#---------------------------
		#
		#	get info about myEASYdb from its site
		#
		#	0: username
		#	1: password
		#
		$fp = fsockopen('myeasydb.com', 80, $errno, $errstr, 10);

//echo '$fp['.$fp.']<br>';
//echo '$errno['.$errno.']<br>';
//echo '$errstr['.$errstr.']<br>';

		if (!$fp) {
			#
			#	HTTP ERROR
			#
			$version = 'HTTP ERROR';
			$subscription = 'HTTP ERROR';

		} else {
			#
			#	get the latest version number
			#
			$header = "GET /commands/getVersion.php HTTP/1.0\r\n"
						."Host: myeasydb.com\r\n"
						."Connection: Close\r\n\r\n"
			;
			fwrite($fp, $header);

			$result = '';
			while (!feof($fp)) {
				$result .= fgets($fp, 1024);
			}

//echo '$result['.$result.']<br>';

			$version = MED_VERSION;
			$p = strpos($result, 'myEASYdb[', 0);
			if($p!==false)
			{
				$beg = $p + 9;
				$end = strpos($result, ']', $p);
				$version = substr($result, $beg, ($end-$beg));
			}

			fclose($fp);
			$fp = fsockopen('myeasydb.com', 80, $errno, $errstr, 10);

//echo '$fp['.$fp.']<br>';
//echo '$errno['.$errno.']<br>';
//echo '$errstr['.$errstr.']<br>';

			#
			#	get the subscription info
			#
			$req = 'u=' . urlencode(stripslashes($username)) . '&p=' . urlencode(stripslashes($password));

			$header = "POST /commands/getSubscription.php HTTP/1.0\r\n"
						."Host: myeasydb.com\r\n"
						."Content-Type: application/x-www-form-urlencoded\r\n"
						."Content-Length: " . strlen($req) . "\r\n\r\n"
			;
			fwrite($fp, $header . $req);

			$result = '';
			while (!feof($fp)) {
				$result .= fgets($fp, 1024);
			}

//echo '$req['.$req.']<br>';
//echo '$result['.$result.']<br>';

			$subscription = '';
			$p = strpos($result, 'myEASYdb[', 0);
			if($p!==false)
			{
				$beg = $p + 9;
				$end = strpos($result, ']', $p);
				$subscription = substr($result, $beg, ($end-$beg));
			}

			fclose($fp);
		}

//echo '$version['.$version.']<br>';
//echo '$subscription['.$subscription.']<br>';

		//$info = array();
		//$info['version'] = $version;
		//$info['subscription'] = $subscription;

		$_SESSION['myeasydbVersion']		= $version;
		$_SESSION['myeasydbSubscription']	= $subscription;

		//echo $splitter_block
		//	.$js;

		exit();
		break;
		#
	#---------------------------
	case 'get_med_start_here_contents':
	#---------------------------
		#
		#	get the start here page contents from myEASYdb site
		#
		$fp = fsockopen('myeasydb.com', 80, $errno, $errstr, 10);

//echo '$fp['.$fp.']<br>';
//echo '$errno['.$errno.']<br>';
//echo '$errstr['.$errstr.']<br>';

		if (!$fp) {
			#
			#	HTTP ERROR
			#
			$version = 'HTTP ERROR';
			$subscription = 'HTTP ERROR';

		} else {
			#
			#	get the latest version number
			#
			$header = "GET /service/start_here.php HTTP/1.0\r\n"
						."Host: myeasydb.com\r\n"
						."Connection: Close\r\n\r\n"
			;
			fwrite($fp, $header);

			$result = '';
			while (!feof($fp)) {
				$result .= fgets($fp, 1024);
			}

//echo '$result['.$result.']<br>';

			$html = '';
			$needle = 'myEASYdb~~~BEG[';
			$p = strpos($result, $needle, 0);
			if($p!==false)
			{
				$beg = $p + strlen($needle);
				$end = strpos($result, ']END~~~myEASYdb', $p);
				$html = substr($result, $beg, ($end-$beg));
			}

			fclose($fp);
			$fp = fsockopen('myeasydb.com', 80, $errno, $errstr, 10);

//echo '$fp['.$fp.']<br>';
//echo '$errno['.$errno.']<br>';
//echo '$errstr['.$errstr.']<br>';
		}

		echo $html;

		//echo $splitter_block
		//	.$js;

		exit();
		break;
		#
	#---------------------------
	case 'table_adm':
	#---------------------------
		#
		#	$parms
		#
		#	0: language
		#	1: table name
		#	2: limits or pagination command (next|prev|{page number})
		#	3: filters for the query in this form: filter_name|-filter-|irelan*|-filter-|filter_country|-filter-|united*
		#	4: items per page
		#	5: filters relations
		#
		$TABLE = $parms[1];
		if(strlen($parms[0])>0)
		{
			$_SESSION['table_editor']['lang'] = $parms[0];
		}
		else
		{
			$parms[0] = $_SESSION['table_editor']['lang'];
		}

		$limits = $parms[2];
		$filters = $parms[3];
		$filters_relations = $parms[5];

//echo '<p class="todo">$filters_relations='.$filters_relations.'</p>';

		define('EDIT_TABLE_NAME',$TABLE);
		require_once(MED_PATH.'inc/table_editor.inc.php');

//echo ' $TABLE_ADM_PARAM<br>';var_dump($TABLE_ADM_PARAM);echo '<hr>';


		//if($parms[2]=='next' || $parms[2]=='prev')
		//{
			//$limits = calc_query_limits('table_editor_adm', $parms[2], $TABLE);			#	0.0.4
			$limits = calc_query_limits('table_editor_adm', $parms[2], $TABLE, $parms[4]);	#	0.0.4
		//}
		//else
		//{
		//	#	update the totals anyways, it may be needed when the language changes
		//	#
		//	calc_query_limits('table_editor_adm', $parms[2], $TABLE);
		//	$_SESSION['table_editor_adm'.'_paginate']['thispage'] = 0;
		//}

//echo '<p class="todo">$limits='.$limits.'</p>';
//echo '<p class="todo">$filters='.$filters.'</p>';

//echo ' $TABLE_ADM_PARAM<br>';var_dump($TABLE_ADM_PARAM);echo '<hr>';

		if($TABLE_ADM_PARAM)
		{
			$rows = get_table_data('*all', $parms[0], $limits, $filters, $TABLE_ADM_PARAM);
		}
		else
		{
			$rows = get_table_data('*all', $parms[0], $limits, $filters, $filters_relations);
		}

		if(function_exists('get_table_data_list'))
		{
			echo get_table_data_list($rows, 'table_editor_adm', $parms[4]);
		}
		else
		{
			echo '<p style="color:red;">Missing function <b>get_table_data_list</b></p>';
			//var_dump($rows);
			echo '<hr>';
		}

		//echo $splitter_block
		//	.$js;

		exit();
		break;
		#
	#---------------------------
	case 'tables_list':
	#---------------------------
		#
		#	0: database name
		#	1: 'check_med_tables' to check if the med own tables are present
		#	2: 'table name' to avoid including it in the output
		#	3: number of columns to use
		#	4: 'link2fields' to create a link that will open a list of fields for the table
		#	5: field name to use for the identifier if p4 is passed
		#	6: referenced table
		#	7: referenced id field
		#	8: number of the input/hidden field
		#	9: referenced description field
		#
		if($parms[0]=='')
		{
			exit();
		}
		$tables = table_get_tables($parms[0]);
		$t = count($tables);
//var_dump($tables);

//echo $parms[0].'|'.$parms[1].'|'.$parms[2].'|'.$parms[3].'|'.$parms[4].'|'.$parms[5].'|'.$parms[6].'|'.$parms[7].'|'.$parms[8];
//exit();

		if((int)$parms[3]==0)
		{
			$maxCols = 3;						#	0.0.5
		}
		else
		{
			$maxCols = (int)$parms[3] - 1;
		}


		if($t==0 && $parms[1]=='')				#	0.0.6
		{
			echo '<div class="error"><p>' . __( 'No tables found in this database', MED_LOCALE ) . '</p></div>';
		}
		else
		{
			echo '<div class="updated">'
				.'<table width="100%" style="border:1px;" cellspacing="4">'
				.'<tr>';

			$c = 0;
			$t_med_own = 0;
			$MED_OWN_TABLES = explode('|', MED_OWN_TABLES);

			for($i=0;$i<$t;$i++)
			{
				if(substr($tables[$i]['Name'], 0, 4)!='med_' || $parms[1]=='check_med_tables')
				{
					if($c>$maxCols)
					{
						$c = 0;
						echo '</tr><tr>';
					}

					if($parms[2]=='' || ($parms[2]!='' && $parms[2]!=$tables[$i]['Name']))
					{
						if($parms[4]=='link2fields' && $parms[5]!='')
						{
							$background = 'transparent';
							if($tables[$i]['Name']==$parms[6])
							{
								$background = '#ddd';
								$js = 'sndReq(\'fields_list\',\'med_'.$parms[5].'_relation_field\',\''
											. $tables[$i]['Name'] . AJAX_PARMS_SPLITTER
											. $parms[7] . AJAX_PARMS_SPLITTER
											. $parms[8] . AJAX_PARMS_SPLITTER
											. $parms[9]
										.'\');'
								;
							}

							echo '<td id="td_table_'.$parms[5].'_'.$i.'" nowrap width="20%" style="font-size:10px;background:'.$background.';">'
									.'<span style="text-decoration:underline;cursor:pointer;" '
											.'onclick="javascript:'
															.'resetSelectedTdTables('.$t.',\'td_table_'.$parms[5].'_\');'

//.'alert('.$parms[8].'+\', \'+document.getElementById(\'__field_'.$parms[8].'\').value);'

															.'document.getElementById(\'__referencedTable_'.$parms[8].'\').value=\''.$tables[$i]['Name'].'\';'
															.'document.getElementById(\'td_table_'.$parms[5].'_'.$i.'\').style.background=\'#ddd\';'
															.'sndReq(\'fields_list\',\'med_'.$parms[5].'_relation_field\',\''
																		. $tables[$i]['Name'] . AJAX_PARMS_SPLITTER
																		. $parms[5] . AJAX_PARMS_SPLITTER
																		. $parms[8] . AJAX_PARMS_SPLITTER
																		. $parms[9]
														. '\');">'
												.$tables[$i]['Name']
									.'</span>'
								.'</td>'
							;
						}
						else
						{
							echo '<td nowrap width="20%" style="font-size:10px;">'
									.$tables[$i]['Name']
								.'</td>'
							;
						}

						$c++;

						if($parms[1]=='check_med_tables')
						{
							if(in_array($tables[$i]['Name'], $MED_OWN_TABLES))
							{
								$t_med_own++;
							}
						}
					}
				}
			}
			while($c<($maxCols+1))
			{
				echo '<td nowrap width="20%" style="font-size:10px;">'
					.'</td>'
				;
				$c++;
			}

			echo '</tr>'
				.'</table>'
				.'</div>'
			;

			if($parms[1]=='check_med_tables' && count($MED_OWN_TABLES)!=$t_med_own)
			{
				echo '<div class="error"><p>' . __( 'One or more myEASYdb own tables are missing.', MED_LOCALE ) . '</p>';

				#
				#	0.0.6: BEG
				#-------------
//echo $MED_OWN_TABLES.'>'.$t_med_own.'<br>';
//var_dump($MED_OWN_TABLES);echo '<br>';

				$sql = 'SELECT * FROM information_schema.SCHEMA_PRIVILEGES '
						.'WHERE GRANTEE = "\''.DB_USER.'\'@\''.DB_HOST.'\'" '
						.'AND   TABLE_SCHEMA = \''.mysql_real_escape_string(str_replace('_', '\_', $parms[0])).'\' '
				;
				$sth = db_query($sql,__LINE__,__FILE__);
				$tmp = db_fetch($sth[0]);
				$t = count($tmp);

//echo $sql.', '.$sth[1];
//var_dump($tmp);

				$allowedPriv = array();
				for($i=0;$i<$t;$i++)
				{
					if($tmp[$i]['PRIVILEGE_TYPE']=='CREATE') { $allowedPriv[] = 'CREATE'; }
					if($tmp[$i]['PRIVILEGE_TYPE']=='SELECT') { $allowedPriv[] = 'SELECT'; }
					if($tmp[$i]['PRIVILEGE_TYPE']=='INSERT') { $allowedPriv[] = 'INSERT'; }
					if($tmp[$i]['PRIVILEGE_TYPE']=='DELETE') { $allowedPriv[] = 'DELETE'; }
					if($tmp[$i]['PRIVILEGE_TYPE']=='UPDATE') { $allowedPriv[] = 'UPDATE'; }
					if($tmp[$i]['PRIVILEGE_TYPE']=='ALTER')  { $allowedPriv[] = 'ALTER'; }
				}
				#-------------
				#	0.0.6: END
				#

				if($sth[1]==0 || count($allowedPriv)==6)			#	0.0.6
				{
					echo '<br /><input class="button-primary" type="submit" name="btn" '
								.'onclick="javascript:if(confirm(\'' . __( 'Are you sure?', MED_LOCALE ) . '\')==false) {'
														.'return false;'
													.'};" '
								.'value="' . CREATE_BTN .'" />'
					;
					//$js = 'document.getElementById(\'_action\').value="AddMedOwnTbls";';
				}
				else								#	0.0.6
				{
					echo '<p style="color:red;">'
							. __( 'It looks like that you haven\'t sufficient privileges on this database.', MED_LOCALE ) . '<br /><br />'
							. __( 'Please ask your administrator to grant you at least the following privileges: ', MED_LOCALE ) . '<br />'
							. '<b>CREATE</b>, <b>SELECT</b>, <b>INSERT</b>, <b>DELETE</b>, <b>UPDATE</b>, <b>ALTER</b>' . '<br /><br />'
							. __( 'User information: ', MED_LOCALE )
							. '<b>'. DB_USER.'@'.DB_HOST . '</b><br /><br />'
							. __( 'Database: ', MED_LOCALE )
							. '<b>'. $parms[0] . '</b>'
						. '</p>';
				}

				echo '</div>';
			}
		}

		//$js .= '';
		if($js!='')
		{
			echo $splitter_block
				.$js;
		}

		exit();
		break;
		#
	#---------------------------
	case 'fields_list':
	#---------------------------
		#
		#	0: table
		#	1: field to link to
		#	2: number of the input/hidden field
		#	3: field to use for the description
		#
		$fields = table_get_fields(MAINSITE_DB, $parms[0]);
		$t = count($fields);
//var_dump($fields);

//echo $parms[0].'|'.$parms[1];

		if($t==0)
		{
			echo '<div class="error"><p>' . __( 'No fields found in this table', MED_LOCALE ) . '</p></div>';
		}
		else
		{
			echo '<div class="updated">'
				.'<table width="100%" style="border:1px;" cellspacing="4">'
			;

			for($i=0;$i<$t;$i++)
			{

//echo($fields[$i]['Field'].'=='.$parms[1]);
				if(substr($fields[$i]['Field'], 0, 5)!='LAST_')
				{
					$background = 'transparent';
					if($fields[$i]['Field']==$parms[1])
					{
						$background = '#ddd';
						$js = 'document.getElementById(\'__referencedID_'.$parms[2].'\').value=\''.$fields[$i]['Field'].'\';'
								//.'alert(\''.$fields[$i]['Field'].'\')'	#	debug
						;
					}

					$checked = '';
					if($fields[$i]['Field']==$parms[3])
					{
						$checked = ' checked="checked"';
					}

					echo '<tr>'
							.'<td id="td_field_'.$parms[0].'_'.$parms[1].'_'.$i.'" nowrap width="20%" style="font-size:10px;background:'.$background.';">'
//.'<input type="radio" name="radio_'.$parms[0].'_'.$parms[1].'" value="'.$parms[1].'"'.$checked.' /> '
								.'<input type="radio" name="__referencedDesc_'.$parms[2].'" value="'.$fields[$i]['Field'].'"'.$checked.' /> '
								.'<span style="cursor:pointer;" '
										.'onclick="javascript:'
													.'resetSelectedTdTables('.$t.',\'td_field_'.$parms[0].'_'.$parms[1].'_\');'
													.'document.getElementById(\'__referencedID_'.$parms[2].'\').value=\''.$fields[$i]['Field'].'\';'
													.'document.getElementById(\'td_field_'.$parms[0].'_'.$parms[1].'_'.$i.'\').style.background=\'#ddd\';'
												. '">'
									.$fields[$i]['Field']
								.'</span>'
							.'</td>'
						.'</tr>'
					;
				}
			}

			echo ''
				.'</table>'
				.'</div>'
			;
		}

		if($js!='')
		{
			echo $splitter_block
				.$js;
		}

		exit();
		break;
		#
	/***************************
	 *	relations
	 *	@since 0.0.1
	*/
	#---------------------------
	case 'relation_add':
	#---------------------------
		#
		#	0: field
		#	1: total referenced fields
		#	2: the source table
		#
		$item_i = (int)$parms[1];

		$html = ''
				.'<table cellspacing="6" cellpadding="0" border="0" width="98%" align="right">'
					.'<tr>'
					//.'<td valign="top" align="right">'
					//	.'new relation'
					//.'</td>'
					.'<td valign="top" align="right">'
						.'<span '
#
#	do we really need this? | BEG
#
						//.'<span style="text-decoration:underline;cursor:pointer;" '
						//		.'onclick="javascript:sndReq(\'tables_list\',\'med_'.$parms[1].'_relation_table\',\''
						//									. MAINSITE_DB . AJAX_PARMS_SPLITTER
						//									. '' . AJAX_PARMS_SPLITTER
						//									. EDIT_TABLE_NAME . AJAX_PARMS_SPLITTER
						//									. (1) . AJAX_PARMS_SPLITTER
						//									. 'link2fields' . AJAX_PARMS_SPLITTER
						//									. $parms[0]
						//								. '\');"'
#
#	do we really need this? | END
#
							.'>'
							.$parms[0]
						.'</span>'
						//.' {'.$type.'}'

						.'<div style="display:none;border:0px dotted green;">'
							.'<input id="__field_'.$item_i.'" name="__field_'.$item_i.'_" type="hidden" value="'.$parms[0].'" />'
							.'<input id="__referencedTable_'.$item_i.'" name="__referencedTable_'.$item_i.'_" type="hidden" value="" />'
							.'<input id="__referencedID_'.$item_i.'" name="__referencedID_'.$item_i.'_" type="hidden" value="" />'
						.'</div>'

						.'<div>'
							.'<input type="button" class="button-secondary" style="margin:4px -14px 0 0;" name="btn" value="&nbsp;'
									.__( 'Discard this relation', MED_LOCALE )
									.'&nbsp;" onclick="javascript:'
													.'if(confirm(\''.__( 'Are you sure that you want to discard this relation?', MED_LOCALE ).'\')==false) {'
														.'return false;'
													.'};'
													.'var relations=document.getElementById(\'_relations_addChildren\');'
													.'var element=document.getElementById(\'_relations_add_'.$item_i.'\');'
													.'relations.removeChild(element);'
													.'var t=document.getElementById(\'__total_referenced_on_screen_\');'
													.'t.value=t.value-1;'
													.'sndReq(\'set_unreferenced_fields_selection\',\'not_ref_fields_container\',\''

															. MAINSITE_DB . AJAX_PARMS_SPLITTER
															. $parms[2] . AJAX_PARMS_SPLITTER
															//. $parms[0]
													.'\');'
												.'" />'
						.'</div>'
					.'</td>'
					.'<td valign="top">'
						.'&rsaquo;'
					.'</td>'
					.'<td valign="top">'
						.'<div id="med_'.$parms[0].'_relation_table">'
							.'<img src="'.PLUGIN_LINK.'img/loading.gif" />'
						.'</div>'
					.'</td>'
					.'<td valign="top">'
						.'&raquo;'
					.'</td>'
					.'<td valign="top">'
						.'<div id="med_'.$parms[0].'_relation_field">'
							//.'<img src="'.PLUGIN_LINK.'img/loading.gif" />'
						.'</div>'
					.'</td>'
				.'</tr>'
				.'</table>'
		;
//echo $html;

		$t = $item_i + 1;

		$inp = array();		$out = array();
		$inp[] = '"';		$out[] = '\"';
		$inp[] = "'";		$out[] = "\'";
		$html = str_replace($inp, $out, $html);

		$js = ''
				.'var relations=document.getElementById(\'_relations_addChildren\');'
				.'var element=document.createElement(\'div\');'
					.'element.setAttribute(\'id\', \'_relations_add_'.$item_i.'\');'
					.'element.innerHTML = \''.$html.'\';'
				.'relations.appendChild(element);'

				.'sndReq(\'tables_list\',\'med_'.$parms[0].'_relation_table\',\''

					. MAINSITE_DB . AJAX_PARMS_SPLITTER
					. '' . AJAX_PARMS_SPLITTER
					. $parms[2] . AJAX_PARMS_SPLITTER
					. (1) . AJAX_PARMS_SPLITTER
					. 'link2fields' . AJAX_PARMS_SPLITTER
					. $parms[0] . AJAX_PARMS_SPLITTER
					. $referencedTable . AJAX_PARMS_SPLITTER
					. $referencedID . AJAX_PARMS_SPLITTER
					. $item_i
				.'\');'

			.'document.getElementById(\'__total_referenced_on_screen_\').value='.$t.';'
			.'sndReq(\'set_unreferenced_fields_selection\',\'not_ref_fields_container\',\''

					. MAINSITE_DB . AJAX_PARMS_SPLITTER
					. $parms[2] . AJAX_PARMS_SPLITTER
					. $parms[0]
			.'\');'
		;
//echo $js;

		echo $splitter_block
			.$js;

		exit();
		break;
		#
	#---------------------------
	case 'set_unreferenced_fields_selection':
	#---------------------------
		#
		#	0: database
		#	1: table
		#	2: field (not mandatory), if passed avoid to include it in the select
		#
		define('EDIT_TABLE_NAME', $parms[1]);
		require(MED_PATH.'inc/table_editor.inc.php');

		$not_referenced_fields = get_not_referenced_fields();

		$html_not_referenced = '<select id="not_ref_fields" name="not_referenced_fields">'
			.'<option value="">' . __( 'Select a new field to reference and click on this button &raquo;', MED_LOCALE ) . '</option>'
		;
		foreach($not_referenced_fields as $field)
		{
			if($field!=$parms[2])
			{
				$html_not_referenced .= '<option value="'.$field.'">'.$field.'</option>';
			}
		}
		$html_not_referenced .= '</select>';

		echo $html_not_referenced;


		//echo $splitter_block
		//	.$js;

		exit();
		break;
		#
	#---------------------------
	case 'update_references':
	#---------------------------
		#
		#	0: how many fields are referenced
		#	1: the source table
		#
		$js .= 'sndReq(\'update_references_exec\',\'update_references_info\',\'\'';

		for($i=0;$i<(int)$parms[0];$i++)
		{
			$js .= ''
					.'+document.getElementById(\'__field_'.$i.'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
					.'+document.getElementById(\'__referencedTable_'.$i.'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
					.'+document.getElementById(\'__referencedID_'.$i.'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
					.'+getCheckedValue(document.getElementsByName(\'__referencedDesc_'.$i.'\'))+\'' . AJAX_PARMS_SPLITTER . '\''
			;

		}
		$js = substr($js,0,-1)
				.$parms[1].'\''
			.');'
		;


//$js = 'el = document.getElementsByName(\'__referencedDesc_0\');'
//		.'alert(getCheckedValue(el));'
//;

//echo $js;
//exit();

		echo $splitter_block
			.$js;

		exit();
		break;
		#
	#---------------------------
	case 'update_references_exec':
	#---------------------------
		#
		#	0 ~ {n-1}: the input values in groups of 4, in the following order
		#
		#				field name
		#				referenced table
		#				referenced ID
		#				referenced description
		#
		#	{n}: the source table
		#
		$t = count($parms);
		$c = 0;
		$records_data = array();

		for($i=0;$i<$t;$i++)
		{
//echo $i.'='.$parms[$i].'<br>';	#debug

			if($c>3)
			{
				$values = substr($values, 0 ,-1);

				$records_data[] = $values;
//echo $values.'<br>';	#debug

				$values = '';
				$c = 0;
			}
			$values .= $parms[$i].',';
			$c++;
		}
//echo substr($values, 0 ,-1).'<br>';	#debug
		$table = substr($values, 0 ,-1);

		foreach($records_data as $key => $val)
		{
			if($val!='')
			{
				list($field, $referencedTable, $referencedID, $referencedDesc) = explode(',', $val);

				if($field!='' && $referencedTable!='' && $referencedID!='' && $referencedDesc!='')
				{
					$sql = 'SELECT referencedID '

							.'FROM `'.MED_OWN_DB.'`.`'.MED_TABLE_DEFS.'` '
							.'WHERE `table` = \'' . mysql_real_escape_string($table) . '\' '
							.'AND   `referenceField` = \'' . mysql_real_escape_string($field) . '\' '
					;
//echo $sql.'<hr>';	#debug

					$sth = db_query($sql,__LINE__,__FILE__);
					if($sth[1]==0)
					{
						$sql = 'INSERT INTO `'.MED_OWN_DB.'`.`'.MED_TABLE_DEFS.'` '

								.'SET '
									.'`table` = \'' . mysql_real_escape_string($table) . '\', '
									.'`referenceField` = \'' . mysql_real_escape_string($field) . '\', '
									.'`referencedTable` = \'' . mysql_real_escape_string($referencedTable) . '\', '
									.'`referencedID` = \'' . mysql_real_escape_string($referencedID) . '\', '
									.'`referencedDesc` = \'' . mysql_real_escape_string($referencedDesc) . '\' '
						;
					}
					else
					{
						$sql = 'UPDATE `'.MED_OWN_DB.'`.`'.MED_TABLE_DEFS.'` '

								.'SET '
									.'`referencedTable` = \'' . mysql_real_escape_string($referencedTable) . '\', '
									.'`referencedID` = \'' . mysql_real_escape_string($referencedID) . '\', '
									.'`referencedDesc` = \'' . mysql_real_escape_string($referencedDesc) . '\' '

								.'WHERE `table` = \'' . mysql_real_escape_string($table) . '\' '
								.'AND   `referenceField` = \'' . mysql_real_escape_string($field) . '\' '
						;
					}
//echo $sql.'<hr>';	#debug

					$sth = db_query($sql,__LINE__,__FILE__);
				}
			}
		}

		echo '<p style="color:green;font-weight:bold;">' . __( 'References updated successfully!', MED_LOCALE ) . '</p>';

		$js = ''
				.'el_display_toggler(\'_relations_Data\',\'_relations_TogglerImg\');'
				.'window.location.reload(true);'
		;
		echo $splitter_block
			.$js;

		exit();
		break;
		#
	#---------------------------
	case 'delete_reference':
	#---------------------------
		#
		#	0: {n} of the field to delete
		#	1: the source table
		#
		//echo $output;

		$js = 'sndReq(\'delete_reference_exec\',\'update_references_info\',\'\''
				.'+document.getElementById(\'__field_'.$parms[0].'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
				.'+document.getElementById(\'__referencedTable_'.$parms[0].'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
				.'+document.getElementById(\'__referencedID_'.$parms[0].'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
		;

		$js = substr($js,0,-1)
				.$parms[1].'\''
			.');'
		;

		echo $splitter_block
			.$js;

		exit();
		break;
		#
	#---------------------------
	case 'delete_reference_exec':
	#---------------------------
		#
		#	0: field
		#	1: referenced table
		#	2: referenced id
		#	3: the source table
		#
		$sql = 'DELETE FROM `'.MED_OWN_DB.'`.`'.MED_TABLE_DEFS.'` '

				.'WHERE `table` = \'' . mysql_real_escape_string($parms[3]) . '\' '
				.'AND   `referenceField` = \'' . mysql_real_escape_string($parms[0]) . '\' '
				.'AND   `referencedTable` = \'' . mysql_real_escape_string($parms[1]) . '\' '
				.'AND   `referencedID` = \'' . mysql_real_escape_string($parms[2]) . '\' '

				.'LIMIT 1 '
		;
//echo $sql.'<hr>';	#debug
//die();

		$sth = db_query($sql,__LINE__,__FILE__);

		echo '<p style="color:green;font-weight:bold;">' . __( 'Reference deleted succesfully!', MED_LOCALE ) . '</p>';

		#
		#	0.0.6: BEG
		#-------------
		$js = ''
			.'var t=document.getElementById(\'__total_referenced_on_screen_\').value-1;'
			.'sndReq(\'update_references\',\'update_references_info\',\'\'+t+\'' . AJAX_PARMS_SPLITTER
						. EDIT_TABLE_NAME
			.'\');'
		;
		echo $splitter_block
			.$js;
		#-------------
		#	0.0.6: END
		#

		exit();
		break;
		#
	/***************************
	 *	filters
	 *	@since 0.0.6
	*/
	#---------------------------
	case 'filter_add':
	#---------------------------
		#
		#	0: field
		#	1: total filtered fields
		#	2: the source table
		#
		$item_i = (int)$parms[1];

		$html = ''
				.'<table cellspacing="6" cellpadding="0" border="0" width="98%" align="right">'
					.'<tr>'
					//.'<td valign="top" align="right">'
					//	.'new filter'
					//.'</td>'
					.'<td valign="top" align="right">'
						.'<span>'
							.$parms[0]
						.'</span>'
						//.' {'.$type.'}'

						.'<div style="display:none;border:0px dotted green;">'
							.'<input id="__field_'.$item_i.'" name="__field_'.$item_i.'_" type="hidden" value="'.$parms[0].'" />'
//.'<input id="__filteredTable_'.$item_i.'" name="__filteredTable_'.$item_i.'_" type="hidden" value="" />'
//.'<input id="__filteredID_'.$item_i.'" name="__filteredID_'.$item_i.'_" type="hidden" value="" />'
						.'</div>'

						.'<div>'
							.'<input type="button" class="button-secondary" style="margin:4px -14px 0 0;" name="btn" value="&nbsp;'
									.__( 'Discard this filter', MED_LOCALE )
									.'&nbsp;" onclick="javascript:'
													.'if(confirm(\''.__( 'Are you sure that you want to discard this filter?', MED_LOCALE ).'\')==false) {'
														.'return false;'
													.'};'
													.'var filters=document.getElementById(\'_filters_addChildren\');'
													.'var element=document.getElementById(\'_filters_add_'.$item_i.'\');'
													.'filters.removeChild(element);'
													.'var t=document.getElementById(\'__total_filters_on_screen_\');'
													.'t.value=t.value-1;'
													.'sndReq(\'set_unfiltered_fields_selection\',\'not_ref_fields_container\',\''

															. MAINSITE_DB . AJAX_PARMS_SPLITTER
															. $parms[2] . AJAX_PARMS_SPLITTER
															//. $parms[0]
													.'\');'
												.'" />'
						.'</div>'
					.'</td>'
					.'<td valign="top">'
						.''
					.'</td>'
					//.'<td valign="top">'
					//	.'<div id="med_'.$parms[0].'_filter_table">'
					//		//.'<img src="'.PLUGIN_LINK.'img/loading.gif" />'
					//		.'{has end date?}'
					//	.'</div>'
					//.'</td>'
					//.'<td valign="top">'
					//	.'&raquo;'
					//.'</td>'
					//.'<td valign="top">'
					//	.'<div id="med_'.$parms[0].'_filter_field">'
					//		//.'<img src="'.PLUGIN_LINK.'img/loading.gif" />'
					//	.'</div>'
					//.'</td>'
				.'</tr>'
				.'</table>'
		;
//echo $html;

		$t = $item_i + 1;

		$inp = array();		$out = array();
		$inp[] = '"';		$out[] = '\"';
		$inp[] = "'";		$out[] = "\'";
		$html = str_replace($inp, $out, $html);

		$js = ''
				.'var filters=document.getElementById(\'_filters_addChildren\');'
				.'var element=document.createElement(\'div\');'
					.'element.setAttribute(\'id\', \'_filters_add_'.$item_i.'\');'
					.'element.innerHTML = \''.$html.'\';'
				.'filters.appendChild(element);'

		//		.'sndReq(\'tables_list\',\'med_'.$parms[0].'_filter_table\',\''
		//
		//			. MAINSITE_DB . AJAX_PARMS_SPLITTER
		//			. '' . AJAX_PARMS_SPLITTER
		//			. $parms[2] . AJAX_PARMS_SPLITTER
		//			. (1) . AJAX_PARMS_SPLITTER
		//			. 'link2fields' . AJAX_PARMS_SPLITTER
		//			. $parms[0] . AJAX_PARMS_SPLITTER
		//			. $filteredTable . AJAX_PARMS_SPLITTER
		//			. $filteredID . AJAX_PARMS_SPLITTER
		//			. $item_i
		//		.'\');'

			.'document.getElementById(\'__total_filters_on_screen_\').value='.$t.';'
			.'sndReq(\'set_unfiltered_fields_selection\',\'not_ref_fields_container\',\''

					. MAINSITE_DB . AJAX_PARMS_SPLITTER
					. $parms[2] . AJAX_PARMS_SPLITTER
					. $parms[0]
			.'\');'
		;
//echo $js;

		echo $splitter_block
			.$js;

		exit();
		break;
		#
	#---------------------------
	case 'set_unfiltered_fields_selection':
	#---------------------------
		#
		#	0: database
		#	1: table
		#	2: field (not mandatory), if passed avoid to include it in the select
		#
		define('EDIT_TABLE_NAME', $parms[1]);
		require(MED_PATH.'inc/table_editor.inc.php');

		$not_filtered_fields = get_not_filtered_fields();

		$html_not_filtered = '<select id="not_ref_fields" name="not_filtered_fields">'
			.'<option value="">' . __( 'Select a new field to filter and click on this button &raquo;', MED_LOCALE ) . '</option>'
		;
		foreach($not_filtered_fields as $field)
		{
			if($field!=$parms[2])
			{
				$html_not_filtered .= '<option value="'.$field.'">'.$field.'</option>';
			}
		}
		$html_not_filtered .= '</select>';

		echo $html_not_filtered;


		//echo $splitter_block
		//	.$js;

		exit();
		break;
		#
	#---------------------------
	case 'update_filters':
	#---------------------------
		#
		#	0: how many fields are filtered
		#	1: the source table
		#
		$js .= 'sndReq(\'update_filters_exec\',\'update_filters_info\',\'\'';

		for($i=0;$i<(int)$parms[0];$i++)
		{
			$js .= ''
					.'+document.getElementById(\'__field_'.$i.'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
//.'+document.getElementById(\'__filteredTable_'.$i.'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
//.'+document.getElementById(\'__filteredID_'.$i.'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
					//.'+getCheckedValue(document.getElementsByName(\'__filteredDesc_'.$i.'\'))+\'' . AJAX_PARMS_SPLITTER . '\''
			;

		}
		$js = substr($js,0,-1)
				.$parms[1].'\''
			.');'
		;


//$js = 'el = document.getElementsByName(\'__filteredDesc_0\');'
//		.'alert(getCheckedValue(el));'
//;

//echo $js;
//exit();

		echo $splitter_block
			.$js;

		exit();
		break;
		#
	#---------------------------
	case 'update_filters_exec':
	#---------------------------
		#
		#	0 ~ {n-1}: the input values
		#	{n}: the source table
		#
		$t = count($parms);
		$records_data = array();

		for($i=0;$i<$t;$i++)
		{
//echo $i.'='.$parms[$i].'<br>';	#debug

			if($i<($t-1))
			{
				$records_data[] = $parms[$i];
			}
			else
			{
				$table = $parms[$i];
			}
		}

//var_dump($records_data);echo ' $table='.$table;
//exit();

		foreach($records_data as $key => $field)
		{
			if($field!='')
			{
				$sql = 'SELECT RRN '

						.'FROM `'.MED_OWN_DB.'`.`'.MED_FIELDS_DEFS.'` '
						.'WHERE `table` = \'' . mysql_real_escape_string($table) . '\' '
						.'AND   `field` = \'' . mysql_real_escape_string($field) . '\' '
				;
//echo $sql.'<hr>';	#debug

				$sth = db_query($sql,__LINE__,__FILE__);
				if($sth[1]==0)
				{
					$sql = 'INSERT INTO `'.MED_OWN_DB.'`.`'.MED_FIELDS_DEFS.'` '

							.'SET '
								.'`table` = \'' . mysql_real_escape_string($table) . '\', '
								.'`field` = \'' . mysql_real_escape_string($field) . '\', '
								.'`isFILTER` = 1 '
					;
				}
				else
				{
					$sql = 'UPDATE `'.MED_OWN_DB.'`.`'.MED_FIELDS_DEFS.'` '

							.'SET '
								.'`isFILTER` = 1 '

							.'WHERE `table` = \'' . mysql_real_escape_string($table) . '\' '
							.'AND   `field` = \'' . mysql_real_escape_string($field) . '\' '
					;
				}
//echo $sql.'<hr>';	#debug

					$sth = db_query($sql,__LINE__,__FILE__);
			}
		}

		echo '<p style="color:green;font-weight:bold;">' . __( 'filters updated successfully!', MED_LOCALE ) . '</p>';

		$js = ''
				//.'el_display_toggler(\'_filters_Data\',\'_filters_TogglerImg\');'
				.'window.location.reload(true);'
		;
		echo $splitter_block
			.$js;

		exit();
		break;
		#
	#---------------------------
	case 'delete_filter':
	#---------------------------
		#
		#	0: {n} of the field to delete
		#	1: the source table
		#
		//echo $output;

		$js = 'sndReq(\'delete_filter_exec\',\'update_filters_info\',\'\''
				.'+document.getElementById(\'__field_'.$parms[0].'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
				//.'+document.getElementById(\'__filteredTable_'.$parms[0].'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
				//.'+document.getElementById(\'__filteredID_'.$parms[0].'\').value+\'' . AJAX_PARMS_SPLITTER . '\''
		;

		$js = substr($js,0,-1)
				.$parms[1].'\''
			.');'
		;

		echo $splitter_block
			.$js;

		exit();
		break;
		#
	#---------------------------
	case 'delete_filter_exec':
	#---------------------------
		#
		#	0: field
		#	1: the source table
		#
		$sql = 'DELETE FROM `'.MED_OWN_DB.'`.`'.MED_FIELDS_DEFS.'` '

				.'WHERE `table` = \'' . mysql_real_escape_string($parms[1]) . '\' '
				.'AND   `field` = \'' . mysql_real_escape_string($parms[0]) . '\' '

				.'LIMIT 1 '
		;
//echo $sql.'<hr>';	#debug
//die();

		$sth = db_query($sql,__LINE__,__FILE__);

		echo '<p style="color:green;font-weight:bold;">' . __( 'filter deleted succesfully!', MED_LOCALE ) . '</p>';

		$js = ''
			//.'var t=document.getElementById(\'__total_filtered_on_screen_\').value-1;'
			//.'sndReq(\'update_filters\',\'update_filters_info\',\'\'+t+\'' . AJAX_PARMS_SPLITTER
			//			. $parms[1]
			//.'\');'
			.'window.location.reload(true);'
		;
		echo $splitter_block
			.$js;

		exit();
		break;
		#
	#---------------------------
	//case '_____________':
	#---------------------------
		#
		#	0:
		#	1:
		#
		//echo $output;

		//echo $splitter_block
		//	.$js;

		//exit();
		//break;
		#
	#---------------------------
	default:
	#---------------------------
		echo '<fieldset style="color:#000000;background:#ffffff;margin:0px;padding:6px;font-family:monospace;font-size:12px;">'
					.'<div align="center">'
						.'<img src="'.PLUGIN_LINK.'img/warning.gif" border="0" alt="WARNING!" /><br />'
						.'Missing AJAX command...<br />'
		;
		$err = '';
		foreach($_INPUT as $key=>$val)
		{
			$err .= $key.'=>'.$val.', ';
		}
		echo substr($err,0,-2)
			.'</div>'
			.'<br /></fieldset>'
		;
}

?>

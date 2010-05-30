<?php
/**
 * Plugin Settings
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.6
 *
 * @todo Add db connection parameters (host, user, pwd)?
 * @todo Let choose to automatically add LAST_UPDATE field where needed?
 */

//var_dump($_POST);


//$sql = 'mysql_get_server_info';
//$sth = db_query($sql, __LINE__, __FILE__);

#
#	0.0.6: BEG
#-------------
$sql = 'SELECT * FROM information_schema.USER_PRIVILEGES WHERE GRANTEE = "\''.DB_USER.'\'@\''.DB_HOST.'\'" ORDER BY TABLE_CATALOG';
$sth = db_query($sql,__LINE__,__FILE__);
$userPrivil = db_fetch($sth[0]);
$u = count($userPrivil);

$sql = 'SELECT * FROM information_schema.SCHEMA_PRIVILEGES WHERE GRANTEE = "\''.DB_USER.'\'@\''.DB_HOST.'\'" ORDER BY TABLE_SCHEMA';
$sth = db_query($sql,__LINE__,__FILE__);
$tablesPrivil = db_fetch($sth[0]);
$t = count($tablesPrivil);

//echo $sql.'<br>';
//var_dump($tablesPrivil);

if(defined('is_DEBUG') && is_DEBUG==true)
{
	echo '<div style="color:#000;background:#F1F1F1;">'

		.'<h3>MySQL Server Info</h3>'
		.'<table cellspacing="6" cellpadding="6">'
		.'<tr>'
			.'<td>Server: <b>'.mysql_get_server_info().'</b></td>'
			.'<td>Client: <b>'.mysql_get_client_info().'</b></td>'
			.'<td>Host: <b>'.mysql_get_host_info().'</b></td>'
			.'<td>Protocol: <b>'.mysql_get_proto_info().'</b></td>'
		.'</tr>'
		.'</table>'
	;
	echo ''
		.'<h3>User Privileges Info ('.DB_USER.'@'.DB_HOST.')</h3>'
		.'<table cellspacing="6" cellpadding="6">'
		.'<th align="left">Catalog</th>'
		.'<th align="left">Privilege Type</th>'
	;

	for($i=0;$i<$u;$i++)
	{
		$tmp = stripslashes($userPrivil[$i]['TABLE_CATALOG']);
		if($tmp=='') { $tmp = 'ALL'; }

		echo '<tr>'
				.'<td align="left">'
					.$tmp
				.'</td>'
				.'<td align="left">'
					.$userPrivil[$i]['PRIVILEGE_TYPE']
				.'</td>'
			.'</tr>'
		;
	}

	echo ''
		.'</table>'
	;
	echo ''
		.'<h3>Tables Privileges Info</h3>'
		.'<table cellspacing="6" cellpadding="6">'
		.'<th align="left">Schema</th>'
		.'<th align="left">Privilege Type</th>'
	;

	for($i=0;$i<$t;$i++)
	{
		echo '<tr>'
				.'<td align="left">'
					.stripslashes($tablesPrivil[$i]['TABLE_SCHEMA'])
				.'</td>'
				.'<td align="left">'
					.$tablesPrivil[$i]['PRIVILEGE_TYPE']
				.'</td>'
			.'</tr>'
		;
	}

	echo ''
		.'</table>'
		.'</div>'
	;
}
#-------------
#	0.0.6: END
#

#
#	validate input when the form is submitted
#
$errors = array();
$e = 0;
if(count($_POST)>0)
{
	if($_POST['med_database']=='')
	{
		$e++;
		$errors[] = __('Database name is mandatory', MED_LOCALE ) . '<br />';
	}

	if($_POST['med_own_database']=='')
	{
		$e++;
		$errors[] = __('I need to know where to save my data', MED_LOCALE ) . '<br />';
	}

	if($_POST['med_webmaster']=='')
	{
		$e++;
		$errors[] = __('I need to know where to send the email if an error occours on the MySQL database', MED_LOCALE ) . '<br />';
	}

	if($_POST['med_dates_locale']=='')
	{
		$e++;
		$errors[] = __('I need to know how do you like to see dates/times', MED_LOCALE ) . '<br />';
	}
}

if(count($errors)>0)
{
	#	there is at least one error...
	#
	?><div class="error"><?php

		foreach($errors as $err)
		{
			echo '<p><strong>' . $err . '</strong></p>';
		}

	?></div><?php
}
else
{
	#	no errors, lets proceed pending on the button the user clicked
	#
	switch($_POST['btn'])
	{
		#----------------
		case CREATE_BTN:
		#----------------
			#
			#	create the (empty) myEASYdb own tables
			#
			$MED_OWN_TABLES = explode('|', MED_OWN_TABLES);
			$errors = array();

			$result = db_select_db(array($_POST['med_own_database']));
			if($result==1)
			{
				?><div class="updated"><?php

					foreach($MED_OWN_TABLES as $table)
					{
						if(file_exists(MED_PATH.'inc/sql/'.$table.'-create.sql'))
						{
							if(!table_exists($table, $_POST['med_own_database']))		#	0.0.6
							{
								echo '<p>' . __('Adding table: ', MED_LOCALE ) . '<strong>' . $table . '</strong></p>';

								$sql = file_get_contents(MED_PATH.'inc/sql/'.$table.'-create.sql');
								$sth = db_query($sql,__LINE__,__FILE__);

								echo '<code>'.$sql.'</code><br />';						#	0.0.6
							}
						}
						else
						{
							$errors[] = $table;
						}
					}

				?></div><?php
			}

			if(count($errors)>0)
			{
				?><div class="error"><?php

					foreach($errors as $err)
					{
						echo '<p>' . __('Missing CREATE code for table: ', MED_LOCALE ) . '<strong>' . $err . '</strong></p>';
					}

				?></div><?php
			}
			break;
			#
		#----------------
		case SAVE_BTN:
		#----------------
			#
			#	save the posted value in the database
			#
			update_option( 'med_database',		$_POST['med_database'] );
			update_option( 'med_own_database',	$_POST['med_own_database'] );
			update_option( 'med_webmaster',		$_POST['med_webmaster'] );
			if(isset($_POST['med_isPRODUCTION']))
			{
				update_option( 'med_isPRODUCTION', 1 );
			}
			else
			{
				update_option( 'med_isPRODUCTION', 0 );
			}

			update_option( 'med_dates_locale',	$_POST['med_dates_locale'] );

			update_option( 'med_items_per_page',			$_POST['med_items_per_page'] );
			update_option( 'med_tbl_list_cellspacing',		$_POST['med_tbl_list_cellspacing'] );
			update_option( 'med_tbl_list_tr_highlight_on',	$_POST['med_tbl_list_tr_highlight_on'] );
			update_option( 'med_tbl_list_tr_highlight_off',	$_POST['med_tbl_list_tr_highlight_off'] );
			update_option( 'med_tbl_list_td_style',			$_POST['med_tbl_list_td_style'] );
			update_option( 'med_tbl_list_max_length',		$_POST['med_tbl_list_max_length'] );

			if(isset($_POST['med_isDEBUG']))					#	0.0.6
			{
				update_option( 'med_isDEBUG', 1 );
			}
			else
			{
				update_option( 'med_isDEBUG', 0 );
			}

			?>
				<div class="updated">
					<p><strong><?php _e('Options saved!', MED_LOCALE ); ?></strong></p>
					<p><?php _e('You will be redirected to the administration page in a while...', MED_LOCALE ); ?></p>
				</div>
				<script type="text/javascript">setTimeout("window.location='?page=med_admin';", 1500);</script>
			<?php
			break;
			#
		default:
	}
}

$databases = table_get_databases();
$t = count($databases);

//var_dump($databases);

/*
if(!isset($_POST['med_host']))		{ $_POST['med_host'] = DB_HOST; }
if(!isset($_POST['med_user']))		{ $_POST['med_user'] = DB_USER; }
if(!isset($_POST['med_password']))	{ $_POST['med_password'] = DB_PASSWORD; }
<!--
<tr>
	<td nowrap><?php _e('Host:', MED_LOCALE ); ?></td>
	<td>
		<input type="text" name="med_host" value="<?php echo $_POST['med_host']; ?>" size="15" maxlength="128" />
	</td>
</tr>
<tr>
	<td nowrap><?php _e('User:', MED_LOCALE ); ?></td>
	<td>
		<input type="text" name="med_user" value="<?php echo $_POST['med_user']; ?>" size="15" maxlength="128" />
	</td>
</tr>
<tr>
	<td nowrap><?php _e('Password:', MED_LOCALE ); ?></td>
	<td>
		<input type="password" name="med_password" value="<?php echo $_POST['med_password']; ?>" size="15" maxlength="128" />
	</td>
</tr>
-->
*/

#
#	populate the input fields when the page is loaded
#
if(!isset($_POST['med_database']))		{ $_POST['med_database']		= get_option('med_database'); }
if(!isset($_POST['med_own_database']))	{ $_POST['med_own_database']	= get_option('med_own_database'); }
if(!isset($_POST['med_webmaster']))		{ $_POST['med_webmaster']		= get_option('med_webmaster'); }
if(!isset($_POST['med_isPRODUCTION']))	{ $_POST['med_isPRODUCTION']	= get_option('med_isPRODUCTION'); }
if(!isset($_POST['med_isDEBUG']))		{ $_POST['med_isDEBUG']			= get_option('med_isDEBUG'); }			#	0.0.6

if(!isset($_POST['med_tbl_list_cellspacing'])) {

	$tmp = get_option('med_tbl_list_cellspacing');
	if($tmp=='') { $tmp = 2; }
	$_POST['med_tbl_list_cellspacing'] = $tmp;
}

if(!isset($_POST['med_tbl_list_tr_highlight_on'])) {

	$tmp = get_option('med_tbl_list_tr_highlight_on');
	if($tmp=='') { $tmp = 'background:#eee;'; }
	$_POST['med_tbl_list_tr_highlight_on'] = $tmp;
}

if(!isset($_POST['med_tbl_list_tr_highlight_off'])) {

	$tmp = get_option('med_tbl_list_tr_highlight_off');
	if($tmp=='') { $tmp = 'background:#fff;'; }
	$_POST['med_tbl_list_tr_highlight_off'] = $tmp;
}

if(!isset($_POST['med_tbl_list_td_style'])) {

	$tmp = get_option('med_tbl_list_td_style');
	if($tmp=='') { $tmp = 'padding:6px;'; }
	$_POST['med_tbl_list_td_style'] = $tmp;
}

if(!isset($_POST['med_tbl_list_max_length'])) {

	$tmp = get_option('med_tbl_list_max_length');
	if((int)$tmp==0) { $tmp = 30; }
	$_POST['med_tbl_list_max_length'] = $tmp;
}

if(!isset($_POST['med_items_per_page'])) {

	$tmp = get_option('med_items_per_page');
	if((int)$tmp==0) { $tmp = 20; }
	$_POST['med_items_per_page'] = $tmp;
}

if(!isset($_POST['med_items_per_page'])) {

	$tmp = get_option('med_items_per_page');
	if((int)$tmp==0) { $tmp = 20; }
	$_POST['med_items_per_page'] = $tmp;
}

if(!isset($_POST['med_dates_locale'])) {

	$tmp = get_option('med_dates_locale');
	if($tmp=='') { $tmp = 'a'; }
	$_POST['med_dates_locale'] = $tmp;
}


?>
<form name="med_settings" method="post" action="">

<table width="100%" cellpadding="4" cellspacing="8">
<tr>
	<td nowrap><?php
		#
		#	@since 0.0.6
		#
		_e('Check this to show', MED_LOCALE );
		echo '<br />';
		_e('debug code:', MED_LOCALE );

	?></td>
	<td valign="bottom"><?php

			$checked ='';
			if($_POST['med_isDEBUG']==1) { $checked = ' checked="checked"'; }

		?><input type="checkbox" name="med_isDEBUG" value="1"<?php echo $checked; ?> />
	</td>
</tr>
<tr>
	<td width="1%" nowrap valign="top"><?php _e('Database:', MED_LOCALE ); ?></td>
	<td>
		<select name="med_database" onchange="javascript:sndReq('tables_list','med_database_tables',this.value);">

			<option value=""><?php _e('This is the database that contains your data...', MED_LOCALE ); ?></option><?php

			for($i=0;$i<$t;$i++)
			{
				$selected = '';
				if($databases[$i]['Database']==$_POST['med_database'])
				{
					$selected = ' selected="selected"';
				}
				if($databases[$i]['Database']!='information_schema' && $databases[$i]['Database']!='mysql')
				{
					echo '<option value="'.$databases[$i]['Database'].'"'.$selected.'>'
							.$databases[$i]['Database']
						.'</option>'
					;
				}
			}

	?></select>
		<div id="med_database_tables" style="font-size:9px;"><img src="<?php echo PLUGIN_LINK; ?>img/loading.gif" /></div>
	</td>
</tr>
<tr>
	<td nowrap valign="top"><?php _e('myEasyDB Database:', MED_LOCALE ); ?></td>
	<td>
		<select name="med_own_database" onchange="javascript:sndReq('tables_list','med_own_database_tables',this.value+'<?=AJAX_PARMS_SPLITTER?>check_med_tables');">
			<option value=""><?php _e('This is the database where to save myEASYdb own data...', MED_LOCALE ); ?></option><?php

			for($i=0;$i<$t;$i++)
			{
				$selected = '';
				if($databases[$i]['Database']==$_POST['med_own_database'])
				{
					$selected = ' selected="selected"';
				}
				if($databases[$i]['Database']!='information_schema' && $databases[$i]['Database']!='mysql')
				{
					echo '<option value="'.$databases[$i]['Database'].'"'.$selected.'>'
							.$databases[$i]['Database']
						.'</option>'
					;
				}
			}

	?></select>
		<div id="med_own_database_tables" style="font-size:9px;"><img src="<?php echo PLUGIN_LINK; ?>img/loading.gif" /></div>
	</td>
</tr>
<tr>
	<td nowrap><?php _e('Webmaster email:', MED_LOCALE ); ?></td>
	<td>
		<input type="text" size="40" maxlength="128" name="med_webmaster" value="<?php echo $_POST['med_webmaster']; ?>" />
	</td>
</tr>
<tr>
	<td nowrap><?php

		_e('Check if this is a', MED_LOCALE );
		echo '<br />';								#	0.0.5
		_e('production server:', MED_LOCALE );		#	0.0.5

	?></td>
	<td valign="bottom"><?php

			$checked ='';
			if($_POST['med_isPRODUCTION']==1) { $checked = ' checked="checked"'; }

		?><input type="checkbox" name="med_isPRODUCTION" value="1"<?php echo $checked; ?> />
	</td>
</tr>
<tr>
	<td nowrap valign="top"><?php _e('How to represent dates:', MED_LOCALE ); ?></td>
	<td>
		<select name="med_dates_locale">
			<option value=""><?php _e('Select the format you like better...', MED_LOCALE ); ?></option><?php

			foreach($med_dates_locale_ary as $key => $val)
			{
				$selected = '';
				if($_POST['med_dates_locale']==$key)
				{
					$selected = ' selected="selected"';
				}

				echo '<option value="'.$key.'"'.$selected.'>'
						.$val
					.'</option>'
				;
			}

	?></select>
	</td>
</tr>
<tr>
	<td colspan="99">
		<fieldset style="margin-top:8px;">
			<legend>&nbsp;<?php _e('Table contents list options', MED_LOCALE ); ?>&nbsp;</legend>
			<table>
				<tr>
					<td nowrap><?php _e('Records per page:', MED_LOCALE ); ?></td>
					<td>
						<select name="med_items_per_page">
							<option value=""><?php _e('How many records you like to see in each page?', MED_LOCALE ); ?></option><?php

							for($i=10;$i<110;$i=$i+10)
							{
								$selected = '';
								if($i==$_POST['med_items_per_page'])
								{
									$selected = ' selected="selected"';
								}
								echo '<option value="'.$i.'"'.$selected.'>'
										.$i
									.'</option>'
								;
							}

						?></select>
					</td>
				</tr>
				<tr>
					<td nowrap><?php _e('Cell spacing:', MED_LOCALE ); ?></td>
					<td>
						<input type="text" size="2" maxlength="4" name="med_tbl_list_cellspacing" value="<?php echo $_POST['med_tbl_list_cellspacing']; ?>" />
					</td>
				</tr>
				<tr>
					<td nowrap><?php _e('Row (&lt;tr&gt;) highlighted:', MED_LOCALE ); ?></td>
					<td>
						<input type="text" size="30" maxlength="128" name="med_tbl_list_tr_highlight_on" value="<?php echo $_POST['med_tbl_list_tr_highlight_on']; ?>" />
					</td>
				</tr>
				<tr>
					<td nowrap><?php _e('Row (&lt;tr&gt;) normal:', MED_LOCALE ); ?></td>
					<td>
						<input type="text" size="30" maxlength="128" name="med_tbl_list_tr_highlight_off" value="<?php echo $_POST['med_tbl_list_tr_highlight_off']; ?>" />
					</td>
				</tr>
				<tr>
					<td nowrap><?php _e('Cell (&lt;td&gt;) style:', MED_LOCALE ); ?></td>
					<td>
						<input type="text" size="30" maxlength="128" name="med_tbl_list_td_style" value="<?php echo $_POST['med_tbl_list_td_style']; ?>" />
					</td>
				</tr>
				<tr>
					<td nowrap><?php _e('Max length shown within data cells (&lt;td&gt;):', MED_LOCALE ); ?></td>
					<td>
						<input type="text" size="30" maxlength="128" name="med_tbl_list_max_length" value="<?php echo $_POST['med_tbl_list_max_length']; ?>" />
					</td>
				</tr>
			</table>
		</fieldset>
	</td>
</tr>
</table>

<hr class="line" />
<div align="right">
	<input class="button-primary" type="submit" name="btn" value="<?php echo SAVE_BTN; ?>" />
</div>

</form>

<script type="text/javascript">
	sndReq('tables_list','med_database_tables','<?php echo $_POST['med_database']; ?>');
	sndReq('tables_list','med_own_database_tables','<?php echo $_POST['med_own_database'].AJAX_PARMS_SPLITTER.'check_med_tables'; ?>');
</script>

<?php
/**
 * MySQL connector
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.1
 */

/***************************************************************************
 *	MySQL.inc.php
 *
 *	begin		: June 2004
 *	version		: 28 January 2010
 *	copyright	: (C) 2004,2010 grandolini.net
 *
 *	MySQL database connector
 *
 ***************************************************************************/

if(is_callable(db_connect)) { return; }


if(!function_exists('db_connect')) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Connect to the database
	#
	function db_connect($args=array(),$line='',$file='') {		# mod 22/02/2008

		$exec=@mysql_connect($args[0],$args[1],$args[2]);


//	28/11/2009
//die('['.$exec.']');
//if(strlen($exec)==0)
//{
//	$tmp = BuildMySqlString('<div align="center">'.$dbh.'</div><br />mysql_connect<br /><br />File: '.$file.'<br />Line: '.$line.'<br />');
//	handle_critical_errors($tmp,__FILE__,__LINE__);
//	header('Location: /unavailable.html');
//	die();
//}



#		if(!$exec) { die(db_err()); } else { return($exec); }	# del 22/02/2008
		if($exec) { return($exec); } 							# ins 22/02/2008
		$tmp = BuildMySqlString('<div align="center">'.$dbh.'</div><br />'
								.'mysql_connect<br /><br />'
								.'File: '.$file.'<br />'
								.'Line: '.$line.'<br />'
								.'Host: '.$args[0].'<br />'
								.'User: '.$args[1].'<br />'
								.'Password: '.$args[2].'<br />'
		);														# ins 22/02/2008
		handle_critical_errors($tmp,__FILE__,__LINE__);			# mod 26/09/2008
		return false;											# ins 22/02/2008
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Selects the database to use
	#
	function db_select_db($args=array(),$line='',$file='') {		# mod 22/02/2008
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

#echo $file.', '.$line.'<br>';
		if(isset($args[1]))
		{
#			$exec=@mysql_select_db($args[0], $args[1]); if(!$exec) { die(db_err()); } else { return($exec); }	# del 22/02/2008
			$exec=@mysql_select_db($args[0], $args[1]);															# ins 22/02/2008
		}
		else
		{
			$exec=@mysql_select_db($args[0]);
		}
#		$exec=@mysql_select_db($args[0]); if(!$exec) { die(db_err()); } else { return($exec); }	# del 22/02/2008
		if($exec) { return($exec); }															# ins 22/02/2008
		#
		$tmp = BuildMySqlString('<div align="center">'.$dbh.'</div><br />mysql_select_db<br /><br />File: '.$file.'<br />Line: '.$line.'<br />'); # ins 22/02/2008
		handle_critical_errors($tmp,__FILE__,__LINE__);	# mod 26/09/2008
		return false;									# ins 22/02/2008
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Executes a query
	#
	function db_query($query, $line='', $file='', $flag='') {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		global $dbh;
//die(__FUNCTION__.':'.$dbh);

		$_SESSION['mysql_totqry']++;				# ins 30/09/2008

		$exec[0] = @mysql_query($query, $dbh);	# resource ID of the query
		if(!$exec or $exec[0]=='' && $flag=='')
		{
//			echo(BuildMySqlString('<div align="center">'.$dbh.'</div><br />'.$query.'<br /><br />File: '.$file.'<br />Line: '.$line.'<br />'));
			$tmp = BuildMySqlString('<div align="center">'.$dbh.'</div><br />'.htmlspecialchars($query).'<br /><br />File: '.$file.'<br />Line: '.$line.'<br />');
			handle_critical_errors($tmp,__FILE__,__LINE__);	# mod 26/09/2008
			exit;
		}
		else
		{
			if(substr($exec[0],0,8)=='Resource')
			{
				#	if the query includes a LIMIT parameter, $exec[0] is set to that limit
				#	otherwise to the Resource ID that can be used to get the number of records found
				#
				$exec[1]=@mysql_num_rows($exec[0]);	# number of records found
			}
			return($exec);
		}
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Fetches records -- set $debug to print rows
	#	Output:	array of records
	#
//	function db_fetch($query_id, $debug=false)				#	17/01/2009
	function db_fetch($parms, $debug=false) {				#	17/01/2009
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$rows=array(); $i=0; $ii=0;
		$query_id = $parms;									#	17/01/2009
		if(is_array($parms))
		{
			#
			#	0 = resource ID
			#	1 = recs found
			#	2 = query (SELECT only)
			#	3 = cache ID
			#
			$query_id = $parms[0];
			$t_rows = $parms[1];
		}
		#
		#	Get record by record
		#
		while($row=@mysql_fetch_array($query_id,MYSQL_ASSOC))
		{
			#	Build the output array
			#
			array_push($rows,$row);
			if($debug)
			{
				#	Load array data
				#
				unset($tmp_array);
				$tmp_array=$rows[$i];
				$fields_record=count($tmp_array);	# number of fields per record
				reset($tmp_array);					# set to the first array field
				while($tmp_next=each($tmp_array)) { $labels[$ii]=$tmp_next['key']; $table_field[$ii]=$tmp_next['value']; $ii++; }
				$i++;
			}
		}
		if($debug)
		{
			?><br /><?php if(!isset($labels)) { $labels[0]='No records in this table!'; $table_field[0]=$labels[0]; $fields_record=0; }
			ShowTable($labels,$table_field,$fields_record,'80%','center',true,'A');
		}
		return($rows);
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Returns error number
	#
	function db_err() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		return('<fieldset style="color:#000000;background:#ffffff;margin:0px;padding:6px;font-family:monospace;font-size:12px;">'
					.'<div align="center">'
						.'<img src="'.PLUGIN_LINK.'img/warning.gif" border="0" alt="WARNING!" /><br />'
						.'MySQL Error ('.@mysql_errno().'): '.@mysql_error().'</div><br />'
						.'<a href="http://'.$_SERVER[HTTP_HOST].'">Click here</a> to return to the main page<br /><br />File: '.$file.'<br />Line: '.$line
				.'<br /></fieldset>'); # mod 28/01/2010
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Prepare error string
	#
	function BuildMySqlString($temp) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		return('<fieldset style="color:#000000;background:#ffffff;margin:0px;padding:6px;font-family:monospace;font-size:12px;">'
					.'<div align="center">'
						.'<img src="'.PLUGIN_LINK.'img/warning.gif" border="0" alt="WARNING!" /><br />'
							.'MySQL Error ('.@mysql_errno().')<br /><u>'.@mysql_error().'</u></div>'.$temp
				.'</fieldset>'); # mod 28/01/2010
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Checks if a table exists in a database
	#
	function table_exists($table, $db) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$sql = 'SHOW TABLE STATUS FROM '.$db;
		$sth = db_query($sql,__LINE__,__FILE__);
		$rows = db_fetch($sth[0]);
//var_dump($rows);

		$i = 0;
		$isTABLE = false;
		while($i<$sth[1] && $isTABLE==false)
		{
			if($rows[$i]['Name']==$table)
			{
				$isTABLE = true;
			}
			$i++;//echo $rows[$i]['Name'].'<br>';
		}
		return $isTABLE;
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Checks if a field exists in a database table
	#
	function table_field_exists($field, $table, $db) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$sql = 'SHOW COLUMNS FROM '.$db.'.'.$table;
		$sth = db_query($sql,__LINE__,__FILE__);
		$rows = db_fetch($sth[0]);
//var_dump($rows);

		$i = 0;
		$isFIELD = false;
		while($i<$sth[1] && $isFIELD==false)
		{
			if($rows[$i]['Field']==$field)
			{
				$isFIELD = true;
			}
			$i++;//echo $rows[$i]['Field'].'<br>';
		}
		return $isFIELD;
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Returns an array with the definitions of all fields of a table (18 January 2010)
	#
	function table_get_fields($db, $table) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		//$sql = 'SHOW CREATE TABLE `'.$db.'`.`'.$table.'` ';
		//$sql = 'SHOW TABLE STATUS FROM `'.$db.'` ';#LIKE `'.$table.'` ';
		$sql = 'SHOW FULL COLUMNS FROM `'.$db.'`.`'.$table.'` ';
		$sth = db_query($sql,__LINE__,__FILE__);
		$rows = db_fetch($sth[0]);

/*
var_dump($rows);

[3]=>
array(9) {
	["Field"]=>		string(4) "nome"
	["Type"]=>		string(11) "varchar(64)"
	["Collation"]=>	string(15) "utf8_unicode_ci"
	["Null"]=>		string(3) "YES"
	["Key"]=>		string(0) ""
	["Default"]=>	NULL

	["Extra"]=>		string(0) ""	||		["Extra"]=>		string(14) "auto_increment"

	["Privileges"]=>string(31) "select,insert,update,references"
	["Comment"]=>	string(14) "nome del corso"
}
*/
		return $rows;
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Add a field to a table (22 January 2010)
	#
	function table_field_add($field, $type, $after, $table, $db) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$sql = 'ALTER TABLE `'.$db.'`.`'.$table.'` ADD COLUMN `'.$field.'` '.$type.' AFTER `'.$after.'`;';
		$sth = db_query($sql,__LINE__,__FILE__);
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Returns an array with the definitions of all fields of a table (20 January 2010)
	#
	function table_get_table_info($db, $table) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$sql = 'SHOW CREATE TABLE `'.$db.'`.`'.$table.'` ';
		$sth = db_query($sql,__LINE__,__FILE__);
		$rows = db_fetch($sth[0]);

		$create = trim($rows[0]['Create Table']);
		$create = str_replace("\n", '', $create);

//echo '['.$create.']<br>';

		$e = strpos($create, 'ENGINE', 0);

//echo 'ENGINE:'.$e.'<br>';

		$p = strpos($create, 'COMMENT=', $e);

//echo 'COMMENT:'.$p.'<br>';

		if($p!==false)
		{
			$beg = $p;
//echo 'BEG:'.$beg.'<br>';

			$end = strlen($create)-$beg-9-1;

//echo 'len:'.strlen($create).'<br>';
//echo 'END:'.$end.'<br>';

		}
		$comment = substr($create, ($beg+9), $end);

//echo '['.$comment.']<br>';

		$rows[0]['Comment'] = $comment;

		return $rows;
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Returns an array with the definitions of all the tables in a database (20 January 2010)
	#
	function table_get_tables($db) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$sql = 'SHOW FULL TABLES FROM `'.$db.'` ';
		$sth = db_query($sql,__LINE__,__FILE__);
		$rows = db_fetch($sth[0]);

/*
var_dump($rows);

[0]=>
	array(2) {
		["Tables_in_dev_thinkdog"]=>	string(5) "citta"
		["Table_type"]=>				string(10) "BASE TABLE"
	}
}
*/

		for($i=0;$i<$sth[1];$i++)
		{
			$table = $rows[$i]['Tables_in_'.$db];

//echo $table.'<br>';

			$table_fields = table_get_table_info($db, $table);

//var_dump($table_fields);echo '<hr>';
//var_dump($table_fields[0]['Comment']);echo '<hr>';

			$rows[$i]['Name']    = $rows[$i]['Tables_in_'.$db];
			$rows[$i]['Comment'] = $table_fields[0]['Comment'];
		}
//var_dump($rows);echo '<hr>';

		return $rows;
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Returns an array with all the available databases (26 January 2010)
	#
	function table_get_databases() {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$sql = 'SHOW DATABASES';
		$sth = db_query($sql,__LINE__,__FILE__);
		$rows = db_fetch($sth[0]);

/*
//var_dump($rows);

[0]=> array(1) {
	["Database"]=> string(18) "information_schema"
}
*/
		return $rows;
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function db_close($dbh)	{ return @mysql_close($dbh); }							#	Close a database server connection
	function db_last_id($dbh) { return @mysql_insert_id($dbh); }					#	Return the last generated RRN
	function db_affected($dbh) { return @mysql_affected_rows($dbh); }				#	Return the number of affected rows
	function db_free_result($query_id) { return(@mysql_free_result($query_id)); }	#	Destroy a query result to free memory
}
?>
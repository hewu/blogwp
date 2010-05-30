<?php
/**
 * Language handler for tables records data
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.1
 * @todo adaptation for myEASYdb
 */

if(!function_exists('get_site_languages')) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function set_lang_flag($lang, $flag)
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	{
		if(table_exists('site_languages', MAINSITE_DB)) { $db_languages = MAINSITE_DB; } else { $db_languages = CAMALEO_DB; } # set the database to use
		#
		$sql = 'UPDATE `'.$db_languages.'`.`site_languages` '

					.'SET `site_languages`.`switch` = '.mysql_real_escape_string($flag).' '

				.'WHERE lang=\''.mysql_real_escape_string($lang).'\' '
		;
		$sth = db_query($sql,__LINE__,__FILE__);
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function get_site_languages($lang='', $active='')
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	{
		if(table_exists('site_languages', MAINSITE_DB)) { $db_languages = MAINSITE_DB; } else { $db_languages = CAMALEO_DB; } # set the database to use
		#
		$sql = 'SELECT '

					.'`site_languages`.`lang`, '
					.'`site_languages`.`descrizione`, '
					.'LCASE(`site_languages`.`flag`) AS flag, '
					.'LCASE(`site_languages`.`iso`)  AS iso, '
					.'`site_languages`.`switch` '

				.'FROM `'.$db_languages.'`.`site_languages` '

				.'WHERE 1=1 '
		;
		if($lang!='')
		{
			$sql .= 'AND lang = \''.mysql_real_escape_string($lang).'\' ';
		}
		if($active!='')
		{
			$sql .= 'AND switch = 1 ';
		}
		$sql .= 'ORDER BY switch DESC, descrizione ASC ';
		#
		$sth = db_query($sql,__LINE__,__FILE__);
		$rows = db_fetch($sth[0],false);

//echo $sql.'<br>';
//var_dump($rows);

		return $rows;
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function draw_language_flag($lang)
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	{
		$rows = get_site_languages($lang);
		echo '<img src="'.SKIN_PATH.'img/flags/'.$rows[0]['flag'].'.png" width="16" height="10" border="0" style="margin-right:4px;" align="absmiddle" />'
					.$rows[0]['descrizione']
		;
		return;
	}
}
?>

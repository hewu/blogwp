<?php
/**
 * Error handler
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.1
 */

if(!function_exists('handle_critical_errors')) {
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	#	Forward the errors to the proper destination
	#
	function handle_critical_errors($message, $file, $line, $die='', $mailit='', $subj='', $cc='')
	#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	{
		$to = MED_WEBMASTER;
		#
		if($subj=='') { $subj = 'myEASYdb WARNING: CRITICAL ERROR'; }

		$subject = $subj.' (@'.$_SERVER['HTTP_HOST'].')';

		$message .= '<hr />'
					.'Date: '.date('d-m-Y H:i:s').'<br />'
					.'Host: '.$_SERVER['HTTP_HOST'].'<br />'
					.'File: '.$file.'<br />'
					.'Line: '.$line.'<br />'
					.'Referer: '.$_SERVER['HTTP_REFERER'].'<br />'
					.'Request URI: '.$_SERVER['REQUEST_URI'].'<br />'
					.'Request IP: '.$_SERVER['REMOTE_ADDR'].'<br />'
					.'Sent to: '.$to.'<br />'
					.'Sent cc: '.$cc
					.'<hr />'
		;
//die('message:'.$message);

		#
		#	tries to log the message (just in case the email does not go through)
		#
		$sql = 'SELECT RRN FROM `'.MED_OWN_DB.'`.`'.MED_TABLE_ERRORS_LOG.'` LIMIT 1';
		$sth = @mysql_query($sql);
		if($sth)
		{
			#	the query succeded
			#
			$sql = 'INSERT INTO `'.MED_OWN_DB.'`.`'.MED_TABLE_ERRORS_LOG.'` '
					.'SET '
						.'tomail	= \''.mysql_real_escape_string($to).'\', '
						.'subject	= \''.mysql_real_escape_string($subject).'\', '
						.'message	= \''.mysql_real_escape_string($message).'\' '
			;
			$sth = @mysql_query($sql);
		}

		if(!defined('is_PRODSERVER')) { define('is_PRODSERVER', true); }
		if(is_PRODSERVER==false)
		{
			if($die=='') { die($message); } else { echo $message; }
		}

		if(is_PRODSERVER==true || $mailit!='')
		{
			#	production || flag on: send the error by email to the webmaster
			#
			if(!function_exists('emailer'))
			{
				require(MED_PATH.'inc/emailer.inc.php');
			}
			$result = emailer($to, $subject, $message, $reply, $cc, $bcc, $from, 'html', '1');

			if($die=='')
			{
				_e( 'Sorry an error occourred while working on a MySQL table, the administator was already informed.', MED_LOCALE );
				die('');
			}
		}
		else
		{
			if($die=='') { die($message); } else { echo $message; }
		}
	}
}

?>
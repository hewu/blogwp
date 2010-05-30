<?php
/**
 * Email handler
 *
 * @package myEASYdb
 * @author Ugo Grandolini
 * @version 0.0.6
 */

if(!function_exists('emailer')) {

	function emailer(	$to,
						$subject,
						$body,
						$reply		= '',
						$cc			= '',
						$bcc		= '',
						$from		= '',
						$text_type	= 'html', 			#	20/05/2008
						$x_prio		= '3',
						$attach_path= '',
						$attach_file= ''
	) {
		global $USER_PRIV, $_CHARSET, $dbh;
		#
		if(strlen($_CHARSET)==0)		{ $_CHARSET= 'utf-8'; }						#	12/08/2009

//		if($_SESSION['misc']['MAILSRV']=='none') { die('(emailer.inc) MailServer is set to NONE!'); }
		if(	!isset($_SESSION['misc']['MAILSRV'])
			|| $_SESSION['misc']['MAILSRV']=='none'
			|| strlen($_SESSION['misc']['MAILSRV'])==0
		)																			#	11/08/2009
		{
			$_SESSION['misc']['MAILSRV'] = 'ini';
		}
		if($_SESSION['misc']['MAILSRV']!='ini')										#	11/08/2009
		{
			if(!checkdnsrr($_SESSION['misc']['MAILSRV'],'MX'))
			{
				$_SESSION['misc']['MAILSRV'] = 'ini';
			}
		}
		#
		define(_CR_,"\r\n");
		define(_TAB_,"\t");
		#
		$user_body = $body;															#	12/08/2009
		#
		#	Initializations
		#
		if($reply=='')				{ $reply = $_SESSION['misc']['E_NOREPLY']; }
		if($from=='')				{ $from  = 'robot#robot@'.$_SERVER['HTTP_HOST']; }
		list($user_id, $from_id)	= explode('#', $from);

		if($reply=='')				{ $reply   ='noreply@thank.you'; }				#	11/08/2009
		if($user_id=='')			{ $user_id ='robot'; }							#	11/08/2009
		if($from_id=='')			{ $from_id ='robot@'.$_SERVER['HTTP_HOST']; }	#	11/08/2009

		$domain = $_SERVER['SERVER_NAME'];
		$mime_boundary = '------------{'.md5(uniqid(time())).'}';
		#
		#	Set the common header
		#
		$headers = 'MIME-Version: 1.0'._CR_;
		#
		#	$headers .= 'Return-Receipt-To: '.$_SESSION['misc']['E_ADMIN'].'<'.$_SESSION['misc']['E_ADMIN'].'>'._CR_;
		#
//		if($reply)	{ $headers .= 'Reply-To:'.$reply._CR_; }
		$headers .= 'Reply-To:'.$reply._CR_;										#	11/08/2009
		#
		if(is_array($cc))
		{
			#	Send copy to
			#
			$t = count($cc);
			$headers .= 'Cc:';
			for($i=0;$i<$t;$i++) { $headers .= $cc[$i].', '; }
			$headers = substr($headers,0,-2)._CR_;
		}
		else { if($cc) { $headers .= 'Cc:'.$cc._CR_; } }
		#
		if(is_array($bcc))
		{
			#	Send blind copy to
			#
			$t = count($bcc);
			$headers .= 'Bcc:';
			for($i=0;$i<$t;$i++) { $headers .= $bcc[$i].', '; }
			$headers = substr($headers,0,-2)._CR_;
		}
		else { if($bcc) { $headers .= 'Bcc:'.$bcc._CR_; } }
		#
		$headers .= 'User-Agent: '.$_SERVER['HTTP_USER_AGENT']._CR_;
		$headers .= 'From: '.$user_id.' <'.$from_id.'>'._CR_;

//		$headers .= 'Message-ID: <'.md5(uniqid(time())).'@{'.$domain.'}>'._CR_;
		$headers .= 'Message-ID: <'.md5(uniqid(time())).'@'.$domain.'>'._CR_;		#	11/08/2009
		#
		switch($x_prio)
		{
			case '1':	$x_prio .= ' (Highest)';	break;
			case '2':	$x_prio .= ' (High)';		break;
			case '3':	$x_prio .= ' (Normal)';		break;
			case '4':	$x_prio .= ' (Low)';		break;
			case '5':	$x_prio .= ' (Lowest)';		break;
			#
			default:
				$x_prio = '3 (Normal)';												#	12/08/2009
		}
		if($x_prio)	{ $headers .= 'X-Priority: '.$x_prio._CR_; }
		#
		#	Message Priority for Exchange Servers
		#
		#	$headers .=	'X-MSmail-Priority: '.$x_prio_des._CR_;
		#
		#	!!! WARNING !!!---# Hotmail and others do NOT like PHP mailer...
		#	$headers .=	'X-Mailer: PHP/'.phpversion()._CR_;---#
		#
		#	$headers .= 'X-Mailer: Microsoft Office Outlook, Build 11.0.6353'._CR_;
		#	$headers .= 'X-MimeOLE: Produced By Microsoft MimeOLE V6.00.2900.2527'._CR_;
		#
//		$headers .= 'X-Sender: '.$_SESSION['misc']['E_ROBOT']._CR_;
		$headers .= 'X-Sender: '.$user_id.' <'.$from_id.'>'._CR_;					#	12/08/2009

		$headers .= 'X-AntiAbuse: This is a solicited email for - '.$to.' - '._CR_;
		$headers .= 'X-AntiAbuse: Servername - {'.$domain.'}'._CR_;

//		$headers .= 'X-AntiAbuse: User - '.$_SESSION['misc']['E_ROBOT']._CR_;
		$headers .= 'X-AntiAbuse: User - '.$from_id._CR_;							#	12/08/2009
		#
		#	Set the right start of header
		#
//		if($attach_file)						#	25/11/2009
		if($attach_path && $attach_file)		#	25/11/2009
		{
			include(SITE_ROOT.'common/fun2inc/mimetype.inc');
#
# INS 25/11/2009 : BEG
#---------------------
			if(!is_array($attach_path) || !is_array($attach_file))
			{
				$_attach_path = array();
				$_attach_file = array();

				$_attach_path[] = $attach_path;
				$_attach_file[] = $attach_file;
			}
			else
			{
				$_attach_path = $attach_path;
				$_attach_file = $attach_file;
			}
			#
			$a = 0;
			foreach($_attach_file as $key=>$attach_file)
			{
				$attach_path = $_attach_path[$key];
#---------------------
# INS 25/11/2009 : END
#
				$file_name_type = mimetype($attach_path, $attach_file);
				$file_name_name = $attach_file;
				#
				#	Read the file to be attached
				#
				$data = '';
//				$file = @fopen($attach_path.$attach_file,'rb') or die('(emailer.inc) Cannot open: '.$attach_path.$attach_file);
				$file = @fopen($attach_path.$attach_file,'rb');							#	12/08/2009
				if($file)
				{
					while(!feof($file)) { $data .= @fread($file,4096); }
					@fclose($file);
				}
				#
				#	Base64 encode the file data
				#
				$data = chunk_split(base64_encode($data));
				#
				if($a==0)																#	25/11/2009: needed to send the body only once
				{
					#
					#	Complete headers
					#
					$headers .= 'Content-Type: multipart/mixed;'._CR_;
					$headers .= ' boundary="'.$mime_boundary.'"'."\n\n";
					#
					#	Add a multipart boundary above the text message
					#
					$mail_body_attach  = 'This is a multi-part message in MIME format.'._CR_;
					$mail_body_attach .= '--'.$mime_boundary."\n";
//					$mail_body_attach .= 'Content-Type: text/'.$text_type.'; charset=utf-8;'."\n";			# us-ascii,	iso-8859-1
					$mail_body_attach .= 'Content-Type: text/'.$text_type.'; charset='.$_CHARSET.';'."\n";	#	12/08/2009
					$mail_body_attach .= 'Content-Transfer-Encoding: 8bit'."\n\n";							# 7bit, 8bit
					$mail_body_attach .= $body."\n";
					#
					$body = $mail_body_attach;											#	INS 25/11/2009
				}
				#
				#	Add file attachment
				#
#
# MOD 25/11/2009 : BEG
#---------------------
//				$mail_body_attach .= '--'.$mime_boundary."\n";
//				$mail_body_attach .= 'Content-Type: '.$file_name_type.";\n";
//				$mail_body_attach .= ' name="'.$file_name_name.'"'."\n";
//				$mail_body_attach .= 'Content-Disposition: attachment;'."\n";
//				$mail_body_attach .= ' filename="'.$file_name_name.'"'."\n";
//				$mail_body_attach .= 'Content-Transfer-Encoding: base64'."\n\n";
//				$mail_body_attach .= $data."\n";
				#
//				$body = $mail_body_attach;
				#
				$mail_file_attach = '--'.$mime_boundary."\n";
				$mail_file_attach .= 'Content-Type: '.$file_name_type.";\n";
				$mail_file_attach .= ' name="'.$file_name_name.'"'."\n";
				$mail_file_attach .= 'Content-Disposition: attachment;'."\n";
				$mail_file_attach .= ' filename="'.$file_name_name.'"'."\n";
				$mail_file_attach .= 'Content-Transfer-Encoding: base64'."\n\n";
				$mail_file_attach .= $data."\n";
				#
				$body .= $mail_file_attach;
#---------------------
# MOD 25/11/2009 : END
#
				$a++;																	#	25/11/2009
			}
		}
		else
		{
			if($text_type=='plain')
			{
//				$headers .= 'Content-Type: text/'.$text_type.'; charset=utf-8;'._CR_;			# us-ascii, iso-8859-1
				$headers .= 'Content-Type: text/'.$text_type.'; charset='.$_CHARSET.';'."\n";	#	12/08/2009
				$headers .= 'Content-Transfer-Encoding: 8bit'._CR_;								# 7bit, 8bit
			}
			if ($text_type=='html')
			{
				$headers .= 'Content-Type: multipart/alternative;'._CR_;
				$headers .= ' boundary="'.$mime_boundary.'"'."\n\n";
				#
				#	Add ascii
				#
				$mail_body_multipart  = 'This is a multi-part message in MIME format.'._CR_;
				$mail_body_multipart .= '--'.$mime_boundary."\n";
//				$mail_body_multipart .= 'Content-Type: text/plain; charset=utf-8; format=flowed'."\n";
				$mail_body_multipart .= 'Content-Type: text/plain; charset='.$_CHARSET.'; format=flowed'."\n";	#	12/08/2009
				$mail_body_multipart .= 'Content-Transfer-Encoding: 8bit'."\n\n";
				$mail_body_multipart .= $body."\n\n";
				#
				#	Add html
				#
				$mail_body_multipart .= '--'.$mime_boundary."\n";
//				$mail_body_multipart .= 'Content-Type: text/html; charset=utf-8'."\n";
				$mail_body_multipart .= 'Content-Type: text/html; charset='.$_CHARSET."\n";						#	12/08/2009
				$mail_body_multipart .= 'Content-Transfer-Encoding: 8bit'."\n\n";
				$mail_body_multipart .= $body."\n";
				#
				$body = $mail_body_multipart."\n".'--'.$mime_boundary."--\n";
			}
		}
		#
		if($_SESSION['misc']['MAILSRV']!='ini')
		{
			if(strlen($_SESSION['misc']['MAILSRV'])!=0)
			{
				ini_set(SMTP,		$_SESSION['misc']['MAILSRV']);
			}
			if(strlen($_SESSION['misc']['MAILSRVPORT'])!=0)
			{
				ini_set(smtp_port,	$_SESSION['misc']['MAILSRVPORT']);
			}
		}
		if(strlen($_SESSION['misc']['E_SENDER'])!=0)
		{
			ini_set(sendmail_from,	$_SESSION['misc']['E_SENDER']);
		}
		#
		#	$extra_header = '-fwebmaster@{'.$domain.'}'; # this is the User of the machine or hosting account
		#
//echo 'Subject:'.$subject
//	.'<br>Reply:'.$reply
//	.'<br>cc:'.$cc
//	.'<br>To:'.$to
//	.'<br>Body:<br>'.$body
//	.'<br>From_id:'.$from_id
//	.'<br>headers:'.$headers
//	.'<br>Mail Server:'.$_SESSION['misc']['MAILSRV'].':'.$_SESSION['misc']['MAILSRVPORT']
//	.'<br>E sender:'.$_SESSION['misc']['E_SENDER']
//	;
//die();

//$tmp = false;	#debug
		$tmp = @mail($to, $subject, $body, $headers); #, $extra_header);
		#
		if($_SESSION['misc']['MAILSRV']!='ini')															#	12/08/2009
		{
			ini_restore(sendmail_from);
		}
		#
		if($tmp==true)
		{
			if($dbh)																					#	28/11/2009
			{
				_log_email(1, $from_id, $to, $reply, $subject, $user_body, $body, $headers);			#	12/08/2009
			}
			return '*OK*';
		}
		else
		{
			if($dbh)																					#	28/11/2009
			{
				_log_email(0, $from_id, $to, $reply, $subject, $user_body, $body, $headers);			#	12/08/2009
			}
			#
			echo '<hr>There has been a mail error sending to:'.$to.'<hr>';
//			if($USER_PRIV>=80)						#	18/02/2009
			if($USER_PRIV>79 || DEBUG==true)		#	18/02/2009
			{
				echo 'Subject:'.$subject
					.'<br>Reply:'.$reply
					.'<br>cc:'.$cc
					.'<br>Body:<br>'.$body
					.'<br>From_id:'.$from_id
					.'<br>Mail Server:'.$_SESSION['misc']['MAILSRV'].':'.$_SESSION['misc']['MAILSRVPORT']
					.'<br>Headers:'.$headers
				;
			}
			die('emailer');
		}
	}
	#~~~~~~~~~~~~~~~~~~~~~~~~~~
	function _log_email($code, $from_id, $to, $reply, $subject, $user_body, $body, $headers)	#	12/08/2009
	#~~~~~~~~~~~~~~~~~~~~~~~~~~
	{
		#	code:
		#			0 = not sent
		#			1 = sent
		#
		if(defined('TABLE_EMAIL_LOGS'))
		{
			$log_db = MAINSITE_DB;
		}
		else
		{
			define('TABLE_EMAIL_LOGS', 'logs_sendmail');
			//$log_db = CAMALEO_DB;		#	0.0.6
			$log_db = MED_OWN_DB;		#	0.0.6
		}
		if(!table_exists(TABLE_EMAIL_LOGS, $log_db)) { return; }
		#
		$sql = 'INSERT INTO '.$log_db.'.`'.TABLE_EMAIL_LOGS.'` '

			.'SET '
					.'code			= '.(int)($code).', '
					.'mailfrom		= \''.mysql_real_escape_string($from_id).'\', '
					.'mailto		= \''.mysql_real_escape_string($to).'\', '
					.'mailreply		= \''.mysql_real_escape_string($reply).'\', '
					.'subject		= \''.mysql_real_escape_string($subject).'\', '
					.'body			= \''.mysql_real_escape_string($user_body).'\', '
					.'mailbody		= \''.mysql_real_escape_string($body).'\', '
					.'smtp			= \''.mysql_real_escape_string($_SESSION['misc']['MAILSRV']).'\', '
					.'smtp_port		= \''.mysql_real_escape_string($_SESSION['misc']['MAILSRVPORT']).'\', '
					.'e_sender		= \''.mysql_real_escape_string($_SESSION['misc']['E_SENDER']).'\', '
					.'headers		= \''.mysql_real_escape_string($headers).'\', '
					.'referer		= \''.mysql_real_escape_string($_SERVER['HTTP_REFERER']).'\', '
					.'request_uri	= \''.mysql_real_escape_string($_SERVER['REQUEST_URI']).'\', '
					.'request_ip	= \''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\' '
		;
		$sth = db_query($sql,__LINE__,__FILE__);
		return;
	}
}
?>

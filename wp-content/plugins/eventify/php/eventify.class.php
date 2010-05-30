<?php

if(!class_exists("eventify"))
{
	
	class eventify{
	
		var $wp_version = "";
		function eventify()
		{
				
		
			
		}//end of constructor
		
		function options_panel() 
		{
			global $wpdb;
					$isedit = false;
				 $message="";
				$editrow = "";
				if(isset($_POST['eddem']) && $_POST['eddem']=="1")
				{
					$editrow="";
					$isedit= false;
					global $wpdb;
					$nonce = $_POST['nonce-eventify'];
					if (!wp_verify_nonce($nonce, 'eventify-nonce')) die ( 'Security Check - If you receive this in error, log out and back in to WordPress');
						$table_name = $wpdb->prefix."em_main";
						$sql = "update ".$table_name." set em_date='".$_POST['datepicker']."', em_time='".$_POST['timepick_from']."-".$_POST['timepick_to']."', em_desc='".str_ireplace("'","\'",$_POST['em_desc'])."', em_title='".str_ireplace("'","\'",$_POST['em_title'])."', em_venue='".str_ireplace("'","\'",$_POST['em_venue'])."', em_timezone='".$_POST['em_timezone']."', em_timestamp='".strtotime($_POST['datepicker'])."' where em_id='".$_POST['emidd']."';";
				 	
				 	//echo $sql;
					$wpdb->query($sql);
				 	update_option("em_timezone",$_POST['em_timezone']);
					 $message="Event Updated!";
		 	
					$wpdb->print_error();
				}
				
				 if(isset($_POST['eem']))
				{
					//show the edit event form and display the values from the db
					$isedit=true;
					global $wpdb;
					$nonce = $_POST['nonce-eventify'];
					if (!wp_verify_nonce($nonce, 'eventify-nonce')) die ( 'Security Check - If you receive this in error, log out and back in to WordPress');
						$table_name = $wpdb->prefix."em_main";
						
						 	$sqlqry = "Select * from ".$table_name." where em_id='".$_POST['eem']."'";
						 	$editrow = $wpdb->get_results($sqlqry);
						
					
				}
				 if(isset($_POST['addem']) && $_POST['addem']=="1")
				 {
				 	$nonce = $_POST['nonce-eventify'];
					if (!wp_verify_nonce($nonce, 'eventify-nonce')) die ( 'Security Check - If you receive this in error, log out and back in to WordPress');
				 	$message = "Event Added! :)";
		 	
				 	//add the event to the db and the table :)
				 	$table_name = $wpdb->prefix."em_main";
					if($_POST['saveitaspopup']=="posted")
					{
					// Create post object
			
			  			$my_post = array();
			  			$my_post['post_title'] = $_POST['em_title'];
			  			$my_post['post_content'] = "<h2>Event Description:</h2>".$_POST['em_desc']."<br/><h2>Event Date</h2>".$_POST['datepicker']."<br/><h2>Event Time</h2> From: ".$_POST['timepick_from']." - To: ".$_POST['timepick_to']." <br/><h2>Event Venue</h2>".$_POST['em_venue']."<br/><h2>Event TimeZone</h2>".$_POST['em_timezone'];
			  			$my_post['post_status'] = 'publish';
			  			$my_post['post_author'] = 1;
			 			$my_post['post_category'] = array(99=>'Events');
			
						// Insert the post into the database
			 			$postedid = wp_insert_post( $my_post );
				
							$sql = "insert into ".$table_name." values(null,'".$_POST['datepicker']."','".$_POST['timepick_from']."-".$_POST['timepick_to']." ','".str_ireplace("'","\'",$_POST['em_desc'])."','".str_ireplace("'","\'",$_POST['em_title'])."','".str_ireplace("'","\'",$_POST['em_venue'])."','".$_POST['em_timezone']."','".$postedid."','".strtotime($_POST['datepicker'])."')";
			 	
				 	}
				 	else
				 	{
				 		$sql = "insert into ".$table_name." values(null,'".$_POST['datepicker']."','".$_POST['timepick_from']."-".$_POST['timepick_to']."','".str_ireplace("'","\'",$_POST['em_desc'])."','".str_ireplace("'","\'",$_POST['em_title'])."','".str_ireplace("'","\'",$_POST['em_venue'])."','".$_POST['em_timezone']."','0','".strtotime($_POST['datepicker'])."')";
				 	}
				 	
					$wpdb->query($sql);
				 	update_option("em_timezone",$_POST['em_timezone']);

		 	
					$wpdb->print_error();
		 	
		 }
	
		 if(isset($_POST['delem']) && $_POST['delem']=="1")
		 {
				if(!empty($_POST['delemid']))
				{
					$nonce = $_POST['nonceeventify'];
					if (!wp_verify_nonce($nonce, 'eventify-nonce')) die ( 'Security Check - If you receive this in error, log out and back in to WordPress');
					//$message = "Selected Event(s) Deleted! :)";
				 	$delidsem = implode(",",$_POST['delemid']);
				 	//echo $delidsem;
				 	//delete the event(s) from the db and the table :)
				 	$table_name = $wpdb->prefix."em_main";
				 	$sql_qury = "delete from ".$table_name." where em_id in (".$delidsem.")";
				 	$message = "Event(s) Deleted. :)";
				 	$wpdb->query($sql_qury);
					$wpdb->print_error();
				}
				else
				{
					$message="<span style='color:red;'>No events selected for deletion</span>";
				}
		 }
		
		/*code for backing up events*/
		 if(isset($_POST['backemall']) && $_POST['backemall']=="1")
		 {
			global $wpdb;
			$table_name = $wpdb->prefix."em_main";
			$qry= "select * from ".$table_name." order by em_timestamp ASC" ;
			$results = $wpdb->get_results($qry);
			$backupfilename="events.csv";
			$backup_file_handle = fopen(WP_PLUGIN_DIR."/eventify/uploads/".$backupfilename,'w') or die('Something terribly went wrong with creating the file');
			require_once(WP_PLUGIN_DIR.'/eventify/php/parsecsv.lib.php');
			$csv = new parseCSV();
				//print_r($results);
				$i=1;
				$data[0] = array('title' => 'title', 'date' => 'date', 'time' => 'time','desc'=>'desc','venue'=>'venue','timezone'=>'timezone');
				foreach($results as $row)
				{
					$data[$i] = array('title' => $row->em_title, 'date' => $row->em_date, 'timefromtimeto' => $row->em_time,'desc'=>$row->em_desc,'venue'=>$row->em_venue,'timezone'=>$row->em_timezone);
					$i++;	
				}
				$csv->save(WP_PLUGIN_DIR."/eventify/uploads/".$backupfilename, $data,true);
				
				fclose($backup_file_handle);
				$message= "Events backup complete. <a href=\"".WP_PLUGIN_URL."/eventify/uploads/".$backupfilename."\">Save file</a> by right click->save as. Save the file as .csv file";
			
				
				
		 }//code for backing up events ends here
		
		if(isset($_POST['uploadems']) && $_POST['uploadems']=="1") //code for bulk uploads
		{
		 	$nonce = $_POST['nonce-eventify'];
		 	if (!wp_verify_nonce($nonce, 'eventify-nonce')) die ( 'Security Check - If you receive this in error, log out and back in to WordPress');
		 	
		 	if(isset($_POST['postedfpc']))
			{

				$uploaddir = WP_PLUGIN_DIR."/eventify/uploads/"; 

				$target_path = $uploaddir.basename($_FILES['csvfile']['name']); 
				//echo $target_path;
  				//echo getcwd();
 
				//$allowedExtensions_ = ;

				
				
				function isAllowedExtension($fileName) 
				{
				 	global $allowedExtensions_; 

 				 	return in_array(end(explode(".", $fileName)), array("csv"));
				} 
				
				if(($_FILES["csvfile"]["type"]=='text/plain') && (isAllowedExtension($_FILES['csvfile']['name'])))
				{	
					if(move_uploaded_file($_FILES['csvfile']['tmp_name'], $target_path)) 
					{
					require_once(WP_PLUGIN_DIR.'/eventify/php/parsecsv.lib.php');
					$csv = new parseCSV($target_path);
					$csv->auto();
					//print_r($csv); use for debuggin
					$no_of_rows = count($csv->data);
					$no_of_cols = count($csv->data[0]);
					if($no_of_cols!="6")
					{
						die('Uploaded file is in the wrong format.. <a href="http://designerfoo.com/wordpress-plugin-eventify-simple-events-management">please click here for the tutorial..</a>');
					}
					global $wpdb;
					$table_name = $wpdb->prefix."em_main";
					$sql_q = "insert into ".$table_name." values ";
					for($i=0;$i<$no_of_rows;$i++)
					{
						//insert the data into the database..
						
								$sql_q.="(null,'".$csv->data[$i][$csv->titles[$no_of_cols-5]]."','".$csv->data[$i][$csv->titles[$no_of_cols-4]]."','".str_ireplace("'","\'",$csv->data[$i][$csv->titles[$no_of_cols-3]])."','".str_ireplace("'","\'",$csv->data[$i][$csv->titles[$no_of_cols-6]])."','".str_ireplace("'","\'",$csv->data[$i][$csv->titles[$no_of_cols-2]])."','".str_ireplace("'","\'",$csv->data[$i][$csv->titles[$no_of_cols-1]])."','0','".strtotime($csv->data[$i][$csv->titles[$no_of_cols-5]])."')";
						if(($no_of_rows-$i)!=1)
						{
							$sql_q.=", ";
						}
						//echo 
						//echo 
						//echo ;
						//echo ;
					}
					
					$sql_q.=";";
					//echo $sql_q;
					
						$wpdb->query($sql_q);
						$wpdb->print_error();
						$message="Bulk upload of events complete :)";
					//print count($csv->data[0]);
					}//nested if for uploading part ends here if...
				} //uploading part ends here if...
			
			
		 	}//postedfpc if ends here
		 }	//main if ends here
		 
		 
?>
		<div class="wrap">
		<h2>Eventify - Control Panel</h2>
		<h4>A plugin to store and show upcoming events in wordpress widgets/sidebar.</h4>
		<h4>It's alright if you don't donate, just help spread the word about <a href="http://foo.tc/singledin" target="_new">Singled.in - A Webcomic Series about Online &amp; New Age Dating.</a> and if you like it, do subscribe!</h4>
		
		<?php if ($message) : ?>
<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php endif; ?><br/>

	<div style="float:left;clear:both;">
	
	<h3>Enter Events To Display</h3>
	<div id="wrapper">
<script type="text/javascript">
	jQuery(function() {
		jQuery("#datepicker").datepicker();
		jQuery("#timepick_from").timePicker();
		jQuery("#timepick_to").timePicker();
		jQuery("#backeventifyform").validate();
	});
	</script>
	<Style>
div.row { padding-top: 5px; clear: both; width:960px;} div.left { float: left;  text-align: left;margin-left:20px; width:80px;} div.right { margin-left: 110px; }
div.time-picker {
  position: absolute;
  height: 200px;
  width:4em; /* needed for IE */
  overflow: auto;
  background: #fff;
  border: 1px solid #000;
  z-index: 99;
}
div.time-picker-12hours {
  width:6em; /* needed for IE */
}

div.time-picker ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}
div.time-picker li {
  padding: 1px;
  cursor: pointer;
}
div.time-picker li.selected {
  background: #316AC5;
  color: #fff;
}
</style>
<form action="" method="post" id="backeventifyform">
	<div class="row">  
		<div class="left"><strong>Event Title:</strong></div>  
		<div class="right"><input type="text" size="25" id="em_title" name="em_title" class="required" <?php if($isedit){?> value="<?php echo $editrow[0]->em_title; ?>" <?php } ?>></div> 
	</div> 
	<div class="row">  
		<div class="left"><strong>Event Date:</strong></div> 
		<div class="right"><input type="text" size="25" id="datepicker" name="datepicker" READONLY class="required" <?php if($isedit){?> value="<?php echo $editrow[0]->em_date; ?>" <?php } ?>>*For displaying the date in a particular format use the widget :)</div>
	</div>

	<div class="row"> 
		<div class="left"><strong>Time - From</strong></div>  
		<div class="right"><input type="text" size="25" id="timepick_from" name="timepick_from" READONLY class="required" <?php if($isedit){?> value="<?php echo substr($editrow[0]->em_time,0,-6); ?>" <?php } ?>></div>
	</div> 
	<div class="row">
		<div class="left"><strong>Time - To</strong></div>
		<div class="right"><input type="text" size="25" id="timepick_to" name="timepick_to" READONLY class="required" <?php if($isedit){?> value="<?php echo substr($editrow[0]->em_time,-5); ?>" <?php } ?>></div>
	</div>
	<div class="row">
		<div class="left"><strong>Event Venue:</strong></div>  
		<div class="right"><input type="text" size="25" id="em_venue" name="em_venue" class="required" <?php if($isedit){?> value="<?php echo $editrow[0]->em_venue; ?>" <?php } ?>></div> 
	</div>
	<div class="row"> 
		<div class="left"><strong>Event Details:</strong></div>  
		<div class="right"><textarea id="em_desc" name="em_desc"><?php if($isedit){?> <?php echo $editrow[0]->em_title; ?> <?php } ?></textarea></div> 
	</div> 
	<div class="row"> 
		<div class="left"><strong>Event TimeZone:</strong></div>  
		<div class="right"><select name="em_timezone" id="em_timezone">
<option <?php if(preg_match('/none/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="none" >Select your local timezone....</option>
<option <?php if(preg_match('/Canada\/Atlantic/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="Canada/Atlantic">Canada/Atlantic</option>
<option <?php if(preg_match('/Canada\/Central/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="Canada/Central">Canada/Central</option>
<option <?php if(preg_match('/Canada\/East-Saskatchewan/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="Canada/East-Saskatchewan">Canada/East-Saskatchewan</option>
<option <?php if(preg_match('/Canada\/Eastern/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="Canada/Eastern">Canada/Eastern</option>
<option <?php if(preg_match('/Canada\/Mountain/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="Canada/Mountain">Canada/Mountain</option>
<option <?php if(preg_match('/Canada\/Newfoundland/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="Canada/Newfoundland">Canada/Newfoundland</option>
<option <?php if(preg_match('/Canada\/Pacific/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="Canada/Pacific">Canada/Pacific</option>
<option <?php if(preg_match('/Canada\/Saskatchewan/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="Canada/Saskatchewan">Canada/Saskatchewan</option>
<option <?php if(preg_match('/Canada\/Yukon/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="Canada/Yukon">Canada/Yukon</option>
<option <?php if(preg_match('/US\/Alaska/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Alaska">US/Alaska</option>
<option <?php if(preg_match('/US\/Aleutian/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Aleutian">US/Aleutian</option>
<option <?php if(preg_match('/US\/Arizona/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Arizona">US/Arizona</option>
<option <?php if(preg_match('/US\/Central/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Central">US/Central</option>
<option <?php if(preg_match('/US\/East-Indiana/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/East-Indiana">US/East-Indiana</option>
<option <?php if(preg_match('/US\/Eastern/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Eastern">US/Eastern</option>
<option <?php if(preg_match('/US\/Hawaii/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Hawaii">US/Hawaii</option>
<option <?php if(preg_match('/US\/Indiana-Starke/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Indiana-Starke">US/Indiana-Starke</option>
<option <?php if(preg_match('/US\/Michigan/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Michigan">US/Michigan</option>
<option <?php if(preg_match('/US\/Mountain/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Mountain">US/Mountain</option>
<option <?php if(preg_match('/US\/Pacific/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Pacific">US/Pacific</option>
<option <?php if(preg_match('/US\/Samoa/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="US/Samoa">US/Samoa</option>
<option <?php if(preg_match('/Pacific\/Apia/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-11) Pacific/Apia">(GMT-11) Pacific/Apia</option>
<option <?php if(preg_match('/Pacific\/Midway/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-11) Pacific/Midway">(GMT-11) Pacific/Midway</option>
<option <?php if(preg_match('/Pacific\/Niue/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-11) Pacific/Niue">(GMT-11) Pacific/Niue</option>
<option <?php if(preg_match('/Pacific\/Pago_Pago/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-11) Pacific/Pago_Pago">(GMT-11) Pacific/Pago_Pago</option>
<option <?php if(preg_match('/Pacific\/Samoa/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-11) Pacific/Samoa">(GMT-11) Pacific/Samoa</option>
<option <?php if(preg_match('/America\/Adak/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-10) America/Adak">(GMT-10) America/Adak</option>
<option <?php if(preg_match('/America\/Atka/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-10) America/Atka">(GMT-10) America/Atka</option>
<option <?php if(preg_match('/Pacific\/Fakaofo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-10) Pacific/Fakaofo">(GMT-10) Pacific/Fakaofo</option>
<option <?php if(preg_match('/Pacific\/Honolulu/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-10) Pacific/Honolulu">(GMT-10) Pacific/Honolulu</option>
<option <?php if(preg_match('/Pacific\/Johnston/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-10) Pacific/Johnston">(GMT-10) Pacific/Johnston</option>
<option <?php if(preg_match('/Pacific\/Rarotonga/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-10) Pacific/Rarotonga">(GMT-10) Pacific/Rarotonga</option>
<option <?php if(preg_match('/Pacific\/Tahiti/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-10) Pacific/Tahiti">(GMT-10) Pacific/Tahiti</option>
<option <?php if(preg_match('/Pacific\/Marquesas/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-9.5) Pacific/Marquesas">(GMT-9.5) Pacific/Marquesas</option>
<option <?php if(preg_match('/America\/Anchorage/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-9) America/Anchorage">(GMT-9) America/Anchorage</option>
<option <?php if(preg_match('/America\/Juneau/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-9) America/Juneau">(GMT-9) America/Juneau</option>
<option <?php if(preg_match('/America\/Nome/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-9) America/Nome">(GMT-9) America/Nome</option>
<option <?php if(preg_match('/America\/Yakutat/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-9) America/Yakutat">(GMT-9) America/Yakutat</option>
<option <?php if(preg_match('/Pacific\/Gambier/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-9) Pacific/Gambier">(GMT-9) Pacific/Gambier</option>
<option <?php if(preg_match('/America\/Dawson/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-8) America/Dawson">(GMT-8) America/Dawson</option>
<option <?php if(preg_match('/America\/Ensenada/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-8) America/Ensenada">(GMT-8) America/Ensenada</option>
<option <?php if(preg_match('/America\/Los_Angeles/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-8) America/Los_Angeles">(GMT-8) America/Los_Angeles</option>
<option <?php if(preg_match('/America\/Tijuana/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-8) America/Tijuana">(GMT-8) America/Tijuana</option>
<option <?php if(preg_match('/America\/Vancouver/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-8) America/Vancouver">(GMT-8) America/Vancouver</option>
<option <?php if(preg_match('/America\/Whitehorse/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-8) America/Whitehorse">(GMT-8) America/Whitehorse</option>
<option <?php if(preg_match('/Pacific\/Pitcairn/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-8) Pacific/Pitcairn">(GMT-8) Pacific/Pitcairn</option>
<option <?php if(preg_match('/America\/Boise/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Boise">(GMT-7) America/Boise</option>
<option <?php if(preg_match('/America\/Cambridge_Bay/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Cambridge_Bay">(GMT-7) America/Cambridge_Bay</option>
<option <?php if(preg_match('/America\/Chihuahua/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Chihuahua">(GMT-7) America/Chihuahua</option>
<option <?php if(preg_match('/America\/Dawson_Creek/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Dawson_Creek">(GMT-7) America/Dawson_Creek</option>
<option <?php if(preg_match('/America\/Denver/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Denver">(GMT-7) America/Denver</option>
<option <?php if(preg_match('/America\/Edmonton/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Edmonton">(GMT-7) America/Edmonton</option>
<option <?php if(preg_match('/America\/Hermosillo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Hermosillo">(GMT-7) America/Hermosillo</option>
<option <?php if(preg_match('/America\/Inuvik/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Inuvik">(GMT-7) America/Inuvik</option>
<option <?php if(preg_match('/America\/Mazatlan/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Mazatlan">(GMT-7) America/Mazatlan</option>
<option <?php if(preg_match('/America\/Phoenix/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Phoenix">(GMT-7) America/Phoenix</option>
<option <?php if(preg_match('/America\/Shiprock/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Shiprock">(GMT-7) America/Shiprock</option>
<option <?php if(preg_match('/America\/Yellowknife/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-7) America/Yellowknife">(GMT-7) America/Yellowknife</option>
<option <?php if(preg_match('/America\/Belize/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Belize">(GMT-6) America/Belize</option>
<option <?php if(preg_match('/America\/Cancun/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Cancun">(GMT-6) America/Cancun</option>
<option <?php if(preg_match('/America\/Chicago/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Chicago">(GMT-6) America/Chicago</option>
<option <?php if(preg_match('/America\/Costa_Rica/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Costa_Rica">(GMT-6) America/Costa_Rica</option>
<option <?php if(preg_match('/America\/El_Salvador/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/El_Salvador">(GMT-6) America/El_Salvador</option>
<option <?php if(preg_match('/America\/Guatemala/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Guatemala">(GMT-6) America/Guatemala</option>
<option <?php if(preg_match('/America\/Knox_IN/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Knox_IN">(GMT-6) America/Knox_IN</option>
<option <?php if(preg_match('/America\/Managua/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Managua">(GMT-6) America/Managua</option>
<option <?php if(preg_match('/America\/Menominee/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Menominee">(GMT-6) America/Menominee</option>
<option <?php if(preg_match('/America\/Merida/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Merida">(GMT-6) America/Merida</option>
<option <?php if(preg_match('/America\/Mexico_City/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Mexico_City">(GMT-6) America/Mexico_City</option>
<option <?php if(preg_match('/America\/Monterrey/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Monterrey">(GMT-6) America/Monterrey</option>
<option <?php if(preg_match('/America\/Rainy_River/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Rainy_River">(GMT-6) America/Rainy_River</option>
<option <?php if(preg_match('/America\/Rankin_Inlet/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Rankin_Inlet">(GMT-6) America/Rankin_Inlet</option>
<option <?php if(preg_match('/America\/Regina/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Regina">(GMT-6) America/Regina</option>
<option <?php if(preg_match('/America\/Swift_Current/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Swift_Current">(GMT-6) America/Swift_Current</option>
<option <?php if(preg_match('/America\/Tegucigalpa/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Tegucigalpa">(GMT-6) America/Tegucigalpa</option>
<option <?php if(preg_match('/America\/Winnipeg/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) America/Winnipeg">(GMT-6) America/Winnipeg</option>
<option <?php if(preg_match('/Pacific\/Easter/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) Pacific/Easter">(GMT-6) Pacific/Easter</option>
<option <?php if(preg_match('/Pacific\/Galapagos/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-6) Pacific/Galapagos">(GMT-6) Pacific/Galapagos</option>
<option <?php if(preg_match('/America\/Atikokan/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Atikokan">(GMT-5) America/Atikokan</option>
<option <?php if(preg_match('/America\/Bogota/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Bogota">(GMT-5) America/Bogota</option>
<option <?php if(preg_match('/America\/Cayman/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Cayman">(GMT-5) America/Cayman</option>
<option <?php if(preg_match('/America\/Coral_Harbour/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Coral_Harbour">(GMT-5) America/Coral_Harbour</option>
<option <?php if(preg_match('/America\/Detroit/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Detroit">(GMT-5) America/Detroit</option>
<option <?php if(preg_match('/America\/Fort_Wayne/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Fort_Wayne">(GMT-5) America/Fort_Wayne</option>
<option <?php if(preg_match('/America\/Grand_Turk/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Grand_Turk">(GMT-5) America/Grand_Turk</option>
<option <?php if(preg_match('/America\/Guayaquil/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Guayaquil">(GMT-5) America/Guayaquil</option>
<option <?php if(preg_match('/America\/Havana/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Havana">(GMT-5) America/Havana</option>
<option <?php if(preg_match('/America\/Indianapolis/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Indianapolis">(GMT-5) America/Indianapolis</option>
<option <?php if(preg_match('/America\/Iqaluit/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Iqaluit">(GMT-5) America/Iqaluit</option>
<option <?php if(preg_match('/America\/Jamaica/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Jamaica">(GMT-5) America/Jamaica</option>
<option <?php if(preg_match('/America\/Lima/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Lima">(GMT-5) America/Lima</option>
<option <?php if(preg_match('/America\/Louisville/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Louisville">(GMT-5) America/Louisville</option>
<option <?php if(preg_match('/America\/Montreal/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Montreal">(GMT-5) America/Montreal</option>
<option <?php if(preg_match('/America\/Nassau/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Nassau">(GMT-5) America/Nassau</option>
<option <?php if(preg_match('/America\/New_York/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/New_York">(GMT-5) America/New_York</option>
<option <?php if(preg_match('/America\/Nipigon/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Nipigon">(GMT-5) America/Nipigon</option>
<option <?php if(preg_match('/America\/Panama/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Panama">(GMT-5) America/Panama</option>
<option <?php if(preg_match('/America\/Pangnirtung/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Pangnirtung">(GMT-5) America/Pangnirtung</option>
<option <?php if(preg_match('/America\/Port-au-Prince/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Port-au-Prince">(GMT-5) America/Port-au-Prince</option>
<option <?php if(preg_match('/America\/Resolute/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Resolute">(GMT-5) America/Resolute</option>
<option <?php if(preg_match('/America\/Thunder_Bay/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Thunder_Bay">(GMT-5) America/Thunder_Bay</option>
<option <?php if(preg_match('/America\/Toronto/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-5) America/Toronto">(GMT-5) America/Toronto</option>
<option <?php if(preg_match('/America\/Caracas/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4.5) America/Caracas">(GMT-4.5) America/Caracas</option>
<option <?php if(preg_match('/America\/Anguilla/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Anguilla">(GMT-4) America/Anguilla</option>
<option <?php if(preg_match('/America\/Antigua/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Antigua">(GMT-4) America/Antigua</option>
<option <?php if(preg_match('/America\/Aruba/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Aruba">(GMT-4) America/Aruba</option>
<option <?php if(preg_match('/America\/Asuncion/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Asuncion">(GMT-4) America/Asuncion</option>
<option <?php if(preg_match('/America\/Barbados/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Barbados">(GMT-4) America/Barbados</option>
<option <?php if(preg_match('/America\/Blanc-Sablon/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Blanc-Sablon">(GMT-4) America/Blanc-Sablon</option>
<option <?php if(preg_match('/America\/Boa_Vista/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Boa_Vista">(GMT-4) America/Boa_Vista</option>
<option <?php if(preg_match('/America\/Campo_Grande/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Campo_Grande">(GMT-4) America/Campo_Grande</option>
<option <?php if(preg_match('/America\/Cuiaba/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Cuiaba">(GMT-4) America/Cuiaba</option>
<option <?php if(preg_match('/America\/Curacao/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Curacao">(GMT-4) America/Curacao</option>
<option <?php if(preg_match('/America\/Dominica/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Dominica">(GMT-4) America/Dominica</option>
<option <?php if(preg_match('/America\/Eirunepe/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Eirunepe">(GMT-4) America/Eirunepe</option>
<option <?php if(preg_match('/America\/Glace_Bay/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Glace_Bay">(GMT-4) America/Glace_Bay</option>
<option <?php if(preg_match('/America\/Goose_Bay/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Goose_Bay">(GMT-4) America/Goose_Bay</option>
<option <?php if(preg_match('/America\/Grenada/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Grenada">(GMT-4) America/Grenada</option>
<option <?php if(preg_match('/America\/Guadeloupe/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Guadeloupe">(GMT-4) America/Guadeloupe</option>
<option <?php if(preg_match('/America\/Guyana/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Guyana">(GMT-4) America/Guyana</option>
<option <?php if(preg_match('/America\/Halifax/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Halifax">(GMT-4) America/Halifax</option>
<option <?php if(preg_match('/America\/La_Paz/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/La_Paz">(GMT-4) America/La_Paz</option>
<option <?php if(preg_match('/America\/Manaus/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Manaus">(GMT-4) America/Manaus</option>
<option <?php if(preg_match('/America\/Marigot/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Marigot">(GMT-4) America/Marigot</option>
<option <?php if(preg_match('/America\/Martinique/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Martinique">(GMT-4) America/Martinique</option>
<option <?php if(preg_match('/America\/Moncton/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Moncton">(GMT-4) America/Moncton</option>
<option <?php if(preg_match('/America\/Montserrat/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Montserrat">(GMT-4) America/Montserrat</option>
<option <?php if(preg_match('/America\/Port_of_Spain/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Port_of_Spain">(GMT-4) America/Port_of_Spain</option>
<option <?php if(preg_match('/America\/Porto_Acre/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Porto_Acre">(GMT-4) America/Porto_Acre</option>
<option <?php if(preg_match('/America\/Porto_Velho/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Porto_Velho">(GMT-4) America/Porto_Velho</option>
<option <?php if(preg_match('/America\/Puerto_Rico/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Puerto_Rico">(GMT-4) America/Puerto_Rico</option>
<option <?php if(preg_match('/America\/Rio_Branco/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Rio_Branco">(GMT-4) America/Rio_Branco</option>
<option <?php if(preg_match('/America\/Santiago/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Santiago">(GMT-4) America/Santiago</option>
<option <?php if(preg_match('/America\/Santo_Domingo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Santo_Domingo">(GMT-4) America/Santo_Domingo</option>
<option <?php if(preg_match('/America\/St_Barthelemy/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/St_Barthelemy">(GMT-4) America/St_Barthelemy</option>
<option <?php if(preg_match('/America\/St_Kitts/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/St_Kitts">(GMT-4) America/St_Kitts</option>
<option <?php if(preg_match('/America\/St_Lucia/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/St_Lucia">(GMT-4) America/St_Lucia</option>
<option <?php if(preg_match('/America\/St_Thomas/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/St_Thomas">(GMT-4) America/St_Thomas</option>
<option <?php if(preg_match('/America\/St_Vincent/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/St_Vincent">(GMT-4) America/St_Vincent</option>
<option <?php if(preg_match('/America\/Thule/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Thule">(GMT-4) America/Thule</option>
<option <?php if(preg_match('/America\/Tortola/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Tortola">(GMT-4) America/Tortola</option>
<option <?php if(preg_match('/America\/Virgin/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) America/Virgin">(GMT-4) America/Virgin</option>
<option <?php if(preg_match('/Antarctica\/Palmer/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) Antarctica/Palmer">(GMT-4) Antarctica/Palmer</option>
<option <?php if(preg_match('/Atlantic\/Bermuda/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) Atlantic/Bermuda">(GMT-4) Atlantic/Bermuda</option>
<option <?php if(preg_match('/Atlantic\/Stanley/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-4) Atlantic/Stanley">(GMT-4) Atlantic/Stanley</option>
<option <?php if(preg_match('/America\/St_Johns/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3.5) America/St_Johns">(GMT-3.5) America/St_Johns</option>
<option <?php if(preg_match('/America\/Araguaina/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Araguaina">(GMT-3) America/Araguaina</option>
<option <?php if(preg_match('/America\/Bahia/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Bahia">(GMT-3) America/Bahia</option>
<option <?php if(preg_match('/America\/Belem/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Belem">(GMT-3) America/Belem</option>
<option <?php if(preg_match('/America\/Buenos_Aires/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Buenos_Aires">(GMT-3) America/Buenos_Aires</option>
<option <?php if(preg_match('/America\/Catamarca/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Catamarca">(GMT-3) America/Catamarca</option>
<option <?php if(preg_match('/America\/Cayenne/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Cayenne">(GMT-3) America/Cayenne</option>
<option <?php if(preg_match('/America\/Cordoba/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Cordoba">(GMT-3) America/Cordoba</option>
<option <?php if(preg_match('/America\/Fortaleza/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Fortaleza">(GMT-3) America/Fortaleza</option>
<option <?php if(preg_match('/America\/Godthab/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Godthab">(GMT-3) America/Godthab</option>
<option <?php if(preg_match('/America\/Jujuy/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Jujuy">(GMT-3) America/Jujuy</option>
<option <?php if(preg_match('/America\/Maceio/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Maceio">(GMT-3) America/Maceio</option>
<option <?php if(preg_match('/America\/Mendoza/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Mendoza">(GMT-3) America/Mendoza</option>
<option <?php if(preg_match('/America\/Miquelon/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Miquelon">(GMT-3) America/Miquelon</option>
<option <?php if(preg_match('/America\/Montevideo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Montevideo">(GMT-3) America/Montevideo</option>
<option <?php if(preg_match('/America\/Paramaribo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Paramaribo">(GMT-3) America/Paramaribo</option>
<option <?php if(preg_match('/America\/Recife/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Recife">(GMT-3) America/Recife</option>
<option <?php if(preg_match('/America\/Rosario/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Rosario">(GMT-3) America/Rosario</option>
<option <?php if(preg_match('/America\/Santarem/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Santarem">(GMT-3) America/Santarem</option>
<option <?php if(preg_match('/America\/Sao_Paulo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) America/Sao_Paulo">(GMT-3) America/Sao_Paulo</option>
<option <?php if(preg_match('/Antarctica\/Rothera/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-3) Antarctica/Rothera">(GMT-3) Antarctica/Rothera</option>
<option <?php if(preg_match('/America\/Noronha/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-2) America/Noronha">(GMT-2) America/Noronha</option>
<option <?php if(preg_match('/Atlantic\/South_Georgia/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-2) Atlantic/South_Georgia">(GMT-2) Atlantic/South_Georgia</option>
<option <?php if(preg_match('/America\/Scoresbysund/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-1) America/Scoresbysund">(GMT-1) America/Scoresbysund</option>
<option <?php if(preg_match('/Atlantic\/Azores/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-1) Atlantic/Azores">(GMT-1) Atlantic/Azores</option>
<option <?php if(preg_match('/Atlantic\/Cape_Verde/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT-1) Atlantic/Cape_Verde">(GMT-1) Atlantic/Cape_Verde</option>
<option <?php if(preg_match('/Africa\/Abidjan/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Abidjan">(GMT+0) Africa/Abidjan</option>
<option <?php if(preg_match('/Africa\/Accra/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Accra">(GMT+0) Africa/Accra</option>
<option <?php if(preg_match('/Africa\/Bamako/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Bamako">(GMT+0) Africa/Bamako</option>
<option <?php if(preg_match('/Africa\/Banjul/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Banjul">(GMT+0) Africa/Banjul</option>
<option <?php if(preg_match('/Africa\/Bissau/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Bissau">(GMT+0) Africa/Bissau</option>
<option <?php if(preg_match('/Africa\/Casablanca/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Casablanca">(GMT+0) Africa/Casablanca</option>
<option <?php if(preg_match('/Africa\/Conakry/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Conakry">(GMT+0) Africa/Conakry</option>
<option <?php if(preg_match('/Africa\/Dakar/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Dakar">(GMT+0) Africa/Dakar</option>
<option <?php if(preg_match('/Africa\/El_Aaiun/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/El_Aaiun">(GMT+0) Africa/El_Aaiun</option>
<option <?php if(preg_match('/Africa\/Freetown/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Freetown">(GMT+0) Africa/Freetown</option>
<option <?php if(preg_match('/Africa\/Lome/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Lome">(GMT+0) Africa/Lome</option>
<option <?php if(preg_match('/Africa\/Monrovia/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Monrovia">(GMT+0) Africa/Monrovia</option>
<option <?php if(preg_match('/Africa\/Nouakchott/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Nouakchott">(GMT+0) Africa/Nouakchott</option>
<option <?php if(preg_match('/Africa\/Ouagadougou/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Ouagadougou">(GMT+0) Africa/Ouagadougou</option>
<option <?php if(preg_match('/Africa\/Sao_Tome/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Sao_Tome">(GMT+0) Africa/Sao_Tome</option>
<option <?php if(preg_match('/Africa\/Timbuktu/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Africa/Timbuktu">(GMT+0) Africa/Timbuktu</option>
<option <?php if(preg_match('/America\/Danmarkshavn/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) America/Danmarkshavn">(GMT+0) America/Danmarkshavn</option>
<option <?php if(preg_match('/Atlantic\/Canary/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Atlantic/Canary">(GMT+0) Atlantic/Canary</option>
<option <?php if(preg_match('/Atlantic\/Faeroe/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Atlantic/Faeroe">(GMT+0) Atlantic/Faeroe</option>
<option <?php if(preg_match('/Atlantic\/Faroe/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Atlantic/Faroe">(GMT+0) Atlantic/Faroe</option>
<option <?php if(preg_match('/Atlantic\/Madeira/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Atlantic/Madeira">(GMT+0) Atlantic/Madeira</option>
<option <?php if(preg_match('/Atlantic\/Reykjavik/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Atlantic/Reykjavik">(GMT+0) Atlantic/Reykjavik</option>
<option <?php if(preg_match('/Atlantic\/St_Helena/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Atlantic/St_Helena">(GMT+0) Atlantic/St_Helena</option>
<option <?php if(preg_match('/Europe\/Belfast/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Europe/Belfast">(GMT+0) Europe/Belfast</option>
<option <?php if(preg_match('/Europe\/Dublin/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Europe/Dublin">(GMT+0) Europe/Dublin</option>
<option <?php if(preg_match('/Europe\/Guernsey/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Europe/Guernsey">(GMT+0) Europe/Guernsey</option>
<option <?php if(preg_match('/Europe\/Isle_of_Man/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Europe/Isle_of_Man">(GMT+0) Europe/Isle_of_Man</option>
<option <?php if(preg_match('/Europe\/Jersey/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Europe/Jersey">(GMT+0) Europe/Jersey</option>
<option <?php if(preg_match('/Europe\/Lisbon/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Europe/Lisbon">(GMT+0) Europe/Lisbon</option>
<option <?php if(preg_match('/Europe\/London/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+0) Europe/London">(GMT+0) Europe/London</option>
<option <?php if(preg_match('/Africa\/Algiers/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Algiers">(GMT+1) Africa/Algiers</option>
<option <?php if(preg_match('/Africa\/Bangui/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Bangui">(GMT+1) Africa/Bangui</option>
<option <?php if(preg_match('/Africa\/Brazzaville/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Brazzaville">(GMT+1) Africa/Brazzaville</option>
<option <?php if(preg_match('/Africa\/Ceuta/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Ceuta">(GMT+1) Africa/Ceuta</option>
<option <?php if(preg_match('/Africa\/Douala/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Douala">(GMT+1) Africa/Douala</option>
<option <?php if(preg_match('/Africa\/Kinshasa/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Kinshasa">(GMT+1) Africa/Kinshasa</option>
<option <?php if(preg_match('/Africa\/Lagos/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Lagos">(GMT+1) Africa/Lagos</option>
<option <?php if(preg_match('/Africa\/Libreville/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Libreville">(GMT+1) Africa/Libreville</option>
<option <?php if(preg_match('/Africa\/Luanda/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Luanda">(GMT+1) Africa/Luanda</option>
<option <?php if(preg_match('/Africa\/Malabo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Malabo">(GMT+1) Africa/Malabo</option>
<option <?php if(preg_match('/Africa\/Ndjamena/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Ndjamena">(GMT+1) Africa/Ndjamena</option>
<option <?php if(preg_match('/Africa\/Niamey/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Niamey">(GMT+1) Africa/Niamey</option>
<option <?php if(preg_match('/Africa\/Porto-Novo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Porto-Novo">(GMT+1) Africa/Porto-Novo</option>
<option <?php if(preg_match('/Africa\/Tunis/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Tunis">(GMT+1) Africa/Tunis</option>
<option <?php if(preg_match('/Africa\/Windhoek/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Africa/Windhoek">(GMT+1) Africa/Windhoek</option>
<option <?php if(preg_match('/Atlantic\/Jan_Mayen/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Atlantic/Jan_Mayen">(GMT+1) Atlantic/Jan_Mayen</option>
<option <?php if(preg_match('/Europe\/Amsterdam/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Amsterdam">(GMT+1) Europe/Amsterdam</option>
<option <?php if(preg_match('/Europe\/Andorra/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Andorra">(GMT+1) Europe/Andorra</option>
<option <?php if(preg_match('/Europe\/Belgrade/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Belgrade">(GMT+1) Europe/Belgrade</option>
<option <?php if(preg_match('/Europe\/Berlin/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Berlin">(GMT+1) Europe/Berlin</option>
<option <?php if(preg_match('/Europe\/Bratislava/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Bratislava">(GMT+1) Europe/Bratislava</option>
<option <?php if(preg_match('/Europe\/Brussels/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Brussels">(GMT+1) Europe/Brussels</option>
<option <?php if(preg_match('/Europe\/Budapest/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Budapest">(GMT+1) Europe/Budapest</option>
<option <?php if(preg_match('/Europe\/Copenhagen/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Copenhagen">(GMT+1) Europe/Copenhagen</option>
<option <?php if(preg_match('/Europe\/Gibraltar/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Gibraltar">(GMT+1) Europe/Gibraltar</option>
<option <?php if(preg_match('/Europe\/Ljubljana/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Ljubljana">(GMT+1) Europe/Ljubljana</option>
<option <?php if(preg_match('/Europe\/Luxembourg/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Luxembourg">(GMT+1) Europe/Luxembourg</option>
<option <?php if(preg_match('/Europe\/Madrid/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Madrid">(GMT+1) Europe/Madrid</option>
<option <?php if(preg_match('/Europe\/Malta/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Malta">(GMT+1) Europe/Malta</option>
<option <?php if(preg_match('/Europe\/Monaco/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Monaco">(GMT+1) Europe/Monaco</option>
<option <?php if(preg_match('/Europe\/Oslo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Oslo">(GMT+1) Europe/Oslo</option>
<option <?php if(preg_match('/Europe\/Paris/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Paris">(GMT+1) Europe/Paris</option>
<option <?php if(preg_match('/Europe\/Podgorica/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Podgorica">(GMT+1) Europe/Podgorica</option>
<option <?php if(preg_match('/Europe\/Prague/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Prague">(GMT+1) Europe/Prague</option>
<option <?php if(preg_match('/Europe\/Rome/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Rome">(GMT+1) Europe/Rome</option>
<option <?php if(preg_match('/Europe\/San_Marino/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/San_Marino">(GMT+1) Europe/San_Marino</option>
<option <?php if(preg_match('/Europe\/Sarajevo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Sarajevo">(GMT+1) Europe/Sarajevo</option>
<option <?php if(preg_match('/Europe\/Skopje/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Skopje">(GMT+1) Europe/Skopje</option>
<option <?php if(preg_match('/Europe\/Stockholm/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Stockholm">(GMT+1) Europe/Stockholm</option>
<option <?php if(preg_match('/Europe\/Tirane/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Tirane">(GMT+1) Europe/Tirane</option>
<option <?php if(preg_match('/Europe\/Vaduz/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Vaduz">(GMT+1) Europe/Vaduz</option>
<option <?php if(preg_match('/Europe\/Vatican/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Vatican">(GMT+1) Europe/Vatican</option>
<option <?php if(preg_match('/Europe\/Vienna/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Vienna">(GMT+1) Europe/Vienna</option>
<option <?php if(preg_match('/Europe\/Warsaw/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Warsaw">(GMT+1) Europe/Warsaw</option>
<option <?php if(preg_match('/Europe\/Zagreb/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Zagreb">(GMT+1) Europe/Zagreb</option>
<option <?php if(preg_match('/Europe\/Zurich/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+1) Europe/Zurich">(GMT+1) Europe/Zurich</option>
<option <?php if(preg_match('/Africa\/Blantyre/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Blantyre">(GMT+2) Africa/Blantyre</option>
<option <?php if(preg_match('/Africa\/Bujumbura/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Bujumbura">(GMT+2) Africa/Bujumbura</option>
<option <?php if(preg_match('/Africa\/Cairo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Cairo">(GMT+2) Africa/Cairo</option>
<option <?php if(preg_match('/Africa\/Gaborone/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Gaborone">(GMT+2) Africa/Gaborone</option>
<option <?php if(preg_match('/Africa\/Harare/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Harare">(GMT+2) Africa/Harare</option>
<option <?php if(preg_match('/Africa\/Johannesburg/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Johannesburg">(GMT+2) Africa/Johannesburg</option>
<option <?php if(preg_match('/Africa\/Kigali/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Kigali">(GMT+2) Africa/Kigali</option>
<option <?php if(preg_match('/Africa\/Lubumbashi/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Lubumbashi">(GMT+2) Africa/Lubumbashi</option>
<option <?php if(preg_match('/Africa\/Lusaka/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Lusaka">(GMT+2) Africa/Lusaka</option>
<option <?php if(preg_match('/Africa\/Maputo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Maputo">(GMT+2) Africa/Maputo</option>
<option <?php if(preg_match('/Africa\/Maseru/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Maseru">(GMT+2) Africa/Maseru</option>
<option <?php if(preg_match('/Africa\/Mbabane/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Mbabane">(GMT+2) Africa/Mbabane</option>
<option <?php if(preg_match('/Africa\/Tripoli/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Africa/Tripoli">(GMT+2) Africa/Tripoli</option>
<option <?php if(preg_match('/Asia\/Amman/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Asia/Amman">(GMT+2) Asia/Amman</option>
<option <?php if(preg_match('/Asia\/Beirut/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Asia/Beirut">(GMT+2) Asia/Beirut</option>
<option <?php if(preg_match('/Asia\/Damascus/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Asia/Damascus">(GMT+2) Asia/Damascus</option>
<option <?php if(preg_match('/Asia\/Gaza/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Asia/Gaza">(GMT+2) Asia/Gaza</option>
<option <?php if(preg_match('/Asia\/Istanbul/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Asia/Istanbul">(GMT+2) Asia/Istanbul</option>
<option <?php if(preg_match('/Asia\/Jerusalem/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Asia/Jerusalem">(GMT+2) Asia/Jerusalem</option>
<option <?php if(preg_match('/Asia\/Nicosia/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Asia/Nicosia">(GMT+2) Asia/Nicosia</option>
<option <?php if(preg_match('/Asia\/Tel_Aviv/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Asia/Tel_Aviv">(GMT+2) Asia/Tel_Aviv</option>
<option <?php if(preg_match('/Europe\/Athens/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Athens">(GMT+2) Europe/Athens</option>
<option <?php if(preg_match('/Europe\/Bucharest/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Bucharest">(GMT+2) Europe/Bucharest</option>
<option <?php if(preg_match('/Europe\/Chisinau/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Chisinau">(GMT+2) Europe/Chisinau</option>
<option <?php if(preg_match('/Europe\/Helsinki/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Helsinki">(GMT+2) Europe/Helsinki</option>
<option <?php if(preg_match('/Europe\/Istanbul/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Istanbul">(GMT+2) Europe/Istanbul</option>
<option <?php if(preg_match('/Europe\/Kaliningrad/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Kaliningrad">(GMT+2) Europe/Kaliningrad</option>
<option <?php if(preg_match('/Europe\/Kiev/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Kiev">(GMT+2) Europe/Kiev</option>
<option <?php if(preg_match('/Europe\/Mariehamn/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Mariehamn">(GMT+2) Europe/Mariehamn</option>
<option <?php if(preg_match('/Europe\/Minsk/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Minsk">(GMT+2) Europe/Minsk</option>
<option <?php if(preg_match('/Europe\/Nicosia/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Nicosia">(GMT+2) Europe/Nicosia</option>
<option <?php if(preg_match('/Europe\/Riga/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Riga">(GMT+2) Europe/Riga</option>
<option <?php if(preg_match('/Europe\/Simferopol/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Simferopol">(GMT+2) Europe/Simferopol</option>
<option <?php if(preg_match('/Europe\/Sofia/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Sofia">(GMT+2) Europe/Sofia</option>
<option <?php if(preg_match('/Europe\/Tallinn/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Tallinn">(GMT+2) Europe/Tallinn</option>
<option <?php if(preg_match('/Europe\/Tiraspol/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Tiraspol">(GMT+2) Europe/Tiraspol</option>
<option <?php if(preg_match('/Europe\/Uzhgorod/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Uzhgorod">(GMT+2) Europe/Uzhgorod</option>
<option <?php if(preg_match('/Europe\/Vilnius/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Vilnius">(GMT+2) Europe/Vilnius</option>
<option <?php if(preg_match('/Europe\/Zaporozhye/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+2) Europe/Zaporozhye">(GMT+2) Europe/Zaporozhye</option>
<option <?php if(preg_match('/Africa\/Addis_Ababa/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Africa/Addis_Ababa">(GMT+3) Africa/Addis_Ababa</option>
<option <?php if(preg_match('/Africa\/Asmara/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Africa/Asmara">(GMT+3) Africa/Asmara</option>
<option <?php if(preg_match('/Africa\/Asmera/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Africa/Asmera">(GMT+3) Africa/Asmera</option>
<option <?php if(preg_match('/Africa\/Dar_es_Salaam/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Africa/Dar_es_Salaam">(GMT+3) Africa/Dar_es_Salaam</option>
<option <?php if(preg_match('/Africa\/Djibouti/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Africa/Djibouti">(GMT+3) Africa/Djibouti</option>
<option <?php if(preg_match('/Africa\/Kampala/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Africa/Kampala">(GMT+3) Africa/Kampala</option>
<option <?php if(preg_match('/Africa\/Khartoum/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Africa/Khartoum">(GMT+3) Africa/Khartoum</option>
<option <?php if(preg_match('/Africa\/Mogadishu/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Africa/Mogadishu">(GMT+3) Africa/Mogadishu</option>
<option <?php if(preg_match('/Africa\/Nairobi/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Africa/Nairobi">(GMT+3) Africa/Nairobi</option>
<option <?php if(preg_match('/Antarctica\/Syowa/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Antarctica/Syowa">(GMT+3) Antarctica/Syowa</option>
<option <?php if(preg_match('/Asia\/Aden/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Asia/Aden">(GMT+3) Asia/Aden</option>
<option <?php if(preg_match('/Asia\/Baghdad/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Asia/Baghdad">(GMT+3) Asia/Baghdad</option>
<option <?php if(preg_match('/Asia\/Bahrain/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Asia/Bahrain">(GMT+3) Asia/Bahrain</option>
<option <?php if(preg_match('/Asia\/Kuwait/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Asia/Kuwait">(GMT+3) Asia/Kuwait</option>
<option <?php if(preg_match('/Asia\/Qatar/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Asia/Qatar">(GMT+3) Asia/Qatar</option>
<option <?php if(preg_match('/Asia\/Riyadh/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Asia/Riyadh">(GMT+3) Asia/Riyadh</option>
<option <?php if(preg_match('/Europe\/Moscow/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Europe/Moscow">(GMT+3) Europe/Moscow</option>
<option <?php if(preg_match('/Europe\/Volgograd/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Europe/Volgograd">(GMT+3) Europe/Volgograd</option>
<option <?php if(preg_match('/Indian\/Antananarivo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Indian/Antananarivo">(GMT+3) Indian/Antananarivo</option>
<option <?php if(preg_match('/Indian\/Comoro/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Indian/Comoro">(GMT+3) Indian/Comoro</option>
<option <?php if(preg_match('/Indian\/Mayotte/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3) Indian/Mayotte">(GMT+3) Indian/Mayotte</option>
<option <?php if(preg_match('/Asia\/Riyadh87/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3.11777777778) Asia/Riyadh87">(GMT+3.11777777778) Asia/Riyadh87</option>
<option <?php if(preg_match('/Asia\/Riyadh88/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3.11777777778) Asia/Riyadh88">(GMT+3.11777777778) Asia/Riyadh88</option>
<option <?php if(preg_match('/Asia\/Riyadh89/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3.11777777778) Asia/Riyadh89">(GMT+3.11777777778) Asia/Riyadh89</option>
<option <?php if(preg_match('/Asia\/Tehran/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+3.5) Asia/Tehran">(GMT+3.5) Asia/Tehran</option>
<option <?php if(preg_match('/Asia\/Baku/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+4) Asia/Baku">(GMT+4) Asia/Baku</option>
<option <?php if(preg_match('/Asia\/Dubai/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+4) Asia/Dubai">(GMT+4) Asia/Dubai</option>
<option <?php if(preg_match('/Asia\/Muscat/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+4) Asia/Muscat">(GMT+4) Asia/Muscat</option>
<option <?php if(preg_match('/Asia\/Tbilisi/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+4) Asia/Tbilisi">(GMT+4) Asia/Tbilisi</option>
<option <?php if(preg_match('/Asia\/Yerevan/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+4) Asia/Yerevan">(GMT+4) Asia/Yerevan</option>
<option <?php if(preg_match('/Europe\/Samara/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+4) Europe/Samara">(GMT+4) Europe/Samara</option>
<option <?php if(preg_match('/Indian\/Mahe/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+4) Indian/Mahe">(GMT+4) Indian/Mahe</option>
<option <?php if(preg_match('/Indian\/Mauritius/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+4) Indian/Mauritius">(GMT+4) Indian/Mauritius</option>
<option <?php if(preg_match('/Indian\/Reunion/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+4) Indian/Reunion">(GMT+4) Indian/Reunion</option>
<option <?php if(preg_match('/Asia\/Kabul/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+4.5) Asia/Kabul">(GMT+4.5) Asia/Kabul</option>
<option <?php if(preg_match('/Asia\/Aqtau/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Asia/Aqtau">(GMT+5) Asia/Aqtau</option>
<option <?php if(preg_match('/Asia\/Aqtobe/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Asia/Aqtobe">(GMT+5) Asia/Aqtobe</option>
<option <?php if(preg_match('/Asia\/Ashgabat/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Asia/Ashgabat">(GMT+5) Asia/Ashgabat</option>
<option <?php if(preg_match('/Asia\/Ashkhabad/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Asia/Ashkhabad">(GMT+5) Asia/Ashkhabad</option>
<option <?php if(preg_match('/Asia\/Dushanbe/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Asia/Dushanbe">(GMT+5) Asia/Dushanbe</option>
<option <?php if(preg_match('/Asia\/Karachi/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Asia/Karachi">(GMT+5) Asia/Karachi</option>
<option <?php if(preg_match('/Asia\/Oral/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Asia/Oral">(GMT+5) Asia/Oral</option>
<option <?php if(preg_match('/Asia\/Samarkand/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Asia/Samarkand">(GMT+5) Asia/Samarkand</option>
<option <?php if(preg_match('/Asia\/Tashkent/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Asia/Tashkent">(GMT+5) Asia/Tashkent</option>
<option <?php if(preg_match('/Asia\/Yekaterinburg/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Asia/Yekaterinburg">(GMT+5) Asia/Yekaterinburg</option>
<option <?php if(preg_match('/Indian\/Kerguelen/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Indian/Kerguelen">(GMT+5) Indian/Kerguelen</option>
<option <?php if(preg_match('/Indian\/Maldives/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5) Indian/Maldives">(GMT+5) Indian/Maldives</option>
<option <?php if(preg_match('/Asia\/Calcutta/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5.5) Asia/Calcutta">(GMT+5.5) Asia/Calcutta</option>
<option <?php if(preg_match('/Asia\/Colombo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5.5) Asia/Colombo">(GMT+5.5) Asia/Colombo</option>
<option <?php if(preg_match('/Asia\/Kolkata/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5.5) Asia/Kolkata">(GMT+5.5) Asia/Kolkata</option>
<option <?php if(preg_match('/Asia\/Kathmandu/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5.75) Asia/Kathmandu">(GMT+5.75) Asia/Kathmandu</option>
<option <?php if(preg_match('/Asia\/Katmandu/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+5.75) Asia/Katmandu">(GMT+5.75) Asia/Katmandu</option>
<option <?php if(preg_match('/Antarctica\/Mawson/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Antarctica/Mawson">(GMT+6) Antarctica/Mawson</option>
<option <?php if(preg_match('/Antarctica\/Vostok/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Antarctica/Vostok">(GMT+6) Antarctica/Vostok</option>
<option <?php if(preg_match('/Asia\/Almaty/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Asia/Almaty">(GMT+6) Asia/Almaty</option>
<option <?php if(preg_match('/Asia\/Bishkek/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Asia/Bishkek">(GMT+6) Asia/Bishkek</option>
<option <?php if(preg_match('/Asia\/Dacca/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Asia/Dacca">(GMT+6) Asia/Dacca</option>
<option <?php if(preg_match('/Asia\/Dhaka/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Asia/Dhaka">(GMT+6) Asia/Dhaka</option>
<option <?php if(preg_match('/Asia\/Novosibirsk/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Asia/Novosibirsk">(GMT+6) Asia/Novosibirsk</option>
<option <?php if(preg_match('/Asia\/Omsk/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Asia/Omsk">(GMT+6) Asia/Omsk</option>
<option <?php if(preg_match('/Asia\/Qyzylorda/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Asia/Qyzylorda">(GMT+6) Asia/Qyzylorda</option>
<option <?php if(preg_match('/Asia\/Thimbu/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Asia/Thimbu">(GMT+6) Asia/Thimbu</option>
<option <?php if(preg_match('/Asia\/Thimphu/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Asia/Thimphu">(GMT+6) Asia/Thimphu</option>
<option <?php if(preg_match('/Indian\/Chagos/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6) Indian/Chagos">(GMT+6) Indian/Chagos</option>
<option <?php if(preg_match('/Asia\/Rangoon/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6.5) Asia/Rangoon">(GMT+6.5) Asia/Rangoon</option>
<option <?php if(preg_match('/Indian\/Cocos/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+6.5) Indian/Cocos">(GMT+6.5) Indian/Cocos</option>
<option <?php if(preg_match('/Antarctica\/Davis/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Antarctica/Davis">(GMT+7) Antarctica/Davis</option>
<option <?php if(preg_match('/Asia\/Bangkok/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Asia/Bangkok">(GMT+7) Asia/Bangkok</option>
<option <?php if(preg_match('/Asia\/Ho_Chi_Minh/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Asia/Ho_Chi_Minh">(GMT+7) Asia/Ho_Chi_Minh</option>
<option <?php if(preg_match('/Asia\/Hovd/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Asia/Hovd">(GMT+7) Asia/Hovd</option>
<option <?php if(preg_match('/Asia\/Jakarta/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Asia/Jakarta">(GMT+7) Asia/Jakarta</option>
<option <?php if(preg_match('/Asia\/Krasnoyarsk/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Asia/Krasnoyarsk">(GMT+7) Asia/Krasnoyarsk</option>
<option <?php if(preg_match('/Asia\/Phnom_Penh/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Asia/Phnom_Penh">(GMT+7) Asia/Phnom_Penh</option>
<option <?php if(preg_match('/Asia\/Pontianak/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Asia/Pontianak">(GMT+7) Asia/Pontianak</option>
<option <?php if(preg_match('/Asia\/Saigon/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Asia/Saigon">(GMT+7) Asia/Saigon</option>
<option <?php if(preg_match('/Asia\/Vientiane/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Asia/Vientiane">(GMT+7) Asia/Vientiane</option>
<option <?php if(preg_match('/Indian\/Christmas/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+7) Indian/Christmas">(GMT+7) Indian/Christmas</option>
<option <?php if(preg_match('/Antarctica\/Casey/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Antarctica/Casey">(GMT+8) Antarctica/Casey</option>
<option <?php if(preg_match('/Asia\/Brunei/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Brunei">(GMT+8) Asia/Brunei</option>
<option <?php if(preg_match('/Asia\/Choibalsan/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Choibalsan">(GMT+8) Asia/Choibalsan</option>
<option <?php if(preg_match('/Asia\/Chongqing/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Chongqing">(GMT+8) Asia/Chongqing</option>
<option <?php if(preg_match('/Asia\/Chungking/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Chungking">(GMT+8) Asia/Chungking</option>
<option <?php if(preg_match('/Asia\/Harbin/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Harbin">(GMT+8) Asia/Harbin</option>
<option <?php if(preg_match('/Asia\/Hong_Kong/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Hong_Kong">(GMT+8) Asia/Hong_Kong</option>
<option <?php if(preg_match('/Asia\/Irkutsk/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Irkutsk">(GMT+8) Asia/Irkutsk</option>
<option <?php if(preg_match('/Asia\/Kashgar/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Kashgar">(GMT+8) Asia/Kashgar</option>
<option <?php if(preg_match('/Asia\/Kuala_Lumpur/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Kuala_Lumpur">(GMT+8) Asia/Kuala_Lumpur</option>
<option <?php if(preg_match('/Asia\/Kuching/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Kuching">(GMT+8) Asia/Kuching</option>
<option <?php if(preg_match('/Asia\/Macao/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Macao">(GMT+8) Asia/Macao</option>
<option <?php if(preg_match('/Asia\/Macau/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Macau">(GMT+8) Asia/Macau</option>
<option <?php if(preg_match('/Asia\/Makassar/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Makassar">(GMT+8) Asia/Makassar</option>
<option <?php if(preg_match('/Asia\/Manila/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Manila">(GMT+8) Asia/Manila</option>
<option <?php if(preg_match('/Asia\/Shanghai/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Shanghai">(GMT+8) Asia/Shanghai</option>
<option <?php if(preg_match('/Asia\/Singapore/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Singapore">(GMT+8) Asia/Singapore</option>
<option <?php if(preg_match('/Asia\/Taipei/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Taipei">(GMT+8) Asia/Taipei</option>
<option <?php if(preg_match('/Asia\/Ujung_Pandang/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Ujung_Pandang">(GMT+8) Asia/Ujung_Pandang</option>
<option <?php if(preg_match('/Asia\/Ulaanbaatar/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Ulaanbaatar">(GMT+8) Asia/Ulaanbaatar</option>
<option <?php if(preg_match('/Asia\/Ulan_Bator/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Ulan_Bator">(GMT+8) Asia/Ulan_Bator</option>
<option <?php if(preg_match('/Asia\/Urumqi/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Asia/Urumqi">(GMT+8) Asia/Urumqi</option>
<option <?php if(preg_match('/Australia\/Perth/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Australia/Perth">(GMT+8) Australia/Perth</option>
<option <?php if(preg_match('/Australia\/West/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8) Australia/West">(GMT+8) Australia/West</option>
<option <?php if(preg_match('/Australia\/Eucla/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+8.75) Australia/Eucla">(GMT+8.75) Australia/Eucla</option>
<option <?php if(preg_match('/Asia\/Dili/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9) Asia/Dili">(GMT+9) Asia/Dili</option>
<option <?php if(preg_match('/Asia\/Jayapura/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9) Asia/Jayapura">(GMT+9) Asia/Jayapura</option>
<option <?php if(preg_match('/Asia\/Pyongyang/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9) Asia/Pyongyang">(GMT+9) Asia/Pyongyang</option>
<option <?php if(preg_match('/Asia\/Seoul/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9) Asia/Seoul">(GMT+9) Asia/Seoul</option>
<option <?php if(preg_match('/Asia\/Tokyo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9) Asia/Tokyo">(GMT+9) Asia/Tokyo</option>
<option <?php if(preg_match('/Asia\/Yakutsk/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9) Asia/Yakutsk">(GMT+9) Asia/Yakutsk</option>
<option <?php if(preg_match('/Pacific\/Palau/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9) Pacific/Palau">(GMT+9) Pacific/Palau</option>
<option <?php if(preg_match('/Australia\/Adelaide/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9.5) Australia/Adelaide">(GMT+9.5) Australia/Adelaide</option>
<option <?php if(preg_match('/Australia\/Broken_Hill/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9.5) Australia/Broken_Hill">(GMT+9.5) Australia/Broken_Hill</option>
<option <?php if(preg_match('/Australia\/Darwin/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9.5) Australia/Darwin">(GMT+9.5) Australia/Darwin</option>
<option <?php if(preg_match('/Australia\/North/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9.5) Australia/North">(GMT+9.5) Australia/North</option>
<option <?php if(preg_match('/Australia\/South/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9.5) Australia/South">(GMT+9.5) Australia/South</option>
<option <?php if(preg_match('/Australia\/Yancowinna/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+9.5) Australia/Yancowinna">(GMT+9.5) Australia/Yancowinna</option>
<option <?php if(preg_match('/Antarctica\/DumontDUrville/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Antarctica/DumontDUrville">(GMT+10) Antarctica/DumontDUrville</option>
<option <?php if(preg_match('/Asia\/Sakhalin/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Asia/Sakhalin">(GMT+10) Asia/Sakhalin</option>
<option <?php if(preg_match('/Asia\/Vladivostok/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Asia/Vladivostok">(GMT+10) Asia/Vladivostok</option>
<option <?php if(preg_match('/Australia\/ACT/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/ACT">(GMT+10) Australia/ACT</option>
<option <?php if(preg_match('/Australia\/Brisbane/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Brisbane">(GMT+10) Australia/Brisbane</option>
<option <?php if(preg_match('/Australia\/Canberra/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Canberra">(GMT+10) Australia/Canberra</option>
<option <?php if(preg_match('/Australia\/Currie/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Currie">(GMT+10) Australia/Currie</option>
<option <?php if(preg_match('/Australia\/Hobart/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Hobart">(GMT+10) Australia/Hobart</option>
<option <?php if(preg_match('/Australia\/LHI/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/LHI">(GMT+10) Australia/LHI</option>
<option <?php if(preg_match('/Australia\/Lindeman/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Lindeman">(GMT+10) Australia/Lindeman</option>
<option <?php if(preg_match('/Australia\/Lord_Howe/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Lord_Howe">(GMT+10) Australia/Lord_Howe</option>
<option <?php if(preg_match('/Australia\/Melbourne/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Melbourne">(GMT+10) Australia/Melbourne</option>
<option <?php if(preg_match('/Australia\/NSW/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/NSW">(GMT+10) Australia/NSW</option>
<option <?php if(preg_match('/Australia\/Queensland/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Queensland">(GMT+10) Australia/Queensland</option>
<option <?php if(preg_match('/Australia\/Sydney/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Sydney">(GMT+10) Australia/Sydney</option>
<option <?php if(preg_match('/Australia\/Tasmania/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Tasmania">(GMT+10) Australia/Tasmania</option>
<option <?php if(preg_match('/Australia\/Victoria/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Australia/Victoria">(GMT+10) Australia/Victoria</option>
<option <?php if(preg_match('/Pacific\/Guam/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Pacific/Guam">(GMT+10) Pacific/Guam</option>
<option <?php if(preg_match('/Pacific\/Port_Moresby/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Pacific/Port_Moresby">(GMT+10) Pacific/Port_Moresby</option>
<option <?php if(preg_match('/Pacific\/Saipan/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Pacific/Saipan">(GMT+10) Pacific/Saipan</option>
<option <?php if(preg_match('/Pacific\/Truk/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Pacific/Truk">(GMT+10) Pacific/Truk</option>
<option <?php if(preg_match('/Pacific\/Yap/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+10) Pacific/Yap">(GMT+10) Pacific/Yap</option>
<option <?php if(preg_match('/Asia\/Magadan/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+11) Asia/Magadan">(GMT+11) Asia/Magadan</option>
<option <?php if(preg_match('/Pacific\/Efate/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+11) Pacific/Efate">(GMT+11) Pacific/Efate</option>
<option <?php if(preg_match('/Pacific\/Guadalcanal/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+11) Pacific/Guadalcanal">(GMT+11) Pacific/Guadalcanal</option>
<option <?php if(preg_match('/Pacific\/Kosrae/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+11) Pacific/Kosrae">(GMT+11) Pacific/Kosrae</option>
<option <?php if(preg_match('/Pacific\/Noumea/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+11) Pacific/Noumea">(GMT+11) Pacific/Noumea</option>
<option <?php if(preg_match('/Pacific\/Ponape/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+11) Pacific/Ponape">(GMT+11) Pacific/Ponape</option>
<option <?php if(preg_match('/Pacific\/Norfolk/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+11.5) Pacific/Norfolk">(GMT+11.5) Pacific/Norfolk</option>
<option <?php if(preg_match('/Antarctica\/McMurdo/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Antarctica/McMurdo">(GMT+12) Antarctica/McMurdo</option>
<option <?php if(preg_match('/Antarctica\/South_Pole/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Antarctica/South_Pole">(GMT+12) Antarctica/South_Pole</option>
<option <?php if(preg_match('/Asia\/Anadyr/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Asia/Anadyr">(GMT+12) Asia/Anadyr</option>
<option <?php if(preg_match('/Asia\/Kamchatka/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Asia/Kamchatka">(GMT+12) Asia/Kamchatka</option>
<option <?php if(preg_match('/Pacific\/Auckland/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Pacific/Auckland">(GMT+12) Pacific/Auckland</option>
<option <?php if(preg_match('/Pacific\/Fiji/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Pacific/Fiji">(GMT+12) Pacific/Fiji</option>
<option <?php if(preg_match('/Pacific\/Funafuti/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Pacific/Funafuti">(GMT+12) Pacific/Funafuti</option>
<option <?php if(preg_match('/Pacific\/Kwajalein/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Pacific/Kwajalein">(GMT+12) Pacific/Kwajalein</option>
<option <?php if(preg_match('/Pacific\/Majuro/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Pacific/Majuro">(GMT+12) Pacific/Majuro</option>
<option <?php if(preg_match('/Pacific\/Nauru/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Pacific/Nauru">(GMT+12) Pacific/Nauru</option>
<option <?php if(preg_match('/Pacific\/Tarawa/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Pacific/Tarawa">(GMT+12) Pacific/Tarawa</option>
<option <?php if(preg_match('/Pacific\/Wake/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Pacific/Wake">(GMT+12) Pacific/Wake</option>
<option <?php if(preg_match('/Pacific\/Wallis/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12) Pacific/Wallis">(GMT+12) Pacific/Wallis</option>
<option <?php if(preg_match('/Pacific\/Chatham/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+12.75) Pacific/Chatham">(GMT+12.75) Pacific/Chatham</option>
<option <?php if(preg_match('/Pacific\/Enderbury/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+13) Pacific/Enderbury">(GMT+13) Pacific/Enderbury</option>
<option <?php if(preg_match('/Pacific\/Tongatapu/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+13) Pacific/Tongatapu">(GMT+13) Pacific/Tongatapu</option>
<option <?php if(preg_match('/Pacific\/Kiritimati/',get_option("em_timezone"))=='1'){ ?> Selected="selected" <?php } ?> value="(GMT+14) Pacific/Kiritimati">(GMT+14) Pacific/Kiritimati</option>

</select></div> 
	</div> 
	
	<?php if($isedit){ ?>
		<div class="row"> 
			<div class="left">&nbsp;</div>  
			<div class="right"><input type="submit" name="submit" id="eddevent" value="Edit Event"/></div> 
		</div> 
		<input type="hidden" name="eddem" id="eddem" value="1" />
		<input type="hidden" name="emidd" id="emidd" value="<?php echo $editrow[0]->em_id; ?>"/>
	<?php }else {?>  
		<div class="row"> 
			<div class="left"><strong>Save it as:</strong></div>  
			<div class="right">A Popup?<input type="radio" name="saveitaspopup" id="saveitas" value="popup" selected/>OR A Post?<input type="radio" name="saveitaspopup" id="saveitas" value="posted"/></div> 
		</div>
	<div class="row"> 
		<div class="left">&nbsp;</div>  
		<div class="right"><input type="submit" name="submit" id="adddevent" value="Add Event"/></div> 
	</div> 
	<input type="hidden" name="addem" id="addem" value="1" />
	<?php } ?>
	<input type="hidden" name="nonce-eventify" value="<?php echo wp_create_nonce('eventify-nonce'); ?>" />
	
</form>
<?php
/* Showing all events entered in the database to allow deletion */

?>
<br/><br/>
<h3>Bulk Upload Events (See <a href="http://designerfoo.com/wordpress-plugin-eventify-simple-events-management">How to Video</a> )</h3>
<form action="" enctype="multipart/form-data" method="POST">
<input name="csvfile" type="file" />
<input type="hidden" name="uploadems" id="uploadems" value="1" />
<input type="hidden" name="nonce-eventify" value="<?php echo wp_create_nonce('eventify-nonce'); ?>" />
<input type="submit" value="Upload Bulk Events" name="postedfpc"/>

</form> 

<h3>Back up Events? See HowTo video to Backup/Restore events</h3>

<form action="" method="post">
	<div class="row">
		<div class="left">&nbsp;</div>
		<div class="right"> <input type="submit" value="Backup all Events" name="submit" id="backupallevents"></div>
	</div>
	<input type="hidden" name="nonce-eventify" value="<?php echo wp_create_nonce('eventify-nonce'); ?>" />
		<input type="hidden" name="backemall" id="backemall" value="1" />
</form>
<br/><br/>
<h3>Events added (Events already present in the database)</h3>
<table cellpadding="1" cellspacing="1" border="0"><tr>

 
		<th  style="background:#464646;color:#fff;text-align:center;padding:5px;"><strong>Sr No.</strong></th>
		<th  style="background:#464646;color:#fff;text-align:center;padding:5px;"><strong>Event Title</strong></th>  
		<th  style="background:#464646;color:#fff;text-align:center;padding:5px;"><strong>Event Date(mm/dd/yyyy)</strong></th> 
		<th  style="background:#464646;color:#fff;text-align:center;padding:5px;"><strong>Event Time</strong></th>  
		<th  style="background:#464646;color:#fff;text-align:center;padding:5px;"><strong>Event Venue</strong></th>  
		<th  style="background:#464646;color:#fff;text-align:center;padding:5px;"><strong>Event Details</strong></th>  
		<th  style="background:#464646;color:#fff;padding:5px;"><strong>Event Timzone</strong></th>
		<th  style="background:#464646;color:#fff;padding:5px;"><strong>Event Saved As</strong></th>
		<th  style="background:#464646;color:#fff;padding:5px;"><strong>Select to delete</strong></th>  
		<th style="background:#464646;color:#fff;padding:5px;"><strong>Edit Event</strong></th>
</tr>
<?php
		$table_name = $wpdb->prefix."em_main";
		 	$sqlqry = "Select * from ".$table_name;
		 	$results_list = $wpdb->get_results($sqlqry);
		 	$i_list=1;
		
		 	foreach($results_list as $row_list)
		 	{
				?>	<tr>
				    <td style="text-align:center;padding:5px;"><?php echo $i_list; ?></td>  
					<td style="text-align:center;padding:5px;"><?php echo $row_list->em_title; ?></td>  
					<td style="text-align:center;padding:5px;"><?php echo $row_list->em_date; ?></td> 
					<td style="text-align:center;padding:5px;"><?php echo $row_list->em_time; ?></td>
					<td style="text-align:center;padding:5px;"><?php echo $row_list->em_venue; ?></td>
					<td style="text-align:center;padding:5px;"><?php echo substr($row_list->em_desc,0,12)."..."; ?></td>
					<td style="text-align:center;padding:5px;"><?php echo $row_list->em_timezone; ?></td>
					<td style="text-align:center;padding:5px;"><?php if($row_list->em_savetype>0) { echo 'POST'; } else { Echo 'POPUP'; } ?></td>			
					<td style="text-align:center;padding:5px;"><input type="checkbox" name="delemid[]" id="delemid" value="<?php echo $row_list->em_id; ?>" /></td>
					<td style="text-align:center;padding:5px;"><form method="post" action=""><input type="submit" value="Edit Event" /><input type="hidden" value="<?php echo $row_list->em_id; ?>" name="eem"/><input type="hidden" name="nonce-eventify" value="<?php echo wp_create_nonce('eventify-nonce'); ?>" /></form></td>
					</tr> 
				<?php
			$i_list++;
			}
		$wpdb->print_error();
?></table>
<div class="row">
<div class="left"><div id="deleting"></div></div>  
<div class="right"><input type="button" name="submit" id="deldevent" value="Delete Selected Event(s)" onclick="javascript:postbackdel();"/></div> 
<input type="hidden" id="delnon" name="nonce-eventify" value="<?php echo wp_create_nonce('eventify-nonce'); ?>" />
	<input type="hidden" name="delem" id="delem" value="1" />
</div><br/>
<script language="javascript">
function postbackdel()
{
	jQuery("#deleting").html("<em>Deleting Event(s)<\/em>");
	var url = '<?php echo $_SERVER['REQUEST_URI']; ?>';
	var nonce = jQuery("#delnon").val();
	var delm = jQuery("#delem").val();
	var delid = jQuery("#delemid").val();
	var allVals = [];
     jQuery('#delemid:checked').each(function() {
       allVals.push(jQuery(this).val());
       //alert(jQuery(this).val());
     });

	jQuery.post(url,{nonceeventify: nonce, delem: delm, 'delemid[]': allVals  },function(data){jQuery("#deleting").html("<em>Event Deleted Reloading...<\/em>");location.reload(true);},"text");
	
}
</script>
<br/><br/>
<div style="float:left"> <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHLwYJKoZIhvcNAQcEoIIHIDCCBxwCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYAMLVXUISBgs8zwEMpjwajr8j4EHkAt0XC5mkQGS19rbFJ7+HrGaFUH53zbCYxDgEERj6Osx6HPRbg76lvsIDbXgCsV5/cVoviG6Fc4fNlccwHBmedRXqQaFexXWYqsLMTjHGZR2XDELyVj/Ir/7oexM1mOQ5zaHexAuZkYhVpnyTELMAkGBSsOAwIaBQAwgawGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIBIU1gxCvc5eAgYj6lhT8+HaNApprqvq8WMz24g18OAoKlpa1jEADKGE67cPiXWdpbg564EKaxdk2A0eLs7HZdat0IQ+4Np9FltV53lUrDZ0dGf8iUWEb9UUFwJivSR/WyO617IYFTmnbIWrm7cEgcBCfwb47tHPPlGKsNcyWM+TAEiFKLJ9XYU6mK6zoM1ySQp4XoIIDhzCCA4MwggLsoAMCAQICAQAwDQYJKoZIhvcNAQEFBQAwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMB4XDTA0MDIxMzEwMTMxNVoXDTM1MDIxMzEwMTMxNVowgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDBR07d/ETMS1ycjtkpkvjXZe9k+6CieLuLsPumsJ7QC1odNz3sJiCbs2wC0nLE0uLGaEtXynIgRqIddYCHx88pb5HTXv4SZeuv0Rqq4+axW9PLAAATU8w04qqjaSXgbGLP3NmohqM6bV9kZZwZLR/klDaQGo1u9uDb9lr4Yn+rBQIDAQABo4HuMIHrMB0GA1UdDgQWBBSWn3y7xm8XvVk/UtcKG+wQ1mSUazCBuwYDVR0jBIGzMIGwgBSWn3y7xm8XvVk/UtcKG+wQ1mSUa6GBlKSBkTCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb22CAQAwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOBgQCBXzpWmoBa5e9fo6ujionW1hUhPkOBakTr3YCDjbYfvJEiv/2P+IobhOGJr85+XHhN0v4gUkEDI8r2/rNk1m0GA8HKddvTjyGw/XqXa+LSTlDYkqI8OwR8GEYj4efEtcRpRYBxV8KxAW93YDWzFGvruKnnLbDAF6VR5w/cCMn5hzGCAZowggGWAgEBMIGUMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbQIBADAJBgUrDgMCGgUAoF0wGAYJKoZIhvcNAQkDMQsGCSqGSIb3DQEHATAcBgkqhkiG9w0BCQUxDxcNMDkxMDMwMTYyMTIzWjAjBgkqhkiG9w0BCQQxFgQUU0Bs1GFXXrV03dertBwfNOQf4BMwDQYJKoZIhvcNAQEBBQAEgYCpuVu4RuXvzJPrn1qRi2vwNqcCqiF2I7MA94GSgPRDW4dUBH5sULBV/sPxvBBVWS30t0Q24BkC3hzwQKhuP20/jdmQCDPD7NCRhyHv/CKgvNFCWJiypSfFqjDTGHFRlpMGqR8Kxg/QtMakOW051eNVs3kTjLDvAFo75SmGXAbs2g==-----END PKCS7-----
">
Buy me a beer :)<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" stlye="margin:0;float:left;padding:0;position:relative;" align="left">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form> </div><div style="float:left"> | Need help? <a target="_blank" title="Follow me on twitter" href="http://twitter.com/designerfoo">Follow me on twitter</a><br/><br/><h4><a href="http://feeds.feedburner.com/Designerfoo" target="_blank">Subscribe to the RSS feed</a> or <a href="http://feedburner.google.com/fb/a/mailverify?uri=Designerfoo&loc=en_US" target="_blank">subscribe via Email</a>, to know what other updates/plugins/themes I am releasing</h4></div>
</div>
<?php	} //end of options_panel()


	function admin_menu() {
		$file = __FILE__;
		
		// hack for 1.5
		if (substr($this->wp_version, 0, 3) == '1.5') {
			$file = 'eventify/eventify.php';
		}
		//add_management_page(__('All in One SEO Title', 'all_in_one_seo_pack'), __('All in One SEO', 'all_in_one_seo_pack'), 10, $file, array($this, 'management_panel'));
		$subpage = add_submenu_page('options-general.php', "Eventify", "Eventify", 10, $file, array($this, 'options_panel'));
		
	} //end of admin_menu()
	
	function remove_add_jquery()
	{	global $subpage;
		if(is_admin())
		{
			
				wp_enqueue_script('jquery');
				$url = get_bloginfo('wpurl').'/wp-content/plugins/eventify/js/'; 
	  		//	wp_register_script( 'jquery_no_conflict', $url . 'jquery_no_conflict.js', array( 'jquery' ), '' );     
	  		//	wp_enqueue_script( 'jquery_no_conflict' ); 
	  			wp_register_script('ui.core',$url.'ui.core.js',false,'');
	  			wp_enqueue_script( 'ui.core' );
	  			wp_register_script('ui.datepicker',$url.'ui.datepicker.js',false,'');
	  			wp_enqueue_script( 'ui.datepicker' );
	  			wp_register_script('jquery.timepicker',$url.'jquery.timepicker.js',false,'');
	  			wp_enqueue_script( 'jquery.timepicker' );
				wp_register_script('jquery.validate',$url.'jquery.validate.pack.js',false,'');
	  			wp_enqueue_script('jquery.validate');
				wp_register_script('adminjs',$url.'adminmain.js',false,'');
				wp_enqueue_script('adminjs');
				
			//echo("jquery changed");
		}//end of if(is_admin());
	}//end of remove_add_jquery();
	
	function add_custom_css()
	{
		if(is_admin())
		{
		$url = get_bloginfo('wpurl').'/wp-content/plugins/eventify/css/'; 
		echo '<link type="text/css" rel="stylesheet" href="' .$url. 'eventify.ui.all.css" />' . "\n";
		echo '<link type="text/css" rel="stylesheet" href="' .$url. 'eventify.ui.base.css" />' . "\n";
		echo '<link type="text/css" rel="stylesheet" href="' .$url. 'eventify.ui.core.css" />' . "\n";
		echo '<link type="text/css" rel="stylesheet" href="' .$url. 'eventify.ui.datepicker.css" />' . "\n";
		echo '<link type="text/css" rel="stylesheet" href="' .$url. 'eventify.ui.theme.css" />' . "\n";			
		}//end of if(is_admin());
	}//end of add_custom_css();
	
	//adding jquery, other js and css for calendar, add event widget, validation etc.
	function add_js_css_theme()
	{
		if(!is_admin())
		{
			wp_enqueue_script('jquery'); //loading js for the calendar and date/time input for the user form upfront
			wp_enqueue_script('jquery-ui-core');
			$url = get_bloginfo('wpurl').'/wp-content/plugins/eventify/js/'; 
			wp_register_script('ui.datepicker',$url.'ui.datepicker.js',false,'');
		   	wp_enqueue_script( 'ui.datepicker' );
		   	wp_register_script('jquery.timepicker',$url.'jquery.timepicker.js',false,'');
		   	wp_enqueue_script( 'jquery.timepicker' );
			wp_register_script('jquery.validate',$url.'jquery.validate.pack.js',false,'');
			wp_enqueue_script('jquery.validate');
			wp_register_script('popupscript', WP_PLUGIN_URL . '/eventify/js/eventifypopups.js'); //make the script available to all pages and sidebars using eventify
			
			wp_enqueue_script('popupscript');
			
			$url = get_bloginfo('wpurl').'/wp-content/plugins/eventify/css/'; 
			echo '<link type="text/css" rel="stylesheet" href="' .$url. 'eventify.ui.all.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' .$url. 'eventify.ui.base.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' .$url. 'eventify.ui.core.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' .$url. 'eventify.ui.datepicker.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' .$url. 'eventify.ui.theme.css" />' . "\n";
			$popupcssurl = get_bloginfo('wpurl').'/wp-content/plugins/eventify/css/popup_widget.css'; //keep css for popups
			echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"$popupcssurl\" />";
		}
	} //end of add_js_css_theme()
	
	function add_mask_widget() //add the mask div to the bottom of the theme page for jQuery Modal.
	{
		echo '<div id="boxes">

							<div id="dialog" class="window">
								<a href="#"class="close">Close It</a><br/>
								<div id="eventifydetails">
								</div>
							</div>
					</div>

					<div id="mask"></div>
			<script language="javascript">
					
					function fetchevent(emid)
					{
						var ajaxloader = \'<div class="ajxloader"><img src="'.WP_PLUGIN_URL.'/eventify/css/images/ajx.gif"/></div>\'
						jQuery("#eventifydetails").html(ajaxloader);
						jQuery.post("'. WP_PLUGIN_URL. '/eventify/php/ajax/fetcheventdetails.php",{eventid: emid, npath:"'.WP_CONTENT_DIR.'"},function(data){jQuery("#eventifydetails").html(data);},"html");
					}
					
					
			</script>';
			

	}
	
	//[eventifytag displaytype=Events or Days displayno = 7 events or 7 days]
		function eventifyform_func($atts)
		{
			$eventform=""; //var to return the event form for a page/post;
			$flags = extract(shortcode_atts(array('loggedin'=>'admin','popuponly'=>'0'),$atts));
			//set default values for the atts array
			if(!isset($atts['loggedin']))
			{
				$atts['loggedin']="10";
				
			}
			if(!isset($atts['popuponly']))
			{
				$atts['popuponly']="0";
			}
			
			//set the actual values for the roles [who can add events using the shortcode?]
			
			if($atts['loggedin']=="admin")
			{
				$atts['loggedin']="10";
			}
			elseif($atts['loggedin']=="editor")
			{
				$atts['loggedin']="7";
			}
			elseif($atts['loggedin']=="author")
			{
				$atts['loggedin']="2";
			}
			elseif($atts['loggedin']=="user")
			{
				$atts['loggedin']="1";
			}
			else
			{
				$atts['loggedin']="10";
			}
			
			$eventform='<script type="text/javascript">
				jQuery(function() {
					jQuery("#datepicker_post").datepicker();
						jQuery("#timepick_from_post").timePicker();
						jQuery("#timepick_to_post").timePicker();
					jQuery("#fronteventifyform_post").validate();
				});
				</script>
				<Style>
			label.error{
				color:red;
			}
			div.time-picker {
			  position: absolute;
			  height: 200px;
			  width:4em; /* needed for IE */
			  overflow: auto;
			  background: #fff;
			  border: 1px solid #000;
			  z-index: 99;
			}
			div.time-picker-12hours {
			  width:6em; /* needed for IE */
			}

			div.time-picker ul {
			  list-style-type: none;
			  margin: 0;
			  padding: 0;
			}
			div.time-picker li {
			  padding: 1px;
			  cursor: pointer;
			}
			div.time-picker li.selected {
			  background: #316AC5;
			  color: #fff;
			}
			#em_timezone
			{
				width:130px;
			}
			</style><form action="" method="post" id="fronteventifyform_post">
				<div class="row">  
					<div class="left"><strong>Event Title:</strong></div>  
					<div class="right"><input type="text" size="27" id="em_title" name="em_title" class="required"></div> 
				</div> 
				<div class="row">  
					<div class="left"><strong>Event Date:</strong></div> 
					<div class="right"><input type="text" size="27" id="datepicker_post" name="datepicker_post" READONLY class="required"></div>
				</div>


				<div class="row"> 
					<div class="left"><strong>Time - From</strong></div>  
					<div class="right"><input type="text" size="27" id="timepick_from_post" name="timepick_from_post" READONLY class="required"></div>
				</div> 
				<div class="row">
					<div class="left"><strong>Time - To</strong></div>
					<div class="right"><input type="text" size="27" id="timepick_to_post" name="timepick_to_post" READONLY class="required"></div>
				</div>
				<div class="row">
					<div class="left"><strong>Event Venue:</strong></div>  
					<div class="right"><input type="text" size="27" id="em_venue" name="em_venue" class="required"></div> 
				</div>
				<div class="row"> 
					<div class="left"><strong>Event Details:</strong></div>  
					<div class="right"><textarea id="em_desc" name="em_desc" rows="9" cols="27"></textarea></div> 
				</div> 
				<div class="row"> 
					<div class="left"><strong>Event TimeZone:</strong></div>  
					<div class="right"><select name="em_timezone" id="em_timezone" style="width:227px;"><option '; if(preg_match("/none/",get_option("em_timezone"))=="1"){ $eventform.=' Selected="selected"'; }; $eventform.='value="none" >Select your local timezone....</option>
							<option';  if(preg_match("/Canada\/Atlantic/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="Canada/Atlantic">Canada/Atlantic</option>
														<option';  if(preg_match("/Canada\/Central/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="Canada/Central">Canada/Central</option>
														<option';  if(preg_match("/Canada\/East-Saskatchewan/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="Canada/East-Saskatchewan">Canada/East-Saskatchewan</option>
														<option';  if(preg_match("/Canada\/Eastern/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="Canada/Eastern">Canada/Eastern</option>
														<option';  if(preg_match("/Canada\/Mountain/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="Canada/Mountain">Canada/Mountain</option>
														<option';  if(preg_match("/Canada\/Newfoundland/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="Canada/Newfoundland">Canada/Newfoundland</option>
														<option';  if(preg_match("/Canada\/Pacific/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="Canada/Pacific">Canada/Pacific</option>
														<option';  if(preg_match("/Canada\/Saskatchewan/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="Canada/Saskatchewan">Canada/Saskatchewan</option>
														<option';  if(preg_match("/Canada\/Yukon/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="Canada/Yukon">Canada/Yukon</option>
														<option';  if(preg_match("/US\/Alaska/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Alaska">US/Alaska</option>
														<option';  if(preg_match("/US\/Aleutian/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Aleutian">US/Aleutian</option>
														<option';  if(preg_match("/US\/Arizona/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Arizona">US/Arizona</option>
														<option';  if(preg_match("/US\/Central/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Central">US/Central</option>
														<option';  if(preg_match("/US\/East-Indiana/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/East-Indiana">US/East-Indiana</option>
														<option';  if(preg_match("/US\/Eastern/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Eastern">US/Eastern</option>
														<option';  if(preg_match("/US\/Hawaii/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Hawaii">US/Hawaii</option>
														<option';  if(preg_match("/US\/Indiana-Starke/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Indiana-Starke">US/Indiana-Starke</option>
														<option';  if(preg_match("/US\/Michigan/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Michigan">US/Michigan</option>
														<option';  if(preg_match("/US\/Mountain/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Mountain">US/Mountain</option>
														<option';  if(preg_match("/US\/Pacific/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Pacific">US/Pacific</option>
														<option';  if(preg_match("/US\/Samoa/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="US/Samoa">US/Samoa</option>
														<option';  if(preg_match("/Pacific\/Apia/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-11) Pacific/Apia">(GMT-11) Pacific/Apia</option>
														<option';  if(preg_match("/Pacific\/Midway/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-11) Pacific/Midway">(GMT-11) Pacific/Midway</option>
														<option';  if(preg_match("/Pacific\/Niue/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-11) Pacific/Niue">(GMT-11) Pacific/Niue</option>
														<option';  if(preg_match("/Pacific\/Pago_Pago/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-11) Pacific/Pago_Pago">(GMT-11) Pacific/Pago_Pago</option>
														<option';  if(preg_match("/Pacific\/Samoa/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-11) Pacific/Samoa">(GMT-11) Pacific/Samoa</option>
														<option';  if(preg_match("/America\/Adak/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-10) America/Adak">(GMT-10) America/Adak</option>
														<option';  if(preg_match("/America\/Atka/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-10) America/Atka">(GMT-10) America/Atka</option>
														<option';  if(preg_match("/Pacific\/Fakaofo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-10) Pacific/Fakaofo">(GMT-10) Pacific/Fakaofo</option>
														<option';  if(preg_match("/Pacific\/Honolulu/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-10) Pacific/Honolulu">(GMT-10) Pacific/Honolulu</option>
														<option';  if(preg_match("/Pacific\/Johnston/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-10) Pacific/Johnston">(GMT-10) Pacific/Johnston</option>
														<option';  if(preg_match("/Pacific\/Rarotonga/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-10) Pacific/Rarotonga">(GMT-10) Pacific/Rarotonga</option>
														<option';  if(preg_match("/Pacific\/Tahiti/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-10) Pacific/Tahiti">(GMT-10) Pacific/Tahiti</option>
														<option';  if(preg_match("/Pacific\/Marquesas/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-9.5) Pacific/Marquesas">(GMT-9.5) Pacific/Marquesas</option>
														<option';  if(preg_match("/America\/Anchorage/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-9) America/Anchorage">(GMT-9) America/Anchorage</option>
														<option';  if(preg_match("/America\/Juneau/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-9) America/Juneau">(GMT-9) America/Juneau</option>
														<option';  if(preg_match("/America\/Nome/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-9) America/Nome">(GMT-9) America/Nome</option>
														<option';  if(preg_match("/America\/Yakutat/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-9) America/Yakutat">(GMT-9) America/Yakutat</option>
														<option';  if(preg_match("/Pacific\/Gambier/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-9) Pacific/Gambier">(GMT-9) Pacific/Gambier</option>
														<option';  if(preg_match("/America\/Dawson/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-8) America/Dawson">(GMT-8) America/Dawson</option>
														<option';  if(preg_match("/America\/Ensenada/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-8) America/Ensenada">(GMT-8) America/Ensenada</option>
														<option';  if(preg_match("/America\/Los_Angeles/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-8) America/Los_Angeles">(GMT-8) America/Los_Angeles</option>
														<option';  if(preg_match("/America\/Tijuana/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-8) America/Tijuana">(GMT-8) America/Tijuana</option>
														<option';  if(preg_match("/America\/Vancouver/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-8) America/Vancouver">(GMT-8) America/Vancouver</option>
														<option';  if(preg_match("/America\/Whitehorse/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-8) America/Whitehorse">(GMT-8) America/Whitehorse</option>
														<option';  if(preg_match("/Pacific\/Pitcairn/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-8) Pacific/Pitcairn">(GMT-8) Pacific/Pitcairn</option>
														<option';  if(preg_match("/America\/Boise/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Boise">(GMT-7) America/Boise</option>
														<option';  if(preg_match("/America\/Cambridge_Bay/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Cambridge_Bay">(GMT-7) America/Cambridge_Bay</option>
														<option';  if(preg_match("/America\/Chihuahua/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Chihuahua">(GMT-7) America/Chihuahua</option>
														<option';  if(preg_match("/America\/Dawson_Creek/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Dawson_Creek">(GMT-7) America/Dawson_Creek</option>
														<option';  if(preg_match("/America\/Denver/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Denver">(GMT-7) America/Denver</option>
														<option';  if(preg_match("/America\/Edmonton/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Edmonton">(GMT-7) America/Edmonton</option>
														<option';  if(preg_match("/America\/Hermosillo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Hermosillo">(GMT-7) America/Hermosillo</option>
														<option';  if(preg_match("/America\/Inuvik/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Inuvik">(GMT-7) America/Inuvik</option>
														<option';  if(preg_match("/America\/Mazatlan/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Mazatlan">(GMT-7) America/Mazatlan</option>
														<option';  if(preg_match("/America\/Phoenix/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Phoenix">(GMT-7) America/Phoenix</option>
														<option';  if(preg_match("/America\/Shiprock/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Shiprock">(GMT-7) America/Shiprock</option>
														<option';  if(preg_match("/America\/Yellowknife/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-7) America/Yellowknife">(GMT-7) America/Yellowknife</option>
														<option';  if(preg_match("/America\/Belize/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Belize">(GMT-6) America/Belize</option>
														<option';  if(preg_match("/America\/Cancun/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Cancun">(GMT-6) America/Cancun</option>
														<option';  if(preg_match("/America\/Chicago/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Chicago">(GMT-6) America/Chicago</option>
														<option';  if(preg_match("/America\/Costa_Rica/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Costa_Rica">(GMT-6) America/Costa_Rica</option>
														<option';  if(preg_match("/America\/El_Salvador/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/El_Salvador">(GMT-6) America/El_Salvador</option>
														<option';  if(preg_match("/America\/Guatemala/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Guatemala">(GMT-6) America/Guatemala</option>
														<option';  if(preg_match("/America\/Knox_IN/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Knox_IN">(GMT-6) America/Knox_IN</option>
														<option';  if(preg_match("/America\/Managua/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Managua">(GMT-6) America/Managua</option>
														<option';  if(preg_match("/America\/Menominee/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Menominee">(GMT-6) America/Menominee</option>
														<option';  if(preg_match("/America\/Merida/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Merida">(GMT-6) America/Merida</option>
														<option';  if(preg_match("/America\/Mexico_City/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Mexico_City">(GMT-6) America/Mexico_City</option>
														<option';  if(preg_match("/America\/Monterrey/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Monterrey">(GMT-6) America/Monterrey</option>
														<option';  if(preg_match("/America\/Rainy_River/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Rainy_River">(GMT-6) America/Rainy_River</option>
														<option';  if(preg_match("/America\/Rankin_Inlet/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Rankin_Inlet">(GMT-6) America/Rankin_Inlet</option>
														<option';  if(preg_match("/America\/Regina/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Regina">(GMT-6) America/Regina</option>
														<option';  if(preg_match("/America\/Swift_Current/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Swift_Current">(GMT-6) America/Swift_Current</option>
														<option';  if(preg_match("/America\/Tegucigalpa/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Tegucigalpa">(GMT-6) America/Tegucigalpa</option>
														<option';  if(preg_match("/America\/Winnipeg/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) America/Winnipeg">(GMT-6) America/Winnipeg</option>
														<option';  if(preg_match("/Pacific\/Easter/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) Pacific/Easter">(GMT-6) Pacific/Easter</option>
														<option';  if(preg_match("/Pacific\/Galapagos/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-6) Pacific/Galapagos">(GMT-6) Pacific/Galapagos</option>
														<option';  if(preg_match("/America\/Atikokan/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Atikokan">(GMT-5) America/Atikokan</option>
														<option';  if(preg_match("/America\/Bogota/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Bogota">(GMT-5) America/Bogota</option>
														<option';  if(preg_match("/America\/Cayman/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Cayman">(GMT-5) America/Cayman</option>
														<option';  if(preg_match("/America\/Coral_Harbour/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Coral_Harbour">(GMT-5) America/Coral_Harbour</option>
														<option';  if(preg_match("/America\/Detroit/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Detroit">(GMT-5) America/Detroit</option>
														<option';  if(preg_match("/America\/Fort_Wayne/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Fort_Wayne">(GMT-5) America/Fort_Wayne</option>
														<option';  if(preg_match("/America\/Grand_Turk/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Grand_Turk">(GMT-5) America/Grand_Turk</option>
														<option';  if(preg_match("/America\/Guayaquil/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Guayaquil">(GMT-5) America/Guayaquil</option>
														<option';  if(preg_match("/America\/Havana/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Havana">(GMT-5) America/Havana</option>
														<option';  if(preg_match("/America\/Indianapolis/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Indianapolis">(GMT-5) America/Indianapolis</option>
														<option';  if(preg_match("/America\/Iqaluit/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Iqaluit">(GMT-5) America/Iqaluit</option>
														<option';  if(preg_match("/America\/Jamaica/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Jamaica">(GMT-5) America/Jamaica</option>
														<option';  if(preg_match("/America\/Lima/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Lima">(GMT-5) America/Lima</option>
														<option';  if(preg_match("/America\/Louisville/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Louisville">(GMT-5) America/Louisville</option>
														<option';  if(preg_match("/America\/Montreal/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Montreal">(GMT-5) America/Montreal</option>
														<option';  if(preg_match("/America\/Nassau/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Nassau">(GMT-5) America/Nassau</option>
														<option';  if(preg_match("/America\/New_York/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/New_York">(GMT-5) America/New_York</option>
														<option';  if(preg_match("/America\/Nipigon/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Nipigon">(GMT-5) America/Nipigon</option>
														<option';  if(preg_match("/America\/Panama/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Panama">(GMT-5) America/Panama</option>
														<option';  if(preg_match("/America\/Pangnirtung/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Pangnirtung">(GMT-5) America/Pangnirtung</option>
														<option';  if(preg_match("/America\/Port-au-Prince/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Port-au-Prince">(GMT-5) America/Port-au-Prince</option>
														<option';  if(preg_match("/America\/Resolute/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Resolute">(GMT-5) America/Resolute</option>
														<option';  if(preg_match("/America\/Thunder_Bay/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Thunder_Bay">(GMT-5) America/Thunder_Bay</option>
														<option';  if(preg_match("/America\/Toronto/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-5) America/Toronto">(GMT-5) America/Toronto</option>
														<option';  if(preg_match("/America\/Caracas/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4.5) America/Caracas">(GMT-4.5) America/Caracas</option>
														<option';  if(preg_match("/America\/Anguilla/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Anguilla">(GMT-4) America/Anguilla</option>
														<option';  if(preg_match("/America\/Antigua/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Antigua">(GMT-4) America/Antigua</option>
														<option';  if(preg_match("/America\/Aruba/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Aruba">(GMT-4) America/Aruba</option>
														<option';  if(preg_match("/America\/Asuncion/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Asuncion">(GMT-4) America/Asuncion</option>
														<option';  if(preg_match("/America\/Barbados/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Barbados">(GMT-4) America/Barbados</option>
														<option';  if(preg_match("/America\/Blanc-Sablon/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Blanc-Sablon">(GMT-4) America/Blanc-Sablon</option>
														<option';  if(preg_match("/America\/Boa_Vista/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Boa_Vista">(GMT-4) America/Boa_Vista</option>
														<option';  if(preg_match("/America\/Campo_Grande/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Campo_Grande">(GMT-4) America/Campo_Grande</option>
														<option';  if(preg_match("/America\/Cuiaba/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Cuiaba">(GMT-4) America/Cuiaba</option>
														<option';  if(preg_match("/America\/Curacao/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Curacao">(GMT-4) America/Curacao</option>
														<option';  if(preg_match("/America\/Dominica/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Dominica">(GMT-4) America/Dominica</option>
														<option';  if(preg_match("/America\/Eirunepe/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Eirunepe">(GMT-4) America/Eirunepe</option>
														<option';  if(preg_match("/America\/Glace_Bay/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Glace_Bay">(GMT-4) America/Glace_Bay</option>
														<option';  if(preg_match("/America\/Goose_Bay/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Goose_Bay">(GMT-4) America/Goose_Bay</option>
														<option';  if(preg_match("/America\/Grenada/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Grenada">(GMT-4) America/Grenada</option>
														<option';  if(preg_match("/America\/Guadeloupe/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Guadeloupe">(GMT-4) America/Guadeloupe</option>
														<option';  if(preg_match("/America\/Guyana/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Guyana">(GMT-4) America/Guyana</option>
														<option';  if(preg_match("/America\/Halifax/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Halifax">(GMT-4) America/Halifax</option>
														<option';  if(preg_match("/America\/La_Paz/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/La_Paz">(GMT-4) America/La_Paz</option>
														<option';  if(preg_match("/America\/Manaus/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Manaus">(GMT-4) America/Manaus</option>
														<option';  if(preg_match("/America\/Marigot/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Marigot">(GMT-4) America/Marigot</option>
														<option';  if(preg_match("/America\/Martinique/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Martinique">(GMT-4) America/Martinique</option>
														<option';  if(preg_match("/America\/Moncton/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Moncton">(GMT-4) America/Moncton</option>
														<option';  if(preg_match("/America\/Montserrat/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Montserrat">(GMT-4) America/Montserrat</option>
														<option';  if(preg_match("/America\/Port_of_Spain/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Port_of_Spain">(GMT-4) America/Port_of_Spain</option>
														<option';  if(preg_match("/America\/Porto_Acre/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Porto_Acre">(GMT-4) America/Porto_Acre</option>
														<option';  if(preg_match("/America\/Porto_Velho/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Porto_Velho">(GMT-4) America/Porto_Velho</option>
														<option';  if(preg_match("/America\/Puerto_Rico/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Puerto_Rico">(GMT-4) America/Puerto_Rico</option>
														<option';  if(preg_match("/America\/Rio_Branco/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Rio_Branco">(GMT-4) America/Rio_Branco</option>
														<option';  if(preg_match("/America\/Santiago/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Santiago">(GMT-4) America/Santiago</option>
														<option';  if(preg_match("/America\/Santo_Domingo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Santo_Domingo">(GMT-4) America/Santo_Domingo</option>
														<option';  if(preg_match("/America\/St_Barthelemy/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/St_Barthelemy">(GMT-4) America/St_Barthelemy</option>
														<option';  if(preg_match("/America\/St_Kitts/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/St_Kitts">(GMT-4) America/St_Kitts</option>
														<option';  if(preg_match("/America\/St_Lucia/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/St_Lucia">(GMT-4) America/St_Lucia</option>
														<option';  if(preg_match("/America\/St_Thomas/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/St_Thomas">(GMT-4) America/St_Thomas</option>
														<option';  if(preg_match("/America\/St_Vincent/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/St_Vincent">(GMT-4) America/St_Vincent</option>
														<option';  if(preg_match("/America\/Thule/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Thule">(GMT-4) America/Thule</option>
														<option';  if(preg_match("/America\/Tortola/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Tortola">(GMT-4) America/Tortola</option>
														<option';  if(preg_match("/America\/Virgin/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) America/Virgin">(GMT-4) America/Virgin</option>
														<option';  if(preg_match("/Antarctica\/Palmer/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) Antarctica/Palmer">(GMT-4) Antarctica/Palmer</option>
														<option';  if(preg_match("/Atlantic\/Bermuda/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) Atlantic/Bermuda">(GMT-4) Atlantic/Bermuda</option>
														<option';  if(preg_match("/Atlantic\/Stanley/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-4) Atlantic/Stanley">(GMT-4) Atlantic/Stanley</option>
														<option';  if(preg_match("/America\/St_Johns/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3.5) America/St_Johns">(GMT-3.5) America/St_Johns</option>
														<option';  if(preg_match("/America\/Araguaina/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Araguaina">(GMT-3) America/Araguaina</option>
														<option';  if(preg_match("/America\/Bahia/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Bahia">(GMT-3) America/Bahia</option>
														<option';  if(preg_match("/America\/Belem/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Belem">(GMT-3) America/Belem</option>
														<option';  if(preg_match("/America\/Buenos_Aires/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Buenos_Aires">(GMT-3) America/Buenos_Aires</option>
														<option';  if(preg_match("/America\/Catamarca/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Catamarca">(GMT-3) America/Catamarca</option>
														<option';  if(preg_match("/America\/Cayenne/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Cayenne">(GMT-3) America/Cayenne</option>
														<option';  if(preg_match("/America\/Cordoba/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Cordoba">(GMT-3) America/Cordoba</option>
														<option';  if(preg_match("/America\/Fortaleza/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Fortaleza">(GMT-3) America/Fortaleza</option>
														<option';  if(preg_match("/America\/Godthab/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Godthab">(GMT-3) America/Godthab</option>
														<option';  if(preg_match("/America\/Jujuy/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Jujuy">(GMT-3) America/Jujuy</option>
														<option';  if(preg_match("/America\/Maceio/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Maceio">(GMT-3) America/Maceio</option>
														<option';  if(preg_match("/America\/Mendoza/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Mendoza">(GMT-3) America/Mendoza</option>
														<option';  if(preg_match("/America\/Miquelon/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Miquelon">(GMT-3) America/Miquelon</option>
														<option';  if(preg_match("/America\/Montevideo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Montevideo">(GMT-3) America/Montevideo</option>
														<option';  if(preg_match("/America\/Paramaribo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Paramaribo">(GMT-3) America/Paramaribo</option>
														<option';  if(preg_match("/America\/Recife/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Recife">(GMT-3) America/Recife</option>
														<option';  if(preg_match("/America\/Rosario/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Rosario">(GMT-3) America/Rosario</option>
														<option';  if(preg_match("/America\/Santarem/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Santarem">(GMT-3) America/Santarem</option>
														<option';  if(preg_match("/America\/Sao_Paulo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) America/Sao_Paulo">(GMT-3) America/Sao_Paulo</option>
														<option';  if(preg_match("/Antarctica\/Rothera/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-3) Antarctica/Rothera">(GMT-3) Antarctica/Rothera</option>
														<option';  if(preg_match("/America\/Noronha/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-2) America/Noronha">(GMT-2) America/Noronha</option>
														<option';  if(preg_match("/Atlantic\/South_Georgia/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-2) Atlantic/South_Georgia">(GMT-2) Atlantic/South_Georgia</option>
														<option';  if(preg_match("/America\/Scoresbysund/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-1) America/Scoresbysund">(GMT-1) America/Scoresbysund</option>
														<option';  if(preg_match("/Atlantic\/Azores/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-1) Atlantic/Azores">(GMT-1) Atlantic/Azores</option>
														<option';  if(preg_match("/Atlantic\/Cape_Verde/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT-1) Atlantic/Cape_Verde">(GMT-1) Atlantic/Cape_Verde</option>
														<option';  if(preg_match("/Africa\/Abidjan/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Abidjan">(GMT+0) Africa/Abidjan</option>
														<option';  if(preg_match("/Africa\/Accra/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Accra">(GMT+0) Africa/Accra</option>
														<option';  if(preg_match("/Africa\/Bamako/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Bamako">(GMT+0) Africa/Bamako</option>
														<option';  if(preg_match("/Africa\/Banjul/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Banjul">(GMT+0) Africa/Banjul</option>
														<option';  if(preg_match("/Africa\/Bissau/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Bissau">(GMT+0) Africa/Bissau</option>
														<option';  if(preg_match("/Africa\/Casablanca/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Casablanca">(GMT+0) Africa/Casablanca</option>
														<option';  if(preg_match("/Africa\/Conakry/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Conakry">(GMT+0) Africa/Conakry</option>
														<option';  if(preg_match("/Africa\/Dakar/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Dakar">(GMT+0) Africa/Dakar</option>
														<option';  if(preg_match("/Africa\/El_Aaiun/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/El_Aaiun">(GMT+0) Africa/El_Aaiun</option>
														<option';  if(preg_match("/Africa\/Freetown/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Freetown">(GMT+0) Africa/Freetown</option>
														<option';  if(preg_match("/Africa\/Lome/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Lome">(GMT+0) Africa/Lome</option>
														<option';  if(preg_match("/Africa\/Monrovia/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Monrovia">(GMT+0) Africa/Monrovia</option>
														<option';  if(preg_match("/Africa\/Nouakchott/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Nouakchott">(GMT+0) Africa/Nouakchott</option>
														<option';  if(preg_match("/Africa\/Ouagadougou/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Ouagadougou">(GMT+0) Africa/Ouagadougou</option>
														<option';  if(preg_match("/Africa\/Sao_Tome/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Sao_Tome">(GMT+0) Africa/Sao_Tome</option>
														<option';  if(preg_match("/Africa\/Timbuktu/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Africa/Timbuktu">(GMT+0) Africa/Timbuktu</option>
														<option';  if(preg_match("/America\/Danmarkshavn/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) America/Danmarkshavn">(GMT+0) America/Danmarkshavn</option>
														<option';  if(preg_match("/Atlantic\/Canary/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Atlantic/Canary">(GMT+0) Atlantic/Canary</option>
														<option';  if(preg_match("/Atlantic\/Faeroe/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Atlantic/Faeroe">(GMT+0) Atlantic/Faeroe</option>
														<option';  if(preg_match("/Atlantic\/Faroe/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Atlantic/Faroe">(GMT+0) Atlantic/Faroe</option>
														<option';  if(preg_match("/Atlantic\/Madeira/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Atlantic/Madeira">(GMT+0) Atlantic/Madeira</option>
														<option';  if(preg_match("/Atlantic\/Reykjavik/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Atlantic/Reykjavik">(GMT+0) Atlantic/Reykjavik</option>
														<option';  if(preg_match("/Atlantic\/St_Helena/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Atlantic/St_Helena">(GMT+0) Atlantic/St_Helena</option>
														<option';  if(preg_match("/Europe\/Belfast/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Europe/Belfast">(GMT+0) Europe/Belfast</option>
														<option';  if(preg_match("/Europe\/Dublin/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Europe/Dublin">(GMT+0) Europe/Dublin</option>
														<option';  if(preg_match("/Europe\/Guernsey/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Europe/Guernsey">(GMT+0) Europe/Guernsey</option>
														<option';  if(preg_match("/Europe\/Isle_of_Man/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Europe/Isle_of_Man">(GMT+0) Europe/Isle_of_Man</option>
														<option';  if(preg_match("/Europe\/Jersey/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Europe/Jersey">(GMT+0) Europe/Jersey</option>
														<option';  if(preg_match("/Europe\/Lisbon/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Europe/Lisbon">(GMT+0) Europe/Lisbon</option>
														<option';  if(preg_match("/Europe\/London/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+0) Europe/London">(GMT+0) Europe/London</option>
														<option';  if(preg_match("/Africa\/Algiers/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Algiers">(GMT+1) Africa/Algiers</option>
														<option';  if(preg_match("/Africa\/Bangui/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Bangui">(GMT+1) Africa/Bangui</option>
														<option';  if(preg_match("/Africa\/Brazzaville/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Brazzaville">(GMT+1) Africa/Brazzaville</option>
														<option';  if(preg_match("/Africa\/Ceuta/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Ceuta">(GMT+1) Africa/Ceuta</option>
														<option';  if(preg_match("/Africa\/Douala/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Douala">(GMT+1) Africa/Douala</option>
														<option';  if(preg_match("/Africa\/Kinshasa/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Kinshasa">(GMT+1) Africa/Kinshasa</option>
														<option';  if(preg_match("/Africa\/Lagos/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Lagos">(GMT+1) Africa/Lagos</option>
														<option';  if(preg_match("/Africa\/Libreville/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Libreville">(GMT+1) Africa/Libreville</option>
														<option';  if(preg_match("/Africa\/Luanda/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Luanda">(GMT+1) Africa/Luanda</option>
														<option';  if(preg_match("/Africa\/Malabo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Malabo">(GMT+1) Africa/Malabo</option>
														<option';  if(preg_match("/Africa\/Ndjamena/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Ndjamena">(GMT+1) Africa/Ndjamena</option>
														<option';  if(preg_match("/Africa\/Niamey/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Niamey">(GMT+1) Africa/Niamey</option>
														<option';  if(preg_match("/Africa\/Porto-Novo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Porto-Novo">(GMT+1) Africa/Porto-Novo</option>
														<option';  if(preg_match("/Africa\/Tunis/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Tunis">(GMT+1) Africa/Tunis</option>
														<option';  if(preg_match("/Africa\/Windhoek/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Africa/Windhoek">(GMT+1) Africa/Windhoek</option>
														<option';  if(preg_match("/Atlantic\/Jan_Mayen/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Atlantic/Jan_Mayen">(GMT+1) Atlantic/Jan_Mayen</option>
														<option';  if(preg_match("/Europe\/Amsterdam/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Amsterdam">(GMT+1) Europe/Amsterdam</option>
														<option';  if(preg_match("/Europe\/Andorra/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Andorra">(GMT+1) Europe/Andorra</option>
														<option';  if(preg_match("/Europe\/Belgrade/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Belgrade">(GMT+1) Europe/Belgrade</option>
														<option';  if(preg_match("/Europe\/Berlin/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Berlin">(GMT+1) Europe/Berlin</option>
														<option';  if(preg_match("/Europe\/Bratislava/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Bratislava">(GMT+1) Europe/Bratislava</option>
														<option';  if(preg_match("/Europe\/Brussels/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Brussels">(GMT+1) Europe/Brussels</option>
														<option';  if(preg_match("/Europe\/Budapest/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Budapest">(GMT+1) Europe/Budapest</option>
														<option';  if(preg_match("/Europe\/Copenhagen/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Copenhagen">(GMT+1) Europe/Copenhagen</option>
														<option';  if(preg_match("/Europe\/Gibraltar/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Gibraltar">(GMT+1) Europe/Gibraltar</option>
														<option';  if(preg_match("/Europe\/Ljubljana/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Ljubljana">(GMT+1) Europe/Ljubljana</option>
														<option';  if(preg_match("/Europe\/Luxembourg/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Luxembourg">(GMT+1) Europe/Luxembourg</option>
														<option';  if(preg_match("/Europe\/Madrid/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Madrid">(GMT+1) Europe/Madrid</option>
														<option';  if(preg_match("/Europe\/Malta/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Malta">(GMT+1) Europe/Malta</option>
														<option';  if(preg_match("/Europe\/Monaco/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Monaco">(GMT+1) Europe/Monaco</option>
														<option';  if(preg_match("/Europe\/Oslo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Oslo">(GMT+1) Europe/Oslo</option>
														<option';  if(preg_match("/Europe\/Paris/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Paris">(GMT+1) Europe/Paris</option>
														<option';  if(preg_match("/Europe\/Podgorica/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Podgorica">(GMT+1) Europe/Podgorica</option>
														<option';  if(preg_match("/Europe\/Prague/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Prague">(GMT+1) Europe/Prague</option>
														<option';  if(preg_match("/Europe\/Rome/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Rome">(GMT+1) Europe/Rome</option>
														<option';  if(preg_match("/Europe\/San_Marino/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/San_Marino">(GMT+1) Europe/San_Marino</option>
														<option';  if(preg_match("/Europe\/Sarajevo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Sarajevo">(GMT+1) Europe/Sarajevo</option>
														<option';  if(preg_match("/Europe\/Skopje/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Skopje">(GMT+1) Europe/Skopje</option>
														<option';  if(preg_match("/Europe\/Stockholm/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Stockholm">(GMT+1) Europe/Stockholm</option>
														<option';  if(preg_match("/Europe\/Tirane/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Tirane">(GMT+1) Europe/Tirane</option>
														<option';  if(preg_match("/Europe\/Vaduz/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Vaduz">(GMT+1) Europe/Vaduz</option>
														<option';  if(preg_match("/Europe\/Vatican/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Vatican">(GMT+1) Europe/Vatican</option>
														<option';  if(preg_match("/Europe\/Vienna/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Vienna">(GMT+1) Europe/Vienna</option>
														<option';  if(preg_match("/Europe\/Warsaw/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Warsaw">(GMT+1) Europe/Warsaw</option>
														<option';  if(preg_match("/Europe\/Zagreb/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Zagreb">(GMT+1) Europe/Zagreb</option>
														<option';  if(preg_match("/Europe\/Zurich/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+1) Europe/Zurich">(GMT+1) Europe/Zurich</option>
														<option';  if(preg_match("/Africa\/Blantyre/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Blantyre">(GMT+2) Africa/Blantyre</option>
														<option';  if(preg_match("/Africa\/Bujumbura/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Bujumbura">(GMT+2) Africa/Bujumbura</option>
														<option';  if(preg_match("/Africa\/Cairo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Cairo">(GMT+2) Africa/Cairo</option>
														<option';  if(preg_match("/Africa\/Gaborone/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Gaborone">(GMT+2) Africa/Gaborone</option>
														<option';  if(preg_match("/Africa\/Harare/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Harare">(GMT+2) Africa/Harare</option>
														<option';  if(preg_match("/Africa\/Johannesburg/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Johannesburg">(GMT+2) Africa/Johannesburg</option>
														<option';  if(preg_match("/Africa\/Kigali/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Kigali">(GMT+2) Africa/Kigali</option>
														<option';  if(preg_match("/Africa\/Lubumbashi/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Lubumbashi">(GMT+2) Africa/Lubumbashi</option>
														<option';  if(preg_match("/Africa\/Lusaka/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Lusaka">(GMT+2) Africa/Lusaka</option>
														<option';  if(preg_match("/Africa\/Maputo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Maputo">(GMT+2) Africa/Maputo</option>
														<option';  if(preg_match("/Africa\/Maseru/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Maseru">(GMT+2) Africa/Maseru</option>
														<option';  if(preg_match("/Africa\/Mbabane/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Mbabane">(GMT+2) Africa/Mbabane</option>
														<option';  if(preg_match("/Africa\/Tripoli/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Africa/Tripoli">(GMT+2) Africa/Tripoli</option>
														<option';  if(preg_match("/Asia\/Amman/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Asia/Amman">(GMT+2) Asia/Amman</option>
														<option';  if(preg_match("/Asia\/Beirut/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Asia/Beirut">(GMT+2) Asia/Beirut</option>
														<option';  if(preg_match("/Asia\/Damascus/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Asia/Damascus">(GMT+2) Asia/Damascus</option>
														<option';  if(preg_match("/Asia\/Gaza/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Asia/Gaza">(GMT+2) Asia/Gaza</option>
														<option';  if(preg_match("/Asia\/Istanbul/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Asia/Istanbul">(GMT+2) Asia/Istanbul</option>
														<option';  if(preg_match("/Asia\/Jerusalem/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Asia/Jerusalem">(GMT+2) Asia/Jerusalem</option>
														<option';  if(preg_match("/Asia\/Nicosia/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Asia/Nicosia">(GMT+2) Asia/Nicosia</option>
														<option';  if(preg_match("/Asia\/Tel_Aviv/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Asia/Tel_Aviv">(GMT+2) Asia/Tel_Aviv</option>
														<option';  if(preg_match("/Europe\/Athens/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Athens">(GMT+2) Europe/Athens</option>
														<option';  if(preg_match("/Europe\/Bucharest/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Bucharest">(GMT+2) Europe/Bucharest</option>
														<option';  if(preg_match("/Europe\/Chisinau/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Chisinau">(GMT+2) Europe/Chisinau</option>
														<option';  if(preg_match("/Europe\/Helsinki/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Helsinki">(GMT+2) Europe/Helsinki</option>
														<option';  if(preg_match("/Europe\/Istanbul/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Istanbul">(GMT+2) Europe/Istanbul</option>
														<option';  if(preg_match("/Europe\/Kaliningrad/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Kaliningrad">(GMT+2) Europe/Kaliningrad</option>
														<option';  if(preg_match("/Europe\/Kiev/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Kiev">(GMT+2) Europe/Kiev</option>
														<option';  if(preg_match("/Europe\/Mariehamn/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Mariehamn">(GMT+2) Europe/Mariehamn</option>
														<option';  if(preg_match("/Europe\/Minsk/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Minsk">(GMT+2) Europe/Minsk</option>
														<option';  if(preg_match("/Europe\/Nicosia/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Nicosia">(GMT+2) Europe/Nicosia</option>
														<option';  if(preg_match("/Europe\/Riga/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Riga">(GMT+2) Europe/Riga</option>
														<option';  if(preg_match("/Europe\/Simferopol/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Simferopol">(GMT+2) Europe/Simferopol</option>
														<option';  if(preg_match("/Europe\/Sofia/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Sofia">(GMT+2) Europe/Sofia</option>
														<option';  if(preg_match("/Europe\/Tallinn/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Tallinn">(GMT+2) Europe/Tallinn</option>
														<option';  if(preg_match("/Europe\/Tiraspol/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Tiraspol">(GMT+2) Europe/Tiraspol</option>
														<option';  if(preg_match("/Europe\/Uzhgorod/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Uzhgorod">(GMT+2) Europe/Uzhgorod</option>
														<option';  if(preg_match("/Europe\/Vilnius/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Vilnius">(GMT+2) Europe/Vilnius</option>
														<option';  if(preg_match("/Europe\/Zaporozhye/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+2) Europe/Zaporozhye">(GMT+2) Europe/Zaporozhye</option>
														<option';  if(preg_match("/Africa\/Addis_Ababa/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Africa/Addis_Ababa">(GMT+3) Africa/Addis_Ababa</option>
														<option';  if(preg_match("/Africa\/Asmara/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Africa/Asmara">(GMT+3) Africa/Asmara</option>
														<option';  if(preg_match("/Africa\/Asmera/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Africa/Asmera">(GMT+3) Africa/Asmera</option>
														<option';  if(preg_match("/Africa\/Dar_es_Salaam/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Africa/Dar_es_Salaam">(GMT+3) Africa/Dar_es_Salaam</option>
														<option';  if(preg_match("/Africa\/Djibouti/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Africa/Djibouti">(GMT+3) Africa/Djibouti</option>
														<option';  if(preg_match("/Africa\/Kampala/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Africa/Kampala">(GMT+3) Africa/Kampala</option>
														<option';  if(preg_match("/Africa\/Khartoum/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Africa/Khartoum">(GMT+3) Africa/Khartoum</option>
														<option';  if(preg_match("/Africa\/Mogadishu/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Africa/Mogadishu">(GMT+3) Africa/Mogadishu</option>
														<option';  if(preg_match("/Africa\/Nairobi/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Africa/Nairobi">(GMT+3) Africa/Nairobi</option>
														<option';  if(preg_match("/Antarctica\/Syowa/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Antarctica/Syowa">(GMT+3) Antarctica/Syowa</option>
														<option';  if(preg_match("/Asia\/Aden/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Asia/Aden">(GMT+3) Asia/Aden</option>
														<option';  if(preg_match("/Asia\/Baghdad/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Asia/Baghdad">(GMT+3) Asia/Baghdad</option>
														<option';  if(preg_match("/Asia\/Bahrain/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Asia/Bahrain">(GMT+3) Asia/Bahrain</option>
														<option';  if(preg_match("/Asia\/Kuwait/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Asia/Kuwait">(GMT+3) Asia/Kuwait</option>
														<option';  if(preg_match("/Asia\/Qatar/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Asia/Qatar">(GMT+3) Asia/Qatar</option>
														<option';  if(preg_match("/Asia\/Riyadh/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Asia/Riyadh">(GMT+3) Asia/Riyadh</option>
														<option';  if(preg_match("/Europe\/Moscow/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Europe/Moscow">(GMT+3) Europe/Moscow</option>
														<option';  if(preg_match("/Europe\/Volgograd/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Europe/Volgograd">(GMT+3) Europe/Volgograd</option>
														<option';  if(preg_match("/Indian\/Antananarivo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Indian/Antananarivo">(GMT+3) Indian/Antananarivo</option>
														<option';  if(preg_match("/Indian\/Comoro/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Indian/Comoro">(GMT+3) Indian/Comoro</option>
														<option';  if(preg_match("/Indian\/Mayotte/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3) Indian/Mayotte">(GMT+3) Indian/Mayotte</option>
														<option';  if(preg_match("/Asia\/Riyadh87/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3.11777777778) Asia/Riyadh87">(GMT+3.11777777778) Asia/Riyadh87</option>
														<option';  if(preg_match("/Asia\/Riyadh88/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3.11777777778) Asia/Riyadh88">(GMT+3.11777777778) Asia/Riyadh88</option>
														<option';  if(preg_match("/Asia\/Riyadh89/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3.11777777778) Asia/Riyadh89">(GMT+3.11777777778) Asia/Riyadh89</option>
														<option';  if(preg_match("/Asia\/Tehran/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+3.5) Asia/Tehran">(GMT+3.5) Asia/Tehran</option>
														<option';  if(preg_match("/Asia\/Baku/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+4) Asia/Baku">(GMT+4) Asia/Baku</option>
														<option';  if(preg_match("/Asia\/Dubai/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+4) Asia/Dubai">(GMT+4) Asia/Dubai</option>
														<option';  if(preg_match("/Asia\/Muscat/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+4) Asia/Muscat">(GMT+4) Asia/Muscat</option>
														<option';  if(preg_match("/Asia\/Tbilisi/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+4) Asia/Tbilisi">(GMT+4) Asia/Tbilisi</option>
														<option';  if(preg_match("/Asia\/Yerevan/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+4) Asia/Yerevan">(GMT+4) Asia/Yerevan</option>
														<option';  if(preg_match("/Europe\/Samara/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+4) Europe/Samara">(GMT+4) Europe/Samara</option>
														<option';  if(preg_match("/Indian\/Mahe/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+4) Indian/Mahe">(GMT+4) Indian/Mahe</option>
														<option';  if(preg_match("/Indian\/Mauritius/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+4) Indian/Mauritius">(GMT+4) Indian/Mauritius</option>
														<option';  if(preg_match("/Indian\/Reunion/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+4) Indian/Reunion">(GMT+4) Indian/Reunion</option>
														<option';  if(preg_match("/Asia\/Kabul/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+4.5) Asia/Kabul">(GMT+4.5) Asia/Kabul</option>
														<option';  if(preg_match("/Asia\/Aqtau/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Asia/Aqtau">(GMT+5) Asia/Aqtau</option>
														<option';  if(preg_match("/Asia\/Aqtobe/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Asia/Aqtobe">(GMT+5) Asia/Aqtobe</option>
														<option';  if(preg_match("/Asia\/Ashgabat/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Asia/Ashgabat">(GMT+5) Asia/Ashgabat</option>
														<option';  if(preg_match("/Asia\/Ashkhabad/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Asia/Ashkhabad">(GMT+5) Asia/Ashkhabad</option>
														<option';  if(preg_match("/Asia\/Dushanbe/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Asia/Dushanbe">(GMT+5) Asia/Dushanbe</option>
														<option';  if(preg_match("/Asia\/Karachi/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Asia/Karachi">(GMT+5) Asia/Karachi</option>
														<option';  if(preg_match("/Asia\/Oral/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Asia/Oral">(GMT+5) Asia/Oral</option>
														<option';  if(preg_match("/Asia\/Samarkand/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Asia/Samarkand">(GMT+5) Asia/Samarkand</option>
														<option';  if(preg_match("/Asia\/Tashkent/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Asia/Tashkent">(GMT+5) Asia/Tashkent</option>
														<option';  if(preg_match("/Asia\/Yekaterinburg/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Asia/Yekaterinburg">(GMT+5) Asia/Yekaterinburg</option>
														<option';  if(preg_match("/Indian\/Kerguelen/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Indian/Kerguelen">(GMT+5) Indian/Kerguelen</option>
														<option';  if(preg_match("/Indian\/Maldives/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5) Indian/Maldives">(GMT+5) Indian/Maldives</option>
														<option';  if(preg_match("/Asia\/Calcutta/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5.5) Asia/Calcutta">(GMT+5.5) Asia/Calcutta</option>
														<option';  if(preg_match("/Asia\/Colombo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5.5) Asia/Colombo">(GMT+5.5) Asia/Colombo</option>
														<option';  if(preg_match("/Asia\/Kolkata/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5.5) Asia/Kolkata">(GMT+5.5) Asia/Kolkata</option>
														<option';  if(preg_match("/Asia\/Kathmandu/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5.75) Asia/Kathmandu">(GMT+5.75) Asia/Kathmandu</option>
														<option';  if(preg_match("/Asia\/Katmandu/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+5.75) Asia/Katmandu">(GMT+5.75) Asia/Katmandu</option>
														<option';  if(preg_match("/Antarctica\/Mawson/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Antarctica/Mawson">(GMT+6) Antarctica/Mawson</option>
														<option';  if(preg_match("/Antarctica\/Vostok/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Antarctica/Vostok">(GMT+6) Antarctica/Vostok</option>
														<option';  if(preg_match("/Asia\/Almaty/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Asia/Almaty">(GMT+6) Asia/Almaty</option>
														<option';  if(preg_match("/Asia\/Bishkek/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Asia/Bishkek">(GMT+6) Asia/Bishkek</option>
														<option';  if(preg_match("/Asia\/Dacca/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Asia/Dacca">(GMT+6) Asia/Dacca</option>
														<option';  if(preg_match("/Asia\/Dhaka/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Asia/Dhaka">(GMT+6) Asia/Dhaka</option>
														<option';  if(preg_match("/Asia\/Novosibirsk/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Asia/Novosibirsk">(GMT+6) Asia/Novosibirsk</option>
														<option';  if(preg_match("/Asia\/Omsk/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Asia/Omsk">(GMT+6) Asia/Omsk</option>
														<option';  if(preg_match("/Asia\/Qyzylorda/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Asia/Qyzylorda">(GMT+6) Asia/Qyzylorda</option>
														<option';  if(preg_match("/Asia\/Thimbu/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Asia/Thimbu">(GMT+6) Asia/Thimbu</option>
														<option';  if(preg_match("/Asia\/Thimphu/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Asia/Thimphu">(GMT+6) Asia/Thimphu</option>
														<option';  if(preg_match("/Indian\/Chagos/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6) Indian/Chagos">(GMT+6) Indian/Chagos</option>
														<option';  if(preg_match("/Asia\/Rangoon/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6.5) Asia/Rangoon">(GMT+6.5) Asia/Rangoon</option>
														<option';  if(preg_match("/Indian\/Cocos/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+6.5) Indian/Cocos">(GMT+6.5) Indian/Cocos</option>
														<option';  if(preg_match("/Antarctica\/Davis/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Antarctica/Davis">(GMT+7) Antarctica/Davis</option>
														<option';  if(preg_match("/Asia\/Bangkok/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Asia/Bangkok">(GMT+7) Asia/Bangkok</option>
														<option';  if(preg_match("/Asia\/Ho_Chi_Minh/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Asia/Ho_Chi_Minh">(GMT+7) Asia/Ho_Chi_Minh</option>
														<option';  if(preg_match("/Asia\/Hovd/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Asia/Hovd">(GMT+7) Asia/Hovd</option>
														<option';  if(preg_match("/Asia\/Jakarta/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Asia/Jakarta">(GMT+7) Asia/Jakarta</option>
														<option';  if(preg_match("/Asia\/Krasnoyarsk/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Asia/Krasnoyarsk">(GMT+7) Asia/Krasnoyarsk</option>
														<option';  if(preg_match("/Asia\/Phnom_Penh/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Asia/Phnom_Penh">(GMT+7) Asia/Phnom_Penh</option>
														<option';  if(preg_match("/Asia\/Pontianak/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Asia/Pontianak">(GMT+7) Asia/Pontianak</option>
														<option';  if(preg_match("/Asia\/Saigon/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Asia/Saigon">(GMT+7) Asia/Saigon</option>
														<option';  if(preg_match("/Asia\/Vientiane/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Asia/Vientiane">(GMT+7) Asia/Vientiane</option>
														<option';  if(preg_match("/Indian\/Christmas/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+7) Indian/Christmas">(GMT+7) Indian/Christmas</option>
														<option';  if(preg_match("/Antarctica\/Casey/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Antarctica/Casey">(GMT+8) Antarctica/Casey</option>
														<option';  if(preg_match("/Asia\/Brunei/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Brunei">(GMT+8) Asia/Brunei</option>
														<option';  if(preg_match("/Asia\/Choibalsan/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Choibalsan">(GMT+8) Asia/Choibalsan</option>
														<option';  if(preg_match("/Asia\/Chongqing/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Chongqing">(GMT+8) Asia/Chongqing</option>
														<option';  if(preg_match("/Asia\/Chungking/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Chungking">(GMT+8) Asia/Chungking</option>
														<option';  if(preg_match("/Asia\/Harbin/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Harbin">(GMT+8) Asia/Harbin</option>
														<option';  if(preg_match("/Asia\/Hong_Kong/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Hong_Kong">(GMT+8) Asia/Hong_Kong</option>
														<option';  if(preg_match("/Asia\/Irkutsk/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Irkutsk">(GMT+8) Asia/Irkutsk</option>
														<option';  if(preg_match("/Asia\/Kashgar/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Kashgar">(GMT+8) Asia/Kashgar</option>
														<option';  if(preg_match("/Asia\/Kuala_Lumpur/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Kuala_Lumpur">(GMT+8) Asia/Kuala_Lumpur</option>
														<option';  if(preg_match("/Asia\/Kuching/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Kuching">(GMT+8) Asia/Kuching</option>
														<option';  if(preg_match("/Asia\/Macao/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Macao">(GMT+8) Asia/Macao</option>
														<option';  if(preg_match("/Asia\/Macau/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Macau">(GMT+8) Asia/Macau</option>
														<option';  if(preg_match("/Asia\/Makassar/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Makassar">(GMT+8) Asia/Makassar</option>
														<option';  if(preg_match("/Asia\/Manila/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Manila">(GMT+8) Asia/Manila</option>
														<option';  if(preg_match("/Asia\/Shanghai/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Shanghai">(GMT+8) Asia/Shanghai</option>
														<option';  if(preg_match("/Asia\/Singapore/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Singapore">(GMT+8) Asia/Singapore</option>
														<option';  if(preg_match("/Asia\/Taipei/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Taipei">(GMT+8) Asia/Taipei</option>
														<option';  if(preg_match("/Asia\/Ujung_Pandang/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Ujung_Pandang">(GMT+8) Asia/Ujung_Pandang</option>
														<option';  if(preg_match("/Asia\/Ulaanbaatar/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Ulaanbaatar">(GMT+8) Asia/Ulaanbaatar</option>
														<option';  if(preg_match("/Asia\/Ulan_Bator/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Ulan_Bator">(GMT+8) Asia/Ulan_Bator</option>
														<option';  if(preg_match("/Asia\/Urumqi/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Asia/Urumqi">(GMT+8) Asia/Urumqi</option>
														<option';  if(preg_match("/Australia\/Perth/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Australia/Perth">(GMT+8) Australia/Perth</option>
														<option';  if(preg_match("/Australia\/West/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8) Australia/West">(GMT+8) Australia/West</option>
														<option';  if(preg_match("/Australia\/Eucla/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+8.75) Australia/Eucla">(GMT+8.75) Australia/Eucla</option>
														<option';  if(preg_match("/Asia\/Dili/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9) Asia/Dili">(GMT+9) Asia/Dili</option>
														<option';  if(preg_match("/Asia\/Jayapura/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9) Asia/Jayapura">(GMT+9) Asia/Jayapura</option>
														<option';  if(preg_match("/Asia\/Pyongyang/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9) Asia/Pyongyang">(GMT+9) Asia/Pyongyang</option>
														<option';  if(preg_match("/Asia\/Seoul/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9) Asia/Seoul">(GMT+9) Asia/Seoul</option>
														<option';  if(preg_match("/Asia\/Tokyo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9) Asia/Tokyo">(GMT+9) Asia/Tokyo</option>
														<option';  if(preg_match("/Asia\/Yakutsk/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9) Asia/Yakutsk">(GMT+9) Asia/Yakutsk</option>
														<option';  if(preg_match("/Pacific\/Palau/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9) Pacific/Palau">(GMT+9) Pacific/Palau</option>
														<option';  if(preg_match("/Australia\/Adelaide/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9.5) Australia/Adelaide">(GMT+9.5) Australia/Adelaide</option>
														<option';  if(preg_match("/Australia\/Broken_Hill/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9.5) Australia/Broken_Hill">(GMT+9.5) Australia/Broken_Hill</option>
														<option';  if(preg_match("/Australia\/Darwin/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9.5) Australia/Darwin">(GMT+9.5) Australia/Darwin</option>
														<option';  if(preg_match("/Australia\/North/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9.5) Australia/North">(GMT+9.5) Australia/North</option>
														<option';  if(preg_match("/Australia\/South/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9.5) Australia/South">(GMT+9.5) Australia/South</option>
														<option';  if(preg_match("/Australia\/Yancowinna/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+9.5) Australia/Yancowinna">(GMT+9.5) Australia/Yancowinna</option>
														<option';  if(preg_match("/Antarctica\/DumontDUrville/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Antarctica/DumontDUrville">(GMT+10) Antarctica/DumontDUrville</option>
														<option';  if(preg_match("/Asia\/Sakhalin/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Asia/Sakhalin">(GMT+10) Asia/Sakhalin</option>
														<option';  if(preg_match("/Asia\/Vladivostok/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Asia/Vladivostok">(GMT+10) Asia/Vladivostok</option>
														<option';  if(preg_match("/Australia\/ACT/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/ACT">(GMT+10) Australia/ACT</option>
														<option';  if(preg_match("/Australia\/Brisbane/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Brisbane">(GMT+10) Australia/Brisbane</option>
														<option';  if(preg_match("/Australia\/Canberra/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Canberra">(GMT+10) Australia/Canberra</option>
														<option';  if(preg_match("/Australia\/Currie/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Currie">(GMT+10) Australia/Currie</option>
														<option';  if(preg_match("/Australia\/Hobart/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Hobart">(GMT+10) Australia/Hobart</option>
														<option';  if(preg_match("/Australia\/LHI/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/LHI">(GMT+10) Australia/LHI</option>
														<option';  if(preg_match("/Australia\/Lindeman/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Lindeman">(GMT+10) Australia/Lindeman</option>
														<option';  if(preg_match("/Australia\/Lord_Howe/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Lord_Howe">(GMT+10) Australia/Lord_Howe</option>
														<option';  if(preg_match("/Australia\/Melbourne/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Melbourne">(GMT+10) Australia/Melbourne</option>
														<option';  if(preg_match("/Australia\/NSW/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/NSW">(GMT+10) Australia/NSW</option>
														<option';  if(preg_match("/Australia\/Queensland/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Queensland">(GMT+10) Australia/Queensland</option>
														<option';  if(preg_match("/Australia\/Sydney/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Sydney">(GMT+10) Australia/Sydney</option>
														<option';  if(preg_match("/Australia\/Tasmania/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Tasmania">(GMT+10) Australia/Tasmania</option>
														<option';  if(preg_match("/Australia\/Victoria/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Australia/Victoria">(GMT+10) Australia/Victoria</option>
														<option';  if(preg_match("/Pacific\/Guam/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Pacific/Guam">(GMT+10) Pacific/Guam</option>
														<option';  if(preg_match("/Pacific\/Port_Moresby/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Pacific/Port_Moresby">(GMT+10) Pacific/Port_Moresby</option>
														<option';  if(preg_match("/Pacific\/Saipan/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Pacific/Saipan">(GMT+10) Pacific/Saipan</option>
														<option';  if(preg_match("/Pacific\/Truk/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Pacific/Truk">(GMT+10) Pacific/Truk</option>
														<option';  if(preg_match("/Pacific\/Yap/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+10) Pacific/Yap">(GMT+10) Pacific/Yap</option>
														<option';  if(preg_match("/Asia\/Magadan/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+11) Asia/Magadan">(GMT+11) Asia/Magadan</option>
														<option';  if(preg_match("/Pacific\/Efate/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+11) Pacific/Efate">(GMT+11) Pacific/Efate</option>
														<option';  if(preg_match("/Pacific\/Guadalcanal/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+11) Pacific/Guadalcanal">(GMT+11) Pacific/Guadalcanal</option>
														<option';  if(preg_match("/Pacific\/Kosrae/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+11) Pacific/Kosrae">(GMT+11) Pacific/Kosrae</option>
														<option';  if(preg_match("/Pacific\/Noumea/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+11) Pacific/Noumea">(GMT+11) Pacific/Noumea</option>
														<option';  if(preg_match("/Pacific\/Ponape/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+11) Pacific/Ponape">(GMT+11) Pacific/Ponape</option>
														<option';  if(preg_match("/Pacific\/Norfolk/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+11.5) Pacific/Norfolk">(GMT+11.5) Pacific/Norfolk</option>
														<option';  if(preg_match("/Antarctica\/McMurdo/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Antarctica/McMurdo">(GMT+12) Antarctica/McMurdo</option>
														<option';  if(preg_match("/Antarctica\/South_Pole/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Antarctica/South_Pole">(GMT+12) Antarctica/South_Pole</option>
														<option';  if(preg_match("/Asia\/Anadyr/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Asia/Anadyr">(GMT+12) Asia/Anadyr</option>
														<option';  if(preg_match("/Asia\/Kamchatka/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Asia/Kamchatka">(GMT+12) Asia/Kamchatka</option>
														<option';  if(preg_match("/Pacific\/Auckland/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Pacific/Auckland">(GMT+12) Pacific/Auckland</option>
														<option';  if(preg_match("/Pacific\/Fiji/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Pacific/Fiji">(GMT+12) Pacific/Fiji</option>
														<option';  if(preg_match("/Pacific\/Funafuti/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Pacific/Funafuti">(GMT+12) Pacific/Funafuti</option>
														<option';  if(preg_match("/Pacific\/Kwajalein/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Pacific/Kwajalein">(GMT+12) Pacific/Kwajalein</option>
														<option';  if(preg_match("/Pacific\/Majuro/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Pacific/Majuro">(GMT+12) Pacific/Majuro</option>
														<option';  if(preg_match("/Pacific\/Nauru/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Pacific/Nauru">(GMT+12) Pacific/Nauru</option>
														<option';  if(preg_match("/Pacific\/Tarawa/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Pacific/Tarawa">(GMT+12) Pacific/Tarawa</option>
														<option';  if(preg_match("/Pacific\/Wake/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Pacific/Wake">(GMT+12) Pacific/Wake</option>
														<option';  if(preg_match("/Pacific\/Wallis/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12) Pacific/Wallis">(GMT+12) Pacific/Wallis</option>
														<option';  if(preg_match("/Pacific\/Chatham/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+12.75) Pacific/Chatham">(GMT+12.75) Pacific/Chatham</option>
														<option';  if(preg_match("/Pacific\/Enderbury/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+13) Pacific/Enderbury">(GMT+13) Pacific/Enderbury</option>
														<option';  if(preg_match("/Pacific\/Tongatapu/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+13) Pacific/Tongatapu">(GMT+13) Pacific/Tongatapu</option>
														<option';  if(preg_match("/Pacific\/Kiritimati/",get_option("em_timezone"))=="1"){  $eventform.=' Selected="selected"'; }; $eventform.=' value="(GMT+14) Pacific/Kiritimati">(GMT+14) Pacific/Kiritimati</option>

														</select></div></div>';
							
							if($atts['popuponly']=="0"){
								$eventform.='<div class="row"> 
									<div class="left"><strong>Save it as:</strong></div>  
									<div class="right">A Popup?<input type="radio" name="saveitaspopup" id="saveitas" value="popup" selected/>OR A Post?<input type="radio" name="saveitaspopup" id="saveitas" value="posted"/></div> 
								</div>';
							}
							else
							{
								$eventform.='<input type="hidden" id="saveitas" name="saveitaspopup" value="popup" />';
							}
							
							$eventform.='<div class="row"> 
								<div class="left">&nbsp;</div>  
								<div class="right"><input type="submit" name="submit" id="adddevent" value="Add Event"/></div> 
							</div> 
							<input type="hidden" name="nonce-eventify" value="'. wp_create_nonce('eventify-nonce').'" />
							<input type="hidden" name="addempost" id="addempost" value="1" />
						</form>';
						
						//do all the user level permissions here..
							global $current_user; //check the level of the user based on the instance set in the widget using this.
							get_currentuserinfo(); //get all info about current user and store it in the above global var
						if($atts['loggedin']=="1") //if allowed for all visitors then allow access to all
						{
							$accessto="1";
						}
						else
						{
							if(isset($current_user->user_level))
							{
								$accessto=$current_user->user_level;
							}
							else
							{
								$accessto="";
								
							}

						}
						
						//a bigger if for checking and displaying the page/post to only with permissiifons
						if($accessto>=$atts['loggedin'])
						{
								//enter to the db if the form is submitted or else display the form as is.
						
								if(isset($_POST['addempost']) && $_POST['addempost']=="1")
								{
							
									if ( get_magic_quotes_gpc() ) {
									    $_POST      = array_map( 'stripslashes_deep', $_POST );

									}

									global $wpdb;
									$table_name = $wpdb->prefix."em_main";
										if($current_user->user_level!="") //add the user name to the even desc to show who added the event from the front end.
										{
											$_POST['em_desc'].= "<br/>Event Added By -<u>".$current_user->user_login."</u>";
										}
										if($_POST['saveitaspopup']=="posted"){
										// Create post object


							  		//	$my_post = array();
							  			$my_post['post_title'] = $_POST['em_title'];
							  			$my_post['post_content'] = "<h2>Event Description:</h2>".$_POST['em_desc']."<br/><h2>Event Date</h2>".$_POST['datepicker_post']."<br/><h2>Event Time</h2>From: ".$_POST['timepick_from_post']." - To: ".$_POST['timepick_to_post']."<br/><h2>Event Venue</h2>".$_POST['em_venue']."<br/><h2>Event TimeZone</h2>".$_POST['em_timezone'];
							  			$my_post['post_status'] = 'publish';
							  			$my_post['post_author'] = 1;
							 			$my_post['post_category'] = array(99=>'Events');

										// Insert the post into the database
							 				$postedid = @wp_insert_post( $my_post ); //undefined offset? 
										
											$sql = "insert into ".$table_name." values(null,'".$_POST['datepicker_post']."','".$_POST['timepick_from_post']."-".$_POST['timepick_to_post']."','".str_ireplace("'","\'",$_POST['em_desc'])."','".str_ireplace("'","\'",$_POST['em_title'])."','".str_ireplace("'","\'",$_POST['em_venue'])."','".$_POST['em_timezone']."','".$postedid."','".strtotime($_POST['datepicker_post'])."')";
											

									 	}
									 	else
									 	{
									 		$sql = "insert into ".$table_name." values(null,'".$_POST['datepicker_post']."','".$_POST['timepick_from_post']."-".$_POST['timepick_to_post']."','".str_ireplace("'","\'",$_POST['em_desc'])."','".str_ireplace("'","\'",$_POST['em_title'])."','".str_ireplace("'","\'",$_POST['em_venue'])."','".$_POST['em_timezone']."','0','".strtotime($_POST['datepicker_post'])."')";
									 	}
									 	$wpdb->query($sql);
									 	update_option("em_timezone",$_POST['em_timezone']);

										return "<br/><h3>Event Has been succesfully added! Thank You For your Submission</h3><br/>".$eventform;
									$wpdb->print_error();
								
							
								}
								else
								{
									return $eventform;
								}
						}//permissio if
					else
					{
						return 'You Do Not Have Sufficient Permissions to Access This Form. Please contact the administrator/webmaster of the blog';
					}//permission else
		}
		
		function eventifytag_func($atts)
		{
			//getting the atts from the shortcode
			$eventlisted=""; //var to return the eventlist to the page in.
			$atts_ret = extract(shortcode_atts(array(
				'displaytype' => 'events',
				'displayno' => '7',
				),$atts));
				
			/*Get the data from the database*/
			global $wpdb;
			$table_name = $wpdb->prefix."em_main";
			$qry= "select * from ".$table_name." order by em_timestamp ASC" ;		
			
			//echo $qry;
		 	$results = $wpdb->get_results($qry);
		 	//$results = $wpdb->query($qry);
			date("m-d-Y");

			$date_today = getdate();
			$date_today = $date_today['mon']."/".$date_today['mday']."/".$date_today['year'];
			$date_today = strtotime($date_today);
			
			/*make sure shortcode atts are right*/
			if($atts['displaytype']!="events" && $atts['displaytype']!="days")
			{
				$atts['displaytype']="events";
			}
			if(!is_numeric($atts['displayno']))
			{
				$atts['displayno']="7";
			}
			/*store the required html to the var which will be returned and given back to page*/
			$eventlisted.="<ul>";
					
		if ( $atts['displaytype'] == 'days' )
		{
			foreach($results as $row){
				/*if($instance['di_format']=="dd/mm/yyyy")
				{
					$rowemdate = date("d/m/Y",strtotime($row->emdate));
				}
				elseif($instance['di_format']=="mm/dd/yyyy")
				{
						$rowemdate = date("m/d/Y",strtotime($row->emdate));
				}
				elseif($instance['di_format']=="mm.dd.yyyy")
				{
						$rowemdate = date("m.d.Y",strtotime($row->emdate));
				}
				else
				{
						$rowemdate = date("d.m.Y",strtotime($row->emdate));
				}*/ //next release will be active for pages.
				$rowemdate = date("m/d/Y",strtotime($row->em_date)); //remove this if above active.
				if(strtotime($row->em_date)>=$date_today && strtotime($row->em_date)<=strtotime("+".$atts['displayno']." day"))
				{  
					if($row->em_savetype>0)
					{
						
						
						$eventlisted.="<li>$rowemdate - <a href=\"".get_permalink($row->em_savetype)."\">".$row->em_title."</a>  </li>";
					}
					else
					{
						$eventlisted.="<li>$rowemdate - <a href=\"javascript:showMe('<strong>Event Desc:</strong> ". str_ireplace("'","&rsquo;",$row->em_desc)."<br/><Br/><strong>Date:</strong> $rowemdate;<br/><br/><strong>Event Time:</strong> $row->em_time<Br/><Br/><strong>Event Venue:</strong> $row->em_venue<Br/><Br/><strong>Event Time Zone:</strong> $row->em_timezone<Br/><Br/>','<span class=\'titlespop\'>$row->em_title</span> ');\">".$row->em_title."</a>  </li>";
					}
					
					/*echo "<strong>Time:</strong> ".$row->em_time."<br/>";
					echo "<strong>Desc:</strong> ".$row->em_desc."<br/>";
					echo "<strong>Date: </strong>".$row->em_date."<br/>";
					$i++;*/
				}
			
			}
		} //displaying next "n" events;
		
		if ( $atts['displaytype'] == 'events' )
		{
			$countloop = $atts['displayno'];
			
			if($countloop>count($results))
			{
				$countloop=count($results);
			}
			
			$arr_i=0;
			//echo count($results);
			//print_r($results[$arr_i]->em_desc);
			while($countloop>0)
			{
				if(strtotime($results[$arr_i]->em_date)>=$date_today)
				{
					/*if($instance['di_format']=="dd/mm/yyyy")
					{
						$rowemdate = date("d/m/Y",strtotime($results[$arr_i]->em_date));
					}
					elseif($instance['di_format']=="mm/dd/yyyy")
					{
						$rowemdate = date("m/d/Y",strtotime($results[$arr_i]->em_date));
					}
					elseif($instance['di_format']=="mm.dd.yyyy")
					{
						$rowemdate = date("m.d.Y",strtotime($results[$arr_i]->em_date));
					}
					else
					{
						$rowemdate = date("d.m.Y",strtotime($results[$arr_i]->em_date));
					}*/
					$rowemdate = date("m/d/Y",strtotime($results[$arr_i]->em_date)); //remove this if above active
					if($results[$arr_i]->em_savetype>0)
					{
						$eventlisted.="<li> ".$rowemdate." - <a href=\"".get_permalink($results[$arr_i]->em_savetype)."\">".$results[$arr_i]->em_title."</a> </li>";
					}
					else
					{
						$eventlisted.="<li> ".$rowemdate." - <a href=\"javascript:showMe('<strong>Event Desc:</strong> ".str_ireplace("'","&rsquo;",$results[$arr_i]->em_desc)."<br/><Br/><strong>Date:</strong> ".$rowemdate."<br/><br/><strong>Event Time:</strong> ".$results[$arr_i]->em_time."<Br/><Br/><strong>Event Venue:</strong> ".$results[$arr_i]->em_venue."<Br/><Br/><strong>Event Time Zone:</strong> ".$results[$arr_i]->em_timezone."<Br/><Br/>','<span class=\'titlespop\'>".$results[$arr_i]->em_title."</span> ');\">".$results[$arr_i]->em_title."</a> </li>";
					}
					
				}
				$countloop--;
				$arr_i++;
			}
		}//displaying  events in the next "n" days;
		
		$eventlisted.="</ul>";
				return $eventlisted;
				
		
		}//replace the shortcode with a list of events from the above functions :D 
	
	} //end of  class
}//end of if for class

?>

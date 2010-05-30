<?php
/*
Plugin Name: Specialist
Plugin URI: http://wp-plugins.clark-lowes.info/
Description: Specialist - a table of people with a set of specialist skills.
Version: 0.0.2
Author: Julian Clark-Lowes
Author URI: http://wp-plugins.clark-lowes.info/
*/
?>
<?php
/*  Copyright 2010 Julian Clark-Lowes (email : others@julian.clark-lowes.info)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php

/*************************************************/ 
// error_reporting(E_ALL);
// ini_set('display_errors', true);
//phpinfo();

$pluginprefix = isset($pluginprefix) ? $pluginprefix: "";
if (!class_exists("jcl_specialist_table_class")){
	include "includes/class.table.php";
}

 
if (!class_exists("jcl_specialist")){
	 class jcl_specialist extends jcl_specialist_table_class{
	 
		 var $tablename = 'jcl_specialist';
		 var $pluginname = 'jcl_specialist';
		 var $plugintitle = 'Specialist';
		 var $db_version = '1.0';
		 var $file = __FILE__;
		 
		 var $tabledef = array();
		 var $rowdef = array();
		 var $editdef = array();
		 
		 
		 function jcl_specialist($prefix){
		 
			$this->pluginprefix = $prefix;
		 
			$this->tabledef[1] = array("field"=>"ID","type"=>"int", "length"=>"11", "null"=>"NOT NULL","extra"=>"AUTO_INCREMENT");
			$this->tabledef[2] = array("field"=>"surname","type"=>"varchar", "length"=>"255","null"=>"NOT NULL",   "header"=>"Surname");
			$this->tabledef[3] = array("field"=>"surname2","type"=>"varchar", "length"=>"255","null"=>"NOT NULL",   "header"=>"Surname 2");
			$this->tabledef[4] = array("field"=>"displayname","type"=>"varchar", "length"=>"255","null"=>"NOT NULL",   "header"=>"Displayname");
			$this->tabledef[5] = array("field"=>"content","type"=>"varchar",  "length"=>"4096", "null"=>"NOT NULL",  "header"=>"Content");
			$this->tabledef[6] = array("field"=>"image","type"=>"varchar", "length"=>"255","null"=>"NOT NULL",   "header"=>"Image");
			$this->tabledef[7] = array("field"=>"jcl_lookup_regions_ID","type"=>"int", "length"=>"11", "null"=>"NOT NULL", "header"=>"Region");
			$this->tabledef[8] = array("field"=>"homepage","type"=>"varchar", "length"=>"255","null"=>"NOT NULL",   "header"=>"Homepage");
			$this->tabledef[9] = array("field"=>"dynamic","type"=>"tinyint", "length"=>"4","null"=>"NOT NULL",   "header"=>"Dynamic");
			$this->tabledef[10] = array("field"=>"frontpage","type"=>"tinyint", "length"=>"4","null"=>"NOT NULL",   "header"=>"Front Page");

			$this->rowdef[1] = array("display"=>"none");
			$this->rowdef[2] = array("width"=>"90px");
			$this->rowdef[3] = array("width"=>"90px");
			$this->rowdef[4] = array("action"=>"title");
			$this->rowdef[5] = array("width"=>"70px","truncate"=>"100","width"=>"200px");
			$this->rowdef[6] = array("truncate"=>"20");
			$this->rowdef[7] = array("width"=>"50px","htmlelement"=>"selectvalue", "selectfield"=>"region");
			$this->rowdef[9] = array("width"=>"60px","htmlelement"=>"no:yes");
			$this->rowdef[10] = array("width"=>"60px","htmlelement"=>"no:yes");
			
			$this->editdef[1] = array("display"=>"none");
			$this->editdef[2] = array("row"=>"1","start"=>"true");
			$this->editdef[3] = array("row"=>"1");
			$this->editdef[4] = array("row"=>"1","end"=>"true");
			$this->editdef[5] = array("htmlelement"=>"editor","colspan"=>"3");
			$this->editdef[7] = array("htmlelement"=>"select", "selectfield"=>"region");
			$this->editdef[9] = array("htmlelement"=>"checkbox");
			$this->editdef[10] = array("htmlelement"=>"checkbox");
			
		 }
		 
		 function get_select($context, $id = 0){
			global $wpdb;
			$this->joinstatement = " LEFT JOIN ".$wpdb->prefix."jcl_lookup_regions ON ".$wpdb->prefix."jcl_lookup_regions.ID = ".$wpdb->prefix.$this->tablename.".jcl_lookup_regions_ID ";
			$this->joinselect = ", ".$wpdb->prefix."jcl_lookup_regions.region";
			
			if ($context == "rows"){

			}
			if ($context == "form"){

			}	
			if ($context == "widget"){

			}			
			
			return parent::get_select($id);
		 } 
	 }
 
 

	$jcl_specialist = new jcl_specialist($pluginprefix);
	$jcl_specialist->init("",'jcl_specialist');
  
	function jcl_specialist_install(){
		$this_table_obj = new jcl_specialist("");
		$this_table_obj->do_install();
	}
  
	register_activation_hook(__FILE__,'jcl_specialist_install');
	register_deactivation_hook(__FILE__,'jcl_specialist_uninstall');
	  
	//debug code if it all doesn't work
	if (isset($_REQUEST["myaction"]) && $_REQUEST["myaction"]=="jcl_specialist_install"){
	  $jcl_specialist->do_install(1);
	}
	if (isset($_REQUEST["myaction"]) && $_REQUEST["myaction"]=="jcl_specialist_uninstall"){
	  $jcl_specialist->do_uninstall();
	}
}else{
	jcl_specialist_options();
}

?>
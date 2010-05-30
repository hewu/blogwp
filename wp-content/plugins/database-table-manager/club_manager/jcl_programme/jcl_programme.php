<?php
/*
Plugin Name: Programme
Plugin URI: http://wp-plugins.clark-lowes.info/
Description: Programme of Events Table created using database-table-manager code.
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
 //error_reporting(E_ALL);
 //ini_set('display_errors', true);
 
 $pluginprefix = isset($pluginprefix) ? $pluginprefix: "";
 if (!class_exists("jcl_programme_table_class")){
	include_once "includes/class.table.php";
}
 
if (!class_exists("jcl_programme")){	
 class jcl_programme extends jcl_programme_table_class{
 
	 var $tablename = 'jcl_programme';
	 var $pluginname = 'jcl_programme';
	 var $plugintitle = 'Programme';
	 var $db_version = '1.0';
	 var $file = __FILE__;
	 
	 var $tabledef = array();
	 var $rowdef = array();
	 var $editdef = array();
	 
	 public function __construct($prefix){
		$this->pluginprefix = $prefix;
	 
		$this->tabledef[1] = array("field"=>"ID","type"=>"bigint", "length"=>"10", "null"=>"NOT NULL","extra"=>"AUTO_INCREMENT");
		$this->tabledef[2] = array("field"=>"startdate","type"=>"date","null"=>"NOT NULL",   "header"=>"Start Date");
		$this->tabledef[3] = array("field"=>"enddate","type"=>"date","default"=>"NULL",   "header"=>"End Date");
		$this->tabledef[4] = array("field"=>"title","type"=>"varchar", "length"=>"255","null"=>"NOT NULL",   "header"=>"Title");
		$this->tabledef[5] = array("field"=>"activitytype","type"=>"varchar", "length"=>"100", "null"=>"NOT NULL",   "header"=>"Activity Type");
		$this->tabledef[6] = array("field"=>"contact","type"=>"varchar", "length"=>"100", "null"=>"NOT NULL",   "header"=>"Contact");
		$this->tabledef[7] = array("field"=>"description","type"=>"varchar",  "length"=>"4000", "null"=>"NOT NULL",  "header"=>"Description");
		$this->tabledef[8] = array("field"=>"foot1","type"=>"varchar",  "length"=>"255", "null"=>"NOT NULL",   "header"=>"Meet Time");
		$this->tabledef[9] = array("field"=>"foot2","type"=>"varchar",  "length"=>"255", "null"=>"NOT NULL",   "header"=>"Return/ Distance");
		$this->tabledef[10] = array("field"=>"trip","type"=>"tinyint",  "length"=>"1", "null"=>"NOT NULL",   "header"=>"Trip");
		$this->tabledef[11] = array("field"=>"social","type"=>"tinyint",  "length"=>"1", "null"=>"NOT NULL",   "header"=>"Social");
		$this->tabledef[12] = array("field"=>"admin","type"=>"tinyint",  "length"=>"1", "null"=>"NOT NULL",   "header"=>"Admin");
		
		$this->rowdef[1] = array("display"=>"none");
		$this->rowdef[2] = array("width"=>"70px");
		$this->rowdef[3] = array("width"=>"70px");
		$this->rowdef[5] = array("width"=>"70px");
		$this->rowdef[7] = array("colspan"=>"3","truncate"=>"100","width"=>"270px");
		$this->rowdef[8] = array("truncate"=>"50");
		$this->rowdef[10] = array("cell"=>"start","width"=>"50px");
		$this->rowdef[11] = array("cell"=>"none");
		$this->rowdef[12] = array("cell"=>"end");
		
		$this->editdef[1] = array("display"=>"none");
		$this->editdef[2] = array("header"=>"Start Date (YYYY-MM-DD)","row"=>"1","start"=>"true");
		$this->editdef[3] = array("header"=>"End Date (YYYY-MM-DD)", "row"=>"1","end"=>"true");
		$this->editdef[4] = array("htmlelement"=>"textarea","row"=>"2","start"=>"true");
		$this->editdef[5] = array("htmlelement"=>"textarea","row"=>"2");
		$this->editdef[6] = array("htmlelement"=>"textarea","row"=>"2","end"=>"true");
		$this->editdef[7] = array("htmlelement"=>"editor","colspan"=>"3");
		$this->editdef[8] = array("htmlelement"=>"textarea","cellafter"=>"1","row"=>"4","start"=>"true");
		$this->editdef[9] = array("htmlelement"=>"textarea","row"=>"4","end"=>"true", "header"=>"Return/Distance");
		$this->editdef[10] = array("row"=>"5","start"=>"true","htmlelement"=>"checkbox");
		$this->editdef[11] = array("row"=>"5","htmlelement"=>"checkbox");
		$this->editdef[12] = array("row"=>"5","end"=>"true","htmlelement"=>"checkbox");
		
		
	 }
	 
	 function get_select($context, $id = 0){
		$orderstatement = "";
		$filterstatement = "";
		$joinstatement = "";
		
		if ($context == "rows"){
			$orderstatement = " ORDER BY startdate";
			$filterstatement = "";
			$joinstatement = "";
		}
		if ($context == "form"){
			$orderstatement = "";
			$filterstatement = "";
			$joinstatement = "";
		}	
		if ($context == "widget"){
			$orderstatement = "";
			$filterstatement = "";
			$joinstatement = "";
		}			
		
		return parent::get_select($id, $joinstatement, $filterstatement, $orderstatement);
	 } 	 
 }
 
  $jcl_programme = new jcl_programme($pluginprefix);
  $jcl_programme->init($jcl_programme,'jcl_programme');

	  
	function jcl_programme_install(){
		//echo "installing";
		$this_table_obj = new jcl_programme("");
		$this_table_obj->do_install();
	}
	  
	register_activation_hook(__FILE__,'jcl_programme_install');
	register_deactivation_hook(__FILE__,'jcl_programme_uninstall');
	  
  //debug code if it all doesn't work
	if (isset($_REQUEST["action"]) && $_REQUEST["action"]=="jcl_programme_install"){
	  $jcl_programme->do_install(1);
	}
	if (isset($_REQUEST["action"]) && $_REQUEST["action"]=="jcl_programme_uninstall"){
	  $jcl_programme->do_uninstall();
	}
}else{
	jcl_programme_options();
}
?>
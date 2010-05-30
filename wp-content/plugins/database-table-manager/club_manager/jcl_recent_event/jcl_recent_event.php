<?php
/*
Plugin Name: Recent Event
Plugin URI: http://wp-plugins.clark-lowes.info/
Description:Recent Events Table created using database-table-manager code.
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
 if (!class_exists("jcl_recent_event_table_class")){
	include_once "includes/class.table.php";
}

if (!class_exists("jcl_recent_event")){	
 class jcl_recent_event extends jcl_recent_event_table_class{
 
	 var $tablename = 'jcl_recent_event';
	 var $pluginname = 'jcl_recent_event';
	 var $plugintitle = 'Recent Event';
	 var $db_version = '1.0';
	 var $file = __FILE__;
	 
	 var $tabledef = array();
	 var $rowdef = array();
	 var $editdef = array();
	 
	 public function __construct($prefix){
	 
		$this->pluginprefix = $prefix;

		$this->tabledef[1] = array("field"=>"ID","type"=>"mediumint(9)","null"=>"NOT NULL","extra"=>"AUTO_INCREMENT","display"=>"none");
		$this->tabledef[2] = array("field"=>"title","type"=>"varchar(255)","null"=>"NOT NULL","header"=>"Title");
		$this->tabledef[3] = array("field"=>"byline","type"=>"varchar(255)","null"=>"NOT NULL","header"=>"Byline");
		$this->tabledef[4] = array("field"=>"href","type"=>"varchar(2048)","null"=>"NOT NULL","header"=>"Href");
		$this->tabledef[5] = array("field"=>"image","type"=>"varchar(2048)","null"=>"NOT NULL","header"=>"Image");
		$this->tabledef[6] = array("field"=>"startdate","type"=>"date","null"=>"NOT NULL","header"=>"Start Date");
		$this->tabledef[7] = array("field"=>"enddate","type"=>"date","default"=>"NULL","header"=>"End Date");
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
 
 	  $jcl_recent_event = new jcl_recent_event($pluginprefix);
	  $jcl_recent_event->init($jcl_recent_event,'jcl_recent_event');

	  
	function jcl_recent_event_install(){
		//echo "installing";
		$this_table_obj = new jcl_recent_event("");
		$this_table_obj->do_install();
	}
	  
	register_activation_hook(__FILE__,'jcl_recent_event_install');
	register_deactivation_hook(__FILE__,'jcl_recent_event_uninstall');
	  
  //debug code if it all doesn't work
	if (isset($_REQUEST["action"]) && $_REQUEST["action"]=="jcl_recent_event_install"){
	  $jcl_recent_event->do_install(1);
	}
	if (isset($_REQUEST["action"]) && $_REQUEST["action"]=="jcl_recent_event_uninstall"){
	  $jcl_recent_event->do_uninstall();
	}
}else{
		jcl_recent_event_options();
}
?>
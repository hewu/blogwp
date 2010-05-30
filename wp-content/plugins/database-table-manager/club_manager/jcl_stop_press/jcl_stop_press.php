<?php
/*
Plugin Name: Stop Press
Plugin URI: http://wp-plugins.clark-lowes.info/
Description:Stop Press Table created using database-table-manager code.
Version: 0.0.1
Author: Julian Clark-Lowes
Author URI: http://wp-plugins.clark-lowes.info/
*/
?>
<?php
/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

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
 if (!class_exists("jcl_stop_press_table_class")){
	include_once "includes/class.table.php";
}

if (!class_exists("jcl_stop_press")){	 
 class jcl_stop_press extends jcl_stop_press_table_class{
 
	 var $tablename = 'jcl_stop_press';
	 var $pluginname = 'jcl_stop_press';
	 var $plugintitle = 'Stop Press';
	 var $db_version = '1.0';
	 var $file = __FILE__;
	 
	 
	 var $tabledef = array();
	 var $rowdef = array();
	 var $editdef = array();
	 
	 public function jcl_stop_press($prefix){
	 
		$this->pluginprefix = $prefix;

		$this->tabledef[1] = array("field"=>"ID","type"=>"mediumint(9)","null"=>"NOT NULL","extra"=>"AUTO_INCREMENT","display"=>"none");
		$this->tabledef[2] = array("field"=>"startdate","type"=>"date","null"=>"NOT NULL","header"=>"Start Date");
		$this->tabledef[3] = array("field"=>"enddate","type"=>"date","default"=>"NULL","header"=>"End Date");
		$this->tabledef[4] = array("field"=>"title","type"=>"varchar(255)","null"=>"NOT NULL","header"=>"Title");
		$this->tabledef[5] = array("field"=>"description","type"=>"varchar(4000)","null"=>"NOT NULL");

		$this->rowdef[5] = array("header"=>"Description","htmlelement"=>"editor","colspan"=>"3","truncate"=>"100","width"=>"270px");
		
		$this->tabledef[5] = array_merge($this->tabledef[5],$this->rowdef[5]);
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
 
  $jcl_stop_press = new jcl_stop_press($pluginprefix);
  $jcl_stop_press->init($jcl_stop_press,'jcl_stop_press');

	  
	function jcl_stop_press_install(){
		$this_table_obj = new jcl_stop_press("");
		$this_table_obj->do_install();
	}
	  
	register_activation_hook(__FILE__,'jcl_stop_press_install');
	register_deactivation_hook(__FILE__,'jcl_stop_press_uninstall');
	  
  //debug code if it all doesn't work
	if (isset($_REQUEST["myaction"]) && $_REQUEST["myaction"]=="jcl_stop_press_install"){
	  $jcl_stop_press->do_install(1);
	}
	if (isset($_REQUEST["myaction"]) && $_REQUEST["myaction"]=="jcl_stop_press_uninstall"){
	  $jcl_stop_press->do_uninstall();
	}
}else{
		jcl_stop_press_options();
}


?>
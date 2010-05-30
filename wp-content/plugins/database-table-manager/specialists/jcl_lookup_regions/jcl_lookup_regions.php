<?php
/*
Plugin Name: Regions
Plugin URI: http://wp-plugins.clark-lowes.info/
Description:Regions - a lookup table of regions in which the specialists operate (linked by ID).
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
if (!class_exists("jcl_lookup_regions_table_class")){
	include_once "includes/class.table.php";
}
 
if (!class_exists("jcl_lookup_regions")){	
 class jcl_lookup_regions extends jcl_lookup_regions_table_class{
 
	 var $tablename = 'jcl_lookup_regions';
	 var $pluginname = 'jcl_lookup_regions';
	 var $plugintitle = 'Regions';
	 var $db_version = '1.0';
	 var $file = __FILE__;
	 
	 
	 var $tabledef = array();
	 var $rowdef = array();
	 var $editdef = array();

 
	 function jcl_lookup_regions($prefix){
		$this->orderfield = "region";
		$this->pluginprefix = $prefix;
	 
		$this->tabledef[1] = array("field"=>"ID","type"=>"int", "length"=>"11", "null"=>"NOT NULL","extra"=>"AUTO_INCREMENT");
		$this->tabledef[2] = array("field"=>"region","type"=>"varchar", "length"=>"255","null"=>"NOT NULL",   "header"=>"Region");
		$this->tabledef[3] = array("field"=>"image","type"=>"varchar", "length"=>"255","null"=>"NOT NULL",   "header"=>"Image");


		$this->rowdef[1] = array("display"=>"none");
		$this->rowdef[2] = array("width"=>"100px");
		$this->rowdef[3] = array("width"=>"100px");
		$this->rowdef[4] = array("width"=>"100px","action"=>"title","header"=>"");

		
		$this->editdef[1] = array("display"=>"none");
		$this->editdef[2] = array("width"=>"300px");
		$this->editdef[3] = array("width"=>"300px");

		
		$this->widgetdef[1] = array("display"=>"none");
		$this->widgetdef[2] = array("width"=>"170px","htmlelement"=>"selectvalue", "selectfield"=>"Name");
		$this->widgetdef[3] = array("width"=>"170px","htmlelement"=>"selectvalue", "selectfield"=>"displayname");
		$this->widgetdef[4] = array("width"=>"170px","htmlelement"=>"no:yes");
		$this->widgetdef[5] = array("width"=>"170px","htmlelement"=>"no:yes");
		$this->widgetdef[6] = array("width"=>"150px");
		
		$this->options['widget_fields']['title'] = array('label'=>'Title:', 'type'=>'text', 'default'=>'');
		$this->options['widget_fields']['username'] = array('label'=>'Username:', 'type'=>'text', 'default'=>'');
		$this->options['widget_fields']['num'] = array('label'=>'Number of jcl_lookup_regions:', 'type'=>'text', 'default'=>'5');
		$this->options['widget_fields']['update'] = array('label'=>'Show timestamps:', 'type'=>'checkbox', 'default'=>true);
		$this->options['widget_fields']['linked'] = array('label'=>'Linked:', 'type'=>'text', 'default'=>'#');
		$this->options['widget_fields']['hyperjcl_lookup_regions'] = array('label'=>'Discover Hyperjcl_lookup_regions:', 'type'=>'checkbox', 'default'=>true);
		$this->options['widget_fields']['twitter_users'] = array('label'=>'Discover @replies:', 'type'=>'checkbox', 'default'=>true);
		$this->options['widget_fields']['encode_utf8'] = array('label'=>'UTF8 Encode:', 'type'=>'checkbox', 'default'=>false);
		$this->options['prefix'] = 'jcl_lookup_regions';
	 }
	 
	 function get_select($context, $id = 0){
		$orderstatement = "";
		$filterstatement = "";
		$joinstatement = "";
		
		if ($context == "rows"){
			$orderstatement = " ORDER BY region";
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
 
 
 $jcl_lookup_regions = new jcl_lookup_regions($pluginprefix);
 $jcl_lookup_regions->init($jcl_lookup_regions,'jcl_lookup_regions');
	function jcl_lookup_regions_install(){
		$this_table_obj = new jcl_lookup_regions("");
		$this_table_obj->do_install();
	}
  
	register_activation_hook(__FILE__,'jcl_lookup_regions_install');
	register_deactivation_hook(__FILE__,'jcl_lookup_regions_uninstall');
	  
	//debug code if it all doesn't work
	if (isset($_REQUEST["myaction"]) && $_REQUEST["myaction"]=="jcl_lookup_regions_install"){
	  $jcl_lookup_regions->do_install(1);
	}
	if (isset($_REQUEST["myaction"]) && $_REQUEST["myaction"]=="jcl_lookup_regions_uninstall"){
	  $jcl_lookup_regions->do_uninstall();
	}
 }else{
	jcl_lookup_regions_options();
}
  

?>
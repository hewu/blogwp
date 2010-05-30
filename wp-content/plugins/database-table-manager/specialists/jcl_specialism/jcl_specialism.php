<?php
/*
Plugin Name: Specialism
Plugin URI: http://wp-plugins.clark-lowes.info/
Description: Specialism - a table of various skills.
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
	if (!class_exists("jcl_specialism_table_class")){
		include_once "includes/class.table.php";
	}
	
if (!class_exists("jcl_specialism")){
	class jcl_specialism extends jcl_specialism_table_class{
 
	 var $tablename = 'jcl_specialism';
	 var $pluginname = 'jcl_specialism';
	 var $plugintitle = 'Specialism';
	 var $db_version = '1.0';
	 var $file = __FILE__;
	 
	 
	 var $tabledef = array();
	 var $rowdef = array();
	 var $editdef = array();
	 
	 function jcl_specialism($prefix){
		$this->orderfield = "Name";
		$this->pluginprefix = $prefix;
	 
		$this->tabledef[1] = array("field"=>"ID","type"=>"int", "length"=>"11", "null"=>"NOT NULL","extra"=>"AUTO_INCREMENT");
		$this->tabledef[2] = array("field"=>"Name","type"=>"varchar", "length"=>"255","null"=>"NOT NULL",   "header"=>"Specialism");

		$this->rowdef[1] = array("display"=>"none");
		$this->rowdef[2] = array("width"=>"170px", "selectfield"=>"Name");
		$this->rowdef[3] = array("width"=>"100px","action"=>"title","header"=>"");
		
		$this->editdef[1] = array("display"=>"none");
		$this->editdef[2] = array();
		
		$this->widgetdef[1] = array("display"=>"none");
		$this->widgetdef[2] = array();
		
		$this->options['widget_fields']['title'] = array('label'=>'Title:', 'type'=>'text', 'default'=>'');
		$this->options['widget_fields']['username'] = array('label'=>'Username:', 'type'=>'text', 'default'=>'');
		$this->options['widget_fields']['num'] = array('label'=>'Number of jcl_specialism:', 'type'=>'text', 'default'=>'5');
		$this->options['widget_fields']['update'] = array('label'=>'Show timestamps:', 'type'=>'checkbox', 'default'=>true);
		$this->options['widget_fields']['linked'] = array('label'=>'Linked:', 'type'=>'text', 'default'=>'#');
		$this->options['widget_fields']['hyperjcl_specialism'] = array('label'=>'Discover Hyperjcl_specialism:', 'type'=>'checkbox', 'default'=>true);
		$this->options['widget_fields']['twitter_users'] = array('label'=>'Discover @replies:', 'type'=>'checkbox', 'default'=>true);
		$this->options['widget_fields']['encode_utf8'] = array('label'=>'UTF8 Encode:', 'type'=>'checkbox', 'default'=>false);
		$this->options['prefix'] = 'jcl_specialism';
	 }
	 
	 function get_select($context, $id = 0){
		$orderstatement = "";
		$filterstatement = "";
		$joinstatement = "";
		
		if ($context == "rows"){
		}
		if ($context == "form"){
		}	
		if ($context == "widget"){
		}			
		
		return parent::get_select($id);
	 }
	
 }
 
 
 $jcl_specialism = new jcl_specialism($pluginprefix);
 $jcl_specialism->init($jcl_specialism,'jcl_specialism');
 
	function jcl_specialism_install(){
		$this_table_obj = new jcl_specialism("");
		$this_table_obj->do_install();
	}
  
	register_activation_hook(__FILE__,'jcl_specialism_install');
	register_deactivation_hook(__FILE__,'jcl_specialism_uninstall');
	  
	//debug code if it all doesn't work
	if (isset($_REQUEST["myaction"]) && $_REQUEST["myaction"]=="jcl_specialism_install"){
	  $jcl_specialism->do_install(1);
	}
	if (isset($_REQUEST["myaction"]) && $_REQUEST["myaction"]=="jcl_specialism_uninstall"){
	  $jcl_specialism->do_uninstall();
	}
 }else{
	jcl_specialism_options();
}
  

?>
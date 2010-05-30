<?php
/*
Plugin Name: Specialist Links
Plugin URI: http://wp-plugins.clark-lowes.info/
Description: A many to many link table joining  Specialists with their many different Specialisms.  Uses left joins and lookup tables. 
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
if (!class_exists("jcl_sp_links_sp_table_class")){
	include_once "includes/class.table.php";
}
 
if (!class_exists("jcl_sp_links_sp")){
 class jcl_sp_links_sp extends jcl_sp_links_sp_table_class{
 
	 var $tablename = 'jcl_sp_links_sp';
	 var $pluginname = 'jcl_sp_links_sp';
	 var $plugintitle = 'Links';
	 var $db_version = '1.0';
	 var $file = __FILE__;
	 
	 
	 var $tabledef = array();
	 var $rowdef = array();
	 var $editdef = array();
	 
	 function jcl_sp_links_sp($prefix){
		global $wpdb;
	 
	 	$this->pluginprefix = $prefix;
		$this->orderfield = $wpdb->prefix."jcl_specialism.Name";
	 
		$this->tabledef[1] = array("field"=>"ID","type"=>"int", "length"=>"11", "null"=>"NOT NULL","extra"=>"AUTO_INCREMENT");
		$this->tabledef[2] = array("field"=>"jcl_specialism_ID","type"=>"int", "length"=>"11","null"=>"NOT NULL",   "header"=>"Specialism");
		$this->tabledef[3] = array("field"=>"jcl_specialist_ID","type"=>"int", "length"=>"11","null"=>"NOT NULL",   "header"=>"Specialist");
		//$this->tabledef[4] = array("field"=>"hideonmenu","type"=>"tinyint", "length"=>"4","null"=>"NOT NULL",   "header"=>"Hide on menu");
		//$this->tabledef[5] = array("field"=>"hideonhome","type"=>"tinyint",  "length"=>"4", "null"=>"NOT NULL",  "header"=>"Hide on home");
		$this->tabledef[4] = array("field"=>"customdescription","type"=>"varchar", "length"=>"255","null"=>"NOT NULL",   "header"=>"Custom Description");

		$this->rowdef[1] = array("display"=>"none");
		$this->rowdef[2] = array("width"=>"170px","htmlelement"=>"selectvalue", "selectfield"=>"Name", "filter"=>"true", "sort"=>"Name");
		$this->rowdef[3] = array("width"=>"170px","htmlelement"=>"selectvalue", "selectfield"=>"displayname", "filter"=>"true", "sort"=>"surname");
		$this->rowdef[4] = array("width"=>"160px");
		$this->rowdef[5] = array("width"=>"100px","action"=>"title","header"=>"");
		
		$this->editdef[1] = array("display"=>"none");
		$this->editdef[2] = array("row"=>"1","start"=>"true","htmlelement"=>"select", "selectfield"=>"Name", "sort"=>"Name");
		$this->editdef[3] = array("row"=>"1","end"=>"true","htmlelement"=>"select", "selectfield"=>"displayname", "sort"=>"surname");
		$this->editdef[4] = array("colspan"=>"2", "default"=>"");	
		
		$this->widgetdef[1] = array("display"=>"none");
		$this->widgetdef[2] = array("width"=>"170px","htmlelement"=>"selectvalue", "selectfield"=>"Name");
		$this->widgetdef[3] = array("width"=>"170px","htmlelement"=>"selectvalue", "selectfield"=>"displayname");
		$this->widgetdef[4] = array("width"=>"150px");
		
		$this->options['widget_fields']['title'] = array('label'=>'Title:', 'type'=>'text', 'default'=>'');
		$this->options['widget_fields']['username'] = array('label'=>'Username:', 'type'=>'text', 'default'=>'');
		$this->options['widget_fields']['num'] = array('label'=>'Number of jcl_sp_links_sp:', 'type'=>'text', 'default'=>'5');
		$this->options['widget_fields']['update'] = array('label'=>'Show timestamps:', 'type'=>'checkbox', 'default'=>true);
		$this->options['widget_fields']['linked'] = array('label'=>'Linked:', 'type'=>'text', 'default'=>'#');
		$this->options['widget_fields']['hyperjcl_sp_links_sp'] = array('label'=>'Discover Hyperjcl_sp_links_sp:', 'type'=>'checkbox', 'default'=>true);
		$this->options['widget_fields']['twitter_users'] = array('label'=>'Discover @replies:', 'type'=>'checkbox', 'default'=>true);
		$this->options['widget_fields']['encode_utf8'] = array('label'=>'UTF8 Encode:', 'type'=>'checkbox', 'default'=>false);
		$this->options['prefix'] = 'jcl_sp_links_sp';
	 }
	 
	 function get_select($context, $id = 0){
		global $wpdb;
		$this->joinstatement = " LEFT JOIN ".$wpdb->prefix."jcl_specialism ON ".$wpdb->prefix."jcl_specialism.ID = ".$wpdb->prefix.$this->tablename.".jcl_specialism_ID ";
		$this->joinstatement .= " LEFT JOIN ".$wpdb->prefix."jcl_specialist ON ".$wpdb->prefix."jcl_specialist.ID = ".$wpdb->prefix.$this->tablename.".jcl_specialist_ID ";
		$this->joinselect = ", ".$wpdb->prefix."jcl_specialism.Name";
		$this->joinselect .= ", ".$wpdb->prefix."jcl_specialist.surname, ".$wpdb->prefix."jcl_specialist.displayname";
		if ($context == "rows"){
			
		}
		if ($context == "form"){

		}	
		if ($context == "widget"){

		}
		
		return parent::get_select($id);
	 }
	
 }
 
 
 $jcl_sp_links_sp = new jcl_sp_links_sp($pluginprefix);
 $jcl_sp_links_sp->init($jcl_sp_links_sp,'jcl_sp_links_sp');
 
	function jcl_sp_links_sp_install(){
		$this_table_obj = new jcl_sp_links_sp("");
		$this_table_obj->do_install();
	}
  
	register_activation_hook(__FILE__,'jcl_sp_links_sp_install');
	register_deactivation_hook(__FILE__,'jcl_sp_links_sp_uninstall');
	  
	//debug code if it all doesn't work
	if (isset($_REQUEST["myaction"]) && $_REQUEST["myaction"]=="jcl_sp_links_sp_install"){
	  $jcl_sp_links_sp->do_install(1);
	}
	if (isset($_REQUEST["myaction"]) && $_REQUEST["myaction"]=="jcl_sp_links_sp_uninstall"){
	  $jcl_sp_links_sp->do_uninstall();
	}
  }else{
	jcl_sp_links_sp_options();
}
  

?>
<?php

class jcl_specialism_table_class {
	var $pluginprefix = "";

	 function init($me, $pluginname){
		//create sub menu
		eval('function '.$pluginname.'_create_submenu()
				{
					global $'.$pluginname.';
					$pluginname = \''.$pluginname.'\';
					$'.$pluginname.'->do_create_submenu();
				}'
			);
		add_action('admin_menu',$pluginname.'_create_submenu');
		//register_activation_hook(__FILE__,$pluginname.'_install');
		//uninstall
		eval('function '.$pluginname.'_uninstall()
			{
					global $'.$pluginname.';
					$pluginname = \''.$pluginname.'\';
					$'.$pluginname.'->do_uninstall();
			}'
		);
		register_activation_hook(__FILE__,$pluginname.'_uninstall');
		//choose between form and row pages
		eval('function '.$pluginname.'_options() 
			{
				global  $'.$pluginname.', $wpdb;
				$this_table_obj = $'.$pluginname.';
			   
				$this_table_obj->setFilters();
				include WP_PLUGIN_DIR."/'.$this->pluginprefix.$pluginname.'/includes/post.php";
				
				$'.$pluginname.'->do_setup_tinymce();		
			}'
		);
		
		eval('function widget_'.$pluginname.'_init() 
			{

				if ( !function_exists(\'register_sidebar_widget\') )
					return;
					
				$check_options = get_option(\'widget_'.$pluginname.'\');
				  if (isset($check_options[\'number\']) && $check_options[\'number\'] =="") {
					$check_options[\'number\'] = 1;
					update_option(\'widget_'.$pluginname.'\', $check_options);
				  }
				$check_options[\'number\'] = 1;
				update_option(\'widget_'.$pluginname.'\', $check_options);
				

				//output on front page
				function widget_'.$pluginname.'($args, $number = 1) {
					global $'.$pluginname.', $'.$pluginname.'_options;
					$'.$pluginname.'->widget_pluginname($'.$pluginname.'_options, $args, $number);		
				}
				
				//here is the admin panel
				function widget_'.$pluginname.'_control($number) {
					global $'.$pluginname.', $'.$pluginname.'_options;
					$'.$pluginname.'->widget_pluginname_control($'.$pluginname.'_options, $number);
					
				}

				function widget_'.$pluginname.'_setup() {
					global $'.$pluginname.';
					$'.$pluginname.'->widget_pluginname_setup();
					$'.$pluginname.'->widget_pluginname_register();
				}
				
				function widget_'.$pluginname.'_page() {
					//echo "pluginname page";
				}

				widget_'.$pluginname.'_setup();
			}'
		);
		
		add_action('widgets_init', 'widget_'.$pluginname.'_init');
	 }

	 function get_rowdef(){
		$displaydef = array();
		for ($i = 1;array_key_exists($i, $this->tabledef) || array_key_exists($i, $this->rowdef); $i++){
			if (isset($this->tabledef[$i]) && isset($this->rowdef[$i])){
				$displaydef[$i] = array_merge($this->tabledef[$i],$this->rowdef[$i]);
				if (isset($this->rowdef[$i]["filter"]))
					$this->showfilterbar = "yes";
			}elseif (isset($this->tabledef[$i])){
				$displaydef[$i] = $this->tabledef[$i];
			}elseif (isset($this->rowdef[$i])){
				$displaydef[$i] = $this->rowdef[$i];
				if (isset($this->rowdef[$i]["filter"]))
					$this->showfilterbar = "yes";
			}
		}
		return $displaydef;
	 }
	 
	 function get_tabledef(){
		return $this->tabledef;
	 }
	 
	 function get_widgetdef(){
		$widgetdef = array();
		for ($i = 1;$i <= count($this->tabledef); $i++){
			if (isset($this->editdef[$i])){
				$widgetdef[$i] = array_merge($this->tabledef[$i],$this->widgetdef[$i]);
			}else{
				$widgetdef[$i] = $this->widgetdef[$i];
			}
		}
		return $widgetdef;
	 }
	 
	 function get_editdef(){
		$displaydef = array();
		for ($i = 1;$i <= count($this->tabledef); $i++){
			if (isset($this->editdef[$i])){
				$displaydef[$i] = array_merge($this->tabledef[$i],$this->editdef[$i]);
			}else{
				$displaydef[$i] = $this->tabledef[$i];
			}
		}
		return $displaydef;
	 }
	
	var $filterstatement = "";
	var $orderstatement = "";
	var $joinstatement = "";
	var $joinselect = "";
	var $filterfield = "";
	var $orderfield = "";
	var $filterid = "";
	var $orderdirection = "";
	var $showfilterbar = "";
	
	
	
	
	
	function setFilters(){

	
		if (isset($_REQUEST["filterstatement"]))
			$this->filterstatement=stripslashes($_REQUEST["filterstatement"]);
		if (isset($_REQUEST["orderstatement"]))
			$this->orderstatement=stripslashes($_REQUEST["orderstatement"]);
		if (isset($_REQUEST["filterfield"]))
			$this->filterfield=stripslashes($_REQUEST["filterfield"]);
		if (isset($_REQUEST["orderfield"]))
			$this->orderfield=stripslashes($_REQUEST["orderfield"]);
		if (isset($_REQUEST["orderdirection"]))
			$this->orderdirection=stripslashes($_REQUEST["orderdirection"]);
		if (isset($_REQUEST["filterid"]))
			$this->filterid=stripslashes($_REQUEST["filterid"]);

			
	}
	
	function getFilterStatement(){
		if ($this->filterid != ""){
			return " ".$this->filterfield."='".$this->filterid."' ";
		}else{
			return "";
		}
	}
	
	function getOrderStatement(){
		if ($this->orderfield != ""){
		return " ORDER BY ".$this->orderfield." ".$this->orderdirection;
		}
	}

	function getJoinStatement(){
		return " ".$this->joinstatement;
	}

	function getJoinSelect(){
		return " ".$this->joinselect;
	}		
	

	 function get_select($id = 0, $select = ""){
		global $wpdb;
		
		$table_name = $wpdb->prefix.$this->tablename;
		$columns = $this->get_tabledef();
		
		if ($select == ""){
			$select = "SELECT ";
					for ( $i = 1; $i <= sizeof($columns); $i++ ) {
						$select .= $table_name.".";
						$select .= $columns[$i]["field"];
						if ($i < sizeof($columns))
							$select .= ", ";
					}
					
					if ($this->getJoinSelect() != ""){
						$select .= $wpdb->prepare(" ".$this->getJoinSelect()." ");
					}					
					
			$select .= " FROM $table_name";
		}
		
		if ($this->getJoinStatement() != ""){
			$select .= $wpdb->prepare(" ".$this->getJoinStatement()." ");
		}
		
					
		if ($id != 0){
			$select .= $wpdb->prepare(" WHERE $table_name.ID = %d LIMIT 1", $id);
			$result =  $wpdb->get_row($select) or die ("your sql '$select' has failed");
		}else{
			if ($this->getFilterStatement() != ""){
				$select .= $wpdb->prepare(" WHERE ".$this->getFilterStatement()." ");
			}
			$select .= $wpdb->prepare($this->getOrderStatement());
			//echo $select;
			$result =  $wpdb->get_results($select) or die ("<div style='border:1px solid red;'>your sql has failed: <textarea style='width:99%;height:100px;'>$select</textarea><br/><div style='color:red'>".mysql_errno() . ": " . mysql_error()."</div></div>");
		}
		
		return $result;
	 }
	 
	 function do_delete($id){
			global $wpdb;
			$table_name = $wpdb->prefix.$this->tablename;
			if (strpos($id,",")!==false){
				$id = mysql_real_escape_string($id);
				$id = substr($id, 0, strlen($id)-1);
				$wpdb->query("DELETE FROM $table_name WHERE ID in (".$id.");");
			}else{
				$id = (int)$id;
				$wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE ID = %d LIMIT 1", $id));
			}
	 }
	 
	 function do_add_defaults(){
		global $wpdb;
		
		$columns = $this->get_editdef();	
				$select = "SELECT ";
				for ( $i = 1; $i <= sizeof($columns); $i++ ) {
					if (isset($columns[$i]["extra"]) && $columns[$i]["extra"] == "AUTO_INCREMENT")
							continue;
					if (isset($columns[$i]["default"])){
							$select .= "'".$columns[$i]["default"]."' as ".$columns[$i]["field"];
					}else{
							switch ($columns[$i]["type"]){
								case 'date':
									$select .= "'0000-00-00' as ".$columns[$i]["field"];
									break;
								default:
									$select .= "'".$columns[$i]["field"]."' as ".$columns[$i]["field"];
							}
					}
					if ($i < sizeof($columns))
						$select .= ", ";
				}

		$_post = $wpdb->get_row($wpdb->prepare($select));
		return $_post;
	}
	
	function do_update($id){
			global $wpdb;
			$table_name = $wpdb->prefix.$this->tablename;
			$columns = $this->get_editdef();	
			
			$id = (int)$id;
			$sql = 	"UPDATE $table_name set ";
			for ( $i = 1; $i <= sizeof($columns); $i++ ) {
					if (isset($columns[$i]["extra"]) && $columns[$i]["extra"] == "AUTO_INCREMENT")
							continue;
					$htmlelement = isset($columns[$i]["htmlelement"])? $columns[$i]["htmlelement"] : '';
					switch ($htmlelement){
						case "checkbox":
							if (isset($_POST[$columns[$i]["field"]])){
								$checked = ($_POST[$columns[$i]["field"]] == "on")? "1" : "0";
							}else{
								$checked = "0";
							}
							$sql .= $columns[$i]["field"]." = '$checked'";
						break;
						case "editor":
							$fieldcontent = nl2br($_POST[$columns[$i]["field"]]);
							$fieldcontent = str_replace("\r\n", "", $fieldcontent);
							$sql .= $columns[$i]["field"]." = '".$fieldcontent."'";
						break;
						default:
							//$fieldcontent = apply_filters('the_content', $_POST[$columns[$i]["field"]]);
							$fieldcontent = nl2br(htmlentities($_POST[$columns[$i]["field"]],ENT_QUOTES));
							$sql .= $columns[$i]["field"]." = '".$fieldcontent."'";
					
					}
					if ($i < sizeof($columns))
						$sql .= ", ";
			}
			$sql .= " WHERE ID = $id LIMIT 1";
			//echo $wpdb->prepare($sql, $id);
			$wpdb->query($sql);
	}
	
	function do_insert(){
			global $wpdb;
			$table_name = $wpdb->prefix.$this->tablename;
			$columns = $this->get_editdef();	
			
			
			$sql = 	"INSERT INTO $table_name ("; 
					for ( $i = 1; $i <= sizeof($columns); $i++ ) {
						if (isset($columns[$i]["extra"]) && $columns[$i]["extra"] == "AUTO_INCREMENT")
							continue;
						$sql .= $columns[$i]["field"];
						if ($i < sizeof($columns))
							$sql .= ", ";
					}
					$sql .= ")VALUES(";
					for ( $i = 1; $i <= sizeof($columns); $i++ ) {
						
						
						if (isset($columns[$i]["extra"]) && $columns[$i]["extra"] == "AUTO_INCREMENT")
							continue;
						$htmlelement = isset($columns[$i]["htmlelement"])? $columns[$i]["htmlelement"] : '';
						switch ($htmlelement){
							case "checkbox":
								if (isset($_POST[$columns[$i]["field"]])){
									$checked = ($_POST[$columns[$i]["field"]] == "on")? "1" : "0";
								}else{
									$checked = "0";
								}
								$sql .= "'$checked'";
							break;
							default:
								$sql .= "'".$_POST[$columns[$i]["field"]]."'";
						}
						if ($i < sizeof($columns))
							$sql .= ", ";						
					}
					$sql .= ");";
			//echo $sql;
			$wpdb->query($sql);
			return $wpdb->insert_id;
	}
	
	function do_install(){
	
		global  $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); 
		$debug = 0;
		
		$table_name = $wpdb->prefix.$this->tablename;
		$columns = $this->tabledef;	
	
			$sql = "CREATE TABLE ".$table_name." (";
				 
  
			for ( $i = 1; $i <= sizeof($columns); $i++ ) {
				$sql .= $columns[$i]["field"]." ";
				$sql .= $columns[$i]["type"];
				if (isset($columns[$i]["length"]))
					$sql .= "(".$columns[$i]["length"].")";
				$sql .= " ";
				if (isset($columns[$i]["null"])){
				$sql .= $columns[$i]["null"]." ";
				}
				if (isset($columns[$i]["extra"])){
				$sql .= $columns[$i]["extra"]." ";
				}
				$sql .= ",";
				if (isset($columns[$i]["extra"]) && $columns[$i]["extra"] == "AUTO_INCREMENT"){
					$key = $columns[$i]["field"];
				}
			}

			$sql .= " PRIMARY KEY  (".$key."));";
			

		echo "<textarea style='width:500px;'>";
		echo $sql;
		echo "</textarea><br/>";



		dbDelta($sql) or die ('Error connecting to mysql');;
		
			$insert = "INSERT INTO $table_name (";
			for ( $i = 1; $i <= sizeof($columns); $i++ ) {
				if (!isset($columns[$i]["extra"]) || $columns[$i]["extra"] != "AUTO_INCREMENT"){
					$insert .= $columns[$i]["field"];
					if ($i < sizeof($columns))
						$insert .= ", ";
				}
			}
			$insert .= ") VALUES (";
			for ( $i = 1; $i <= sizeof($columns); $i++ ) { 
				if (!isset($columns[$i]["extra"]) || $columns[$i]["extra"] != "AUTO_INCREMENT"){
						switch ($columns[$i]["type"]){
							case "int":
							case"tinyint":
								$insert .= "1";
								break;
							default:
								$insert .= "'".$columns[$i]["field"]."'";
						}
						if ($i < sizeof($columns))
								$insert .= ", ";
						
				}
			}
			$insert .= ");";
			
			//minimum startup
			//$insert = "INSERT INTO $table_name (title) VALUES ('test')";
		

		echo "<textarea style='width:500px;'>";
		echo $insert;
		echo "</textarea><br/>";

		$wpdb->query($insert) or die ('Error connecting to mysql');;
		add_option($this->pluginname."_db_version", $this->db_version);
	}
	
	function do_uninstall(){
		//return;
		global  $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); 
		
		$table_name = $wpdb->prefix.$this->tablename;
		
		$sql = "DROP TABLE ".$table_name.";";
		$wpdb->query($sql);
		delete_option($this->pluginname."_db_version");
		delete_option('widget_'.$this->pluginname);
	}
	
	function do_create_submenu(){
		$file = $this->file;
		$pluginname = $this->pluginname;
		$plugintitle = $this->plugintitle;
		$pluginprefix = $this->pluginprefix;
		if(function_exists('add_submenu_page')) {
			wp_enqueue_style("$pluginname",get_bloginfo('wpurl') . "/wp-content/plugins/$pluginprefix$pluginname/$pluginname.css",false," ");
			if ($this->pluginprefix != "")
			return;
			add_menu_page($this->plugintitle, $this->plugintitle, 'edit_posts', $file, $pluginname."_options", plugins_url('$pluginname/blank.png'));
			}		
	}
	
	function do_setup_tinymce(){
		add_action( 'admin_print_footer_scripts', 'wp_tiny_mce', 25 );
		wp_enqueue_script('post');
		if ( user_can_richedit() )
			wp_enqueue_script('editor');
		add_thickbox();
		wp_enqueue_script('media-upload');
		wp_enqueue_script('word-count');
		wp_enqueue_script('quicktags');	
	}
	
	function widget_pluginname($plugin_options, $args, $number = 1) {
		
		$rows = $this->get_select("widget");
		$display = $this->get_widgetdef();
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

		// Each widget can store its own options. We keep strings here.
		
		// fill options with default values if value is not set
		//$item = $options[$number];
		
		//foreach($links_options['widget_fields'] as $key => $field) {
		//	if (! isset($item[$key])) {
		//		$item[$key] = $field['default'];
		//	}
		//}
		
		//$messages = fetch_rss('http://twitter.com/statuses/user_timeline/'.$item['username'].'.rss');
		// These lines generate our output.
		echo $this->plugintitle." widget output<br/><br/>";
		//echo "Title:".$item['title'];
		echo $before_widget;
		
		foreach ( $rows as $row ) {

			for ($i = 1; $i <= sizeof($display); $i++){
				if (isset($display[$i]["header"])){
				echo $display[$i]["header"];
				echo $row->$display[$i]["field"];
				echo "<br/>";
				}
			}
			echo "<br/><br/>";

		}

		echo $after_widget;
				
	}

	function widget_pluginname_control($plugin_options, $number) {

		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_'.$this->pluginname);
		//if ( isset($_POST['twitter-submit']) ) {

			foreach($this->options['widget_fields'] as $key => $field) {
				$options[$number][$key] = $field['default'];
				$field_name = sprintf('%s_%s_%s', $plugin_options['prefix'], $key, $number);

				if ($field['type'] == 'text') {
					if (isset($_POST[$field_name])){
						$options[$number][$key] = strip_tags(stripslashes($_POST[$field_name]));
					}
				} elseif ($field['type'] == 'checkbox') {
					$options[$number][$key] = isset($_POST[$field_name]);
				}
			}

			update_option('widget_'.$this->pluginname, $options);
		//}

		foreach($this->options['widget_fields'] as $key => $field) {
			
			$field_name = sprintf('%s_%s_%s', $plugin_options['prefix'], $key, $number);
			$field_checked = '';
			if ($field['type'] == 'text') {
				$field_value = htmlspecialchars($options[$number][$key], ENT_QUOTES);
			} elseif ($field['type'] == 'checkbox') {
				$field_value = 1;
				if (! empty($options[$number][$key])) {
					$field_checked = 'checked="checked"';
				}
			}
			
			printf('<p style="text-align:right;" class="twitter_field"><label for="%s">%s <input id="%s" name="%s" type="%s" value="%s" class="%s" %s /></label></p>',
				$field_name, __($field['label']), $field_name, $field_name, $field['type'], $field_value, $field['type'], $field_checked);
		}

		//echo '<input type="hidden" id="twitter-submit" name="twitter-submit" value="1" />';
	}
	
	function widget_pluginname_setup() {
	


		$options = $newoptions = get_option('widget_'.$this->pluginname);
		update_option('widget_'.$this->pluginname, $newoptions);
		
	}
	
	function widget_pluginname_register() {
			
		$options = get_option('widget_'.$this->pluginname);
		$dims = array('width' => 300, 'height' => 300);
		$class = array('classname' => 'widget_'.$this->pluginname);

		for ($i = 1; $i <= 9; $i++) {
			$name = sprintf(__($this->plugintitle), $i);
			$id = $this->pluginname."-$i"; // Never never never translate an id
			wp_register_sidebar_widget($id, $name, $i <= $options['number'] ? 'widget_'.$this->pluginname : /* unregister */ '', $class, $i);
			wp_register_widget_control($id, $name, $i <= $options['number'] ? 'widget_'.$this->pluginname.'_control' : /* unregister */ '', $dims, $i);
		}
		
		add_action('sidebar_admin_setup', 'widget_'.$this->pluginname.'_setup');
		add_action('sidebar_admin_page', 'widget_'.$this->pluginname.'_page');
		//echo "REGISTERED";
	}
	
}


?>
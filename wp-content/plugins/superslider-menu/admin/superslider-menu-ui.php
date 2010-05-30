<?php
/*
Copyright 2008 daiv Mowbray

This file is part of SuperSlider-Menu

Fancy Categories is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

SuperSlider-Menu is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Fancy Categories; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
	
   
	if ( !current_user_can('manage_options') ) {
		// Apparently not.
		die( __( 'ACCESS DENIED: Your don\'t have permission to do this.', 'superslider-menu' ) );
		}
		if (isset($_POST['set_defaults']))  {
			check_admin_referer('ssm_options');
			$ssmOldOptions = array(
				"load_moo" => "on",
				"css_load" => "default",
				"css_theme" => "default",
				"user_objects" => "off",
				"holder" => "#ssMenuHolder",
				"toggler" => " div span.show_",
				"content" => " div.showme_",
				"toglink" => "div.subsym",
				"add_mouse" => "off",
				"always_hide" => "off",
				"opacity" => "on",
				"trans_time" => "1200",
				"trans_type" => "quad",
				"trans_typeinout" => "in:out",
				"tooltips" => "on",
				"showDelay" => '1250',
		        "hideDelay" => '2200',
		        "offsetx" => "-290",
		        "offsety" => "0",
		        "fixed" => 'true',
		        "tip_opacity" => '0.9',
		        "toolClass" => '',
		        "tipTitle" => 'title',
		        "tipText" => 'rel',
				"nav_follow" => "on",
				"nav_followspeed" => "700"
			);
			update_option('ssMenu_options', $ssmOldOptions);
				
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'SuperSlider-Menu Default Options reloaded.', 'superslider-menu' ) . '</strong></p></div>';
			
		}
		elseif ($_POST['action'] == 'update' ) {
			
			check_admin_referer('ssm_options'); // check the nonce
					// If we've updated settings, show a message
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'SuperSlider-Menu Options saved.', 'superslider-menu' ) . '</strong></p></div>';
			
			$ssmNewOptions = array(
				'user_objects'	=> $_POST['op_user_objects'],
				'holder'		=> $_POST['op_holder'],
				'toggler'		=> $_POST['op_toggler'],
				'content'		=> $_POST['op_content'],
				'toglink'		=> $_POST['op_toglink'],				
				'load_moo'		=> $_POST['op_load_moo'],
				'css_load'		=> $_POST['op_css_load'],
				'css_theme'		=> $_POST["op_css_theme"],
				'add_mouse'		=> $_POST["op_add_mouse"],
				'always_hide'	=> $_POST["op_always_hide"],
				'opacity'		=> $_POST["op_opacity"],
				'trans_time'	=> $_POST["op_trans_time"],
				'trans_type'		=> $_POST["op_trans_type"],
				'trans_typeinout'	=> $_POST["op_transtypeinout"],
				'tooltips'			=> $_POST["op_tooltips"],
				'showDelay'			=> $_POST["op_showDelay"],
				'hideDelay'			=> $_POST["op_hideDelay"],
				'offsetx'			=> $_POST["op_offsetx"],
				'offsety'			=> $_POST["op_offsety"],
				'fixed'			=> $_POST["op_fixed"],
				'tip_opacity'			=> $_POST["op_tip_opacity"],
				
				'toolClass'			=> $_POST["op_toolClass"],
				'tipTitle'			=> $_POST["op_tipTitle"],
				'tipText'			=> $_POST["op_tipText"],
				'nav_follow'		=> $_POST["op_navfollow"],
				'nav_followspeed'	=> $_POST["op_navfollowspeed"]
			);	

		update_option('ssMenu_options', $ssmNewOptions);

		}	

		$ssmNewOptions = get_option('ssMenu_options');   

	/**
	*	Let's get some variables for multiple instances
	*/
    //$trans_type = attribute_escape(get_option('ssm_trans_type'));
    
    $checked = ' checked="checked"';
    $selected = ' selected="selected"';
	$site = get_settings('siteurl'); 
?>

<div class="wrap">
<form name="ssm_options" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
<!-- possible auto save options : action="options.php" , bellow, update-options as nonce -->
<?php if ( function_exists('wp_nonce_field') )
		wp_nonce_field('ssm_options'); echo "\n"; ?>
		
<div style="">
<a href="http://wp-superslider.com/">
<img src="<?php echo $site ?>/wp-content/plugins/superslider-menu/admin/img/logo_superslider.png" style="margin-bottom: -15px;padding: 20px 20px 0px 20px;" alt="SuperSlider Logo" width="52" height="52" border="0" /></a>
  <h2 style="display:inline; position: relative;">SuperSlider-Menu Options</h2>
 </div><br style="clear:both;" />
 
  <script type="text/javascript">
// <![CDATA[

function create_ui_tabs() {


    jQuery(function() {
        var selector = '#ssslider';
            if ( typeof jQuery.prototype.selector === 'undefined' ) {
            // We have jQuery 1.2.x, tabs work better on UL
            selector += ' > ul';
        }
        jQuery( selector ).tabs({ fxFade: true, fxSpeed: 'slow' });

    });
}

jQuery(document).ready(function(){
        create_ui_tabs();
});

// ]]>
</script>
 
<div id="ssslider" class="ui-tabs">
    <ul id="ssnav" class="ui-tabs-nav">
        <li <?php if ($this->base_over_ride != "on") { 
  		 echo '';
  		} else {
  		echo 'style="display:none;"';
  		}?>	class="ui-state-default" ><a href="#fragment-1"><span>Appearance</span></a></li>
        <li class="ui-tabs-selected"><a href="#fragment-2"><span>Animation</span></a></li>
        <li class="ui-state-default"><a href="#fragment-3"><span>Tool tips</span></a></li>
        <li class="ui-state-default"><a href="#fragment-4"><span>Mouse Tracer</span></a></li>
        <li class="ui-state-default"><a href="#fragment-5"><span>Advanced</span></a></li>
        <li <?php if ($this->base_over_ride != "on") { 
  		 echo '';
  		} else {
  		echo 'style="display:none;"';
  		}?>	class="ss-state-default" ><a href="#fragment-6"><span>File storage</span></a></li>
  		
    </ul>
    
    <div id="fragment-1" class="ss-tabs-panel">
 	<div <?php if ($this->base_over_ride != "on") { 
  		 echo '';
  		} else {
  		echo 'style="display:none;"';
  		}?>	
	>
	<h3 class="title">Menu Appearance</h3>

<fieldset style="border:1px solid grey;margin:10px;padding:10px 10px 10px 30px;"><!-- Theme options start -->  	
		<legend><b><?php _e(' Themes',$excerpt_domain); ?>:</b></legend>
	<table width="100%" cellpadding="10" align="center">
	<tr>
		<td width="25%" align="center" valign="top"><img src="<?php echo $site ?>/wp-content/plugins/superslider-menu/admin/img/default.png" alt="default" border="0" width="110" height="25" /></td>
		<td width="25%" align="center" valign="top"><img src="<?php echo $site ?>/wp-content/plugins/superslider-menu/admin/img/blue.png" alt="blue" border="0" width="110" height="25" /></td>
		<td width="25%" align="center" valign="top"><img src="<?php echo $site ?>/wp-content/plugins/superslider-menu/admin/img/black.png" alt="black" border="0" width="110" height="25" /></td>
		<td width="25%" align="center" valign="top"><img src="<?php echo $site ?>/wp-content/plugins/superslider-menu/admin/img/custom.png" alt="custom" border="0" width="110" height="25" /></td>
	</tr>
	<tr>
		<td><label for="op_css_theme1">
			 <input type="radio"  name="op_css_theme" id="op_css_theme1"
			 <?php if($ssmNewOptions['css_theme'] == "default") echo $checked; ?> value="default" />
			</label>
		</td>
		<td> <label for="op_css_theme2">
			 <input type="radio"  name="op_css_theme" id="op_css_theme2"
			 <?php if($ssmNewOptions['css_theme'] == "blue") echo $checked; ?> value="blue" />
			 </label>
  		</td>
		<td><label for="op_css_theme3">
			 <input type="radio"  name="op_css_theme" id="op_css_theme3"
			 <?php if($ssmNewOptions['css_theme'] == "black") echo $checked; ?> value="black" />
			 </label>
  		</td>
		<td> <label for="op_css_theme4">
			 <input type="radio"  name="op_css_theme" id="op_css_theme4"
			 <?php if($ssmNewOptions['css_theme'] == "custom") echo $checked; ?> value="custom" />
			</label>
     </td>
	</tr>
	</table>

  </fieldset>
  </div>
</div><!-- close frag 1 -->   

<div id="fragment-2" class="ss-tabs-panel">
	<h3 class="title">Accordion animations</h3>

		<fieldset style="border:1px solid grey;margin:10px;padding:10px 10px 10px 30px;"><!-- Accordion options start -->
   <legend><b><?php _e('Accordion Options',$ssm_domain); ?>:</b></legend>
   <ul style="list-style-type: none;">
    <li>
    <optgroup label="op_add_mouse">
    	<label for="op_add_mouseoff">
    		<input type="radio" 
    		<?php if($ssmNewOptions['add_mouse'] == "off") echo $checked; ?> name="op_add_mouse" id="op_add_mouseoff" value="off"/> 
    		<?php _e('Click to Activate the Accordion Togglers (default).',$ssm_domain); ?>
    		</label>
    		<br />
    	<label for="op_add_mouseon">
     		<input type="radio"
     		<?php if($ssmNewOptions['add_mouse'] == "on") echo $checked; ?>  name="op_add_mouse" id="op_add_mouseon" value="on" />
     		<?php _e('MouseOver to Activate the Accordion Togglers.',$ssm_domain); ?>
     		</label>
     		</input>
     </optgroup>
	</li>
	<hr />
    <li>
    	<label for="op_always_hide">
    		<input type="checkbox"
    		<?php if($ssmNewOptions['always_hide'] == "on") echo $checked; ?> name="op_always_hide" id="op_always_hide" /> 
    		<?php _e('Enable close all tabs, deselect will force one top level item to always be open.',$ssm_domain); ?></label>
    </li>
    <li>
    	<label for="op_opacity">
    		<input type="checkbox"
    		<?php if($ssmNewOptions['opacity'] == "on") echo $checked; ?> name="op_opacity" id="op_opacity"/>
    		<?php _e('Apply transition to opacity as well as height.',$ssm_domain); ?></label>
    </li>
    <li>
     <label for="op_trans_time"><?php _e('Accordion transition time'); ?>:
     <input type="text" name="op_trans_time" id="op_trans_time" size="6" maxlength="6"
     value="<?php echo ($ssmNewOptions['trans_time']); ?>"/></label> 
     <small><?php _e(' In milliseconds, ie: 1000 = 1 second',$ssm_domain); ?></small>
     </li>
     <li>
     <label for="op_trans_type"><?php _e('Accordion transition type',$ssm_domain); ?>:   </label>  
     <select name="op_trans_type" id="op_trans_type">
     <option <?php if($ssmNewOptions['trans_type'] == "sine") echo $selected; ?> id="sine" value='sine'> sine</option>
     <option <?php if($ssmNewOptions['trans_type'] == "elastic") echo $selected; ?> id="elastic" value='elastic'> elastic</option>
     <option <?php if($ssmNewOptions['trans_type'] == "bounce") echo $selected; ?> id="bounce" value='bounce'> bounce</option>
     <option <?php if($ssmNewOptions['trans_type'] == "expo") echo $selected; ?> id="expo" value='expo'> expo</option>
     <option <?php if($ssmNewOptions['trans_type'] == "circ") echo $selected; ?> id="circ" value='circ'> circ</option>
     <option <?php if($ssmNewOptions['trans_type'] == "quad") echo $selected; ?> id="quad" value='quad'> quad</option>
     <option <?php if($ssmNewOptions['trans_type'] == "cubic") echo $selected; ?> id="cubic" value='cubic'> cubic</option>
     <option <?php if($ssmNewOptions['trans_type'] == "linear") echo $selected; ?> id="linear" value='linear'> linear</option>
    </select><br />
    <label for="op_transtypeinout"><?php _e('Accordion transition action.',$ssm_domain); ?></label>
    <select name="op_transtypeinout" id="op_transtypeinout">
     <option <?php if($ssmNewOptions['trans_typeinout'] == "in") echo $selected; ?> id="in" value='in'> in</option>
     <option <?php if($ssmNewOptions['trans_typeinout'] == "out") echo $selected; ?> id="out" value='out'> out</option>
     <option <?php if($ssmNewOptions['trans_typeinout'] == "in:out") echo $selected; ?> id="inout" value='in:out'> in:out</option>     
    </select>
    <small><?php _e('IN is the begginning of transition. OUT is the end of transition.',$ssm_domain); ?></small>
     </li><!-- //'quad:in:out'sine:out, elastic:out, bounce:out, expo:out, circ:out, quad:out, cubic:out, linear:out, -->
    </ul>
  </fieldset>

</div><!-- close frag 2 -->   

<div id="fragment-3" class="ss-tabs-panel">
	<h3 class="title">Tool Tips</h3>
	<p <?php if ($this->base_over_ride == "on" && $this->ssModOpOut['tooltips'] == 'on') { 
  		 echo '';
  		} else {
  		echo 'style="display:none;"';
  		}?>	
	>
<?php _e('Tooltips is being controlled by the SuperSlider <a href="admin.php?page=superslider-modules#fragment-7">modules settings panel</a>.',$ssm_domain); ?>
 </p>

    <fieldset style="border:1px solid grey;margin:10px;padding:10px 10px 10px 30px;"><!-- ToolTip options start -->
   <legend><b><?php _e('ToolTip Options',$ssm_domain); ?>:</b></legend>
   <ul style="list-style-type: none;">
    <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
    <optgroup label="op_tooltips">
    
    	<label for="op_tooltipson">
    	<input type="radio"  name="op_tooltips" id="op_tooltipson"
    	<?php if($ssmNewOptions['tooltips'] == "on") echo $checked; ?> value="on" /></input>
    	<?php _e('Tooltips turned on.'); ?> </label>
	<br />
		<label for="op_tooltipsoff">
    	<input type="radio"  name="op_tooltips" id="op_tooltipsoff"
    	<?php if($ssmNewOptions['tooltips'] == "off") echo $checked; ?> value="off" /></input>
    	<?php _e('Tooltips turned off.'); ?> </label>	
	</optgroup>
	</li>
	<li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
         <label for="op_showDelay"><?php _e('Tooltip show delay'); ?>:
         <input type="text" name="op_showDelay" id="op_showDelay" size="6" maxlength="6"
         value="<?php echo ($ssmNewOptions['showDelay']); ?>"/></label> 
         <small><?php _e(' In milliseconds, ie: 1000 = 1 second',$ssm_domain); ?></small>
     </li>
     <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
         <label for="op_hideDelay"><?php _e('Tooltip hide delay'); ?>:
         <input type="text" name="op_hideDelay" id="op_hideDelay" size="6" maxlength="6"
         value="<?php echo ($ssmNewOptions['hideDelay']); ?>"/></label> 
         <small><?php _e(' In milliseconds, ie: 1000 = 1 second',$ssm_domain); ?></small>
     </li>
     <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
         <label for="op_offsetx"><?php _e('Offset x'); ?>:
         <input type="text" name="op_offsetx" id="op_offsetx" size="4" maxlength="4"
         value="<?php echo ($ssmNewOptions['offsetx']); ?>"/></label> 
         <small><?php _e(' horizontal displacement from link, (default =  -290)',$ssm_domain); ?></small>
     <br />
         <label for="op_offsety"><?php _e('Offset y'); ?>:
         <input type="text" name="op_offsety" id="op_offsety" size="4" maxlength="4"
         value="<?php echo ($ssmNewOptions['offsety']); ?>"/></label> 
         <small><?php _e(' vertical displacement from link, (default =  0)',$ssm_domain); ?></small>
     </li>
          <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
         <label for="op_tip_opacityy"><?php _e('Tooltip Opacity'); ?>:
         <input type="text" name="op_tip_opacity" id="op_tip_opacity" size="6" maxlength="6"
         value="<?php echo ($ssmNewOptions['tip_opacity']); ?>"/></label> 
         <small><?php _e(' (default 0.9)',$ssm_domain); ?></small>
     </li>
     <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
         <label for="op_toolClass"><?php _e('Add tooltip to objects with the class name of: '); ?>
         <input type="text" name="op_toolClass" id="op_toolClass" size="15" maxlength="40"
         value="<?php echo ($ssmNewOptions['toolClass']); ?>"/></label> 
         <small><?php _e(' ',$ssm_domain); ?></small>
     
     </li>
     <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
     <label for="op_tipTitle"><?php _e('Tip title, use ',$ssm_domain); ?></label>
    <select name="op_tipTitle" id="op_tipTitle">
     <option <?php if($ssmNewOptions['tipTitle'] == "title") echo $selected; ?> id="titletitle" value='title'> title</option>
     <option <?php if($ssmNewOptions['tipTitle'] == "href") echo $selected; ?> id="titlehref" value='href'> href</option>
     <option <?php if($ssmNewOptions['tipTitle'] == "rel") echo $selected; ?> id="titlerel" value='rel'> rel</option>     
    </select>
    <small><?php _e('for the tooltip title.',$ssm_domain); ?></small>
    </li>
    
    <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
     <label for="op_tipText"><?php _e('Tip text, use',$ssm_domain); ?></label>
    <select name="op_tipText" id="op_tipText">
     <!--<option <?php if($ssmNewOptions['tipText'] == "title") echo $selected; ?> id="texttitle" value='title'> title</option>-->
     <option <?php if($ssmNewOptions['tipText'] == "href") echo $selected; ?> id="texthref" value='href'> href</option>
     <option <?php if($ssmNewOptions['tipText'] == "rel") echo $selected; ?> id="textrel" value='rel'> rel</option>     
    </select>
    <small><?php _e('for the tooltip text.',$ssm_domain); ?></small>
    </li>
    
     <li style="border-bottom:1px solid #cdcdcd; padding: 6px 0px 8px 0px;">
    <optgroup label="op_fixed">
    	<label for="op_fixedon">
     		<input type="radio"
     		<?php if($ssmNewOptions['fixed'] == "true") echo $checked; ?>  name="op_fixed" id="op_fixedon" value="true" />
     		<?php _e('Tool tip possition, fixed on. (default)',$ssm_domain); ?>
     		</label><br />
     	<label for="op_fixedoff">
    		<input type="radio" 
    		<?php if($ssmNewOptions['fixed'] == "false") echo $checked; ?> name="op_fixed" id="op_fixedoff" value="false"/> 
    		<?php _e('fixed off.',$ssm_domain); ?>
    		</label>    	
     </optgroup>
	</li>
   </ul>
  </fieldset>

</div><!-- close frag 3 -->   

<div id="fragment-4" class="ss-tabs-panel">
	<h3 class="title">Mouse Tracer</h3>
	
		 <fieldset style="border:1px solid grey;margin:10px;padding:10px 10px 10px 30px;"><!-- Mouse follower options start -->
   <legend><b><?php _e('Mouse Tracer',$ssm_domain); ?>:</b></legend>
   <ul style="list-style-type: none;">
    <li>
    	<label for="op_navfollow"><input type="checkbox" 
    	<?php if($ssmNewOptions['nav_follow'] == "on") echo $checked; ?> name="op_navfollow" id="op_navfollow"/>
    	<?php _e('Activate the Vertical Mouse Tracer.',$ssm_domain); ?></label>
    </li>
   <!-- <li><optgroup label="nav_followside">
    
		<label for="op_navfollowleft">
    	<input type="radio"  name="op_navfollowside" id="op_navfollowleft"
    	<?php if($ssmNewOptions['nav_followside'] == "left") echo $checked; ?> value="left" /></input>
    	<?php _e('Tracer travels on the left side of menu.'); ?> </label>
    	
    	<label for="op_navfollowright">
    	<input type="radio"  name="op_navfollowside" id="op_navfollowright"
    	<?php if($ssmNewOptions['nav_followside'] == "right") echo $checked; ?> value="right" /></input>
    	<?php _e('Tracer travels on the right side of menu.'); ?> </label>
    	
	</optgroup>
	</li>-->
    <li>
     <label for="op_navfollowspeed"><?php _e('Tracer Reaction speed',$ssm_domain); ?>:
     <input type="text" name="op_navfollowspeed" id="op_navfollowspeed" size="6" maxlength="6" 
     value="<?php echo ($ssmNewOptions['nav_followspeed']); ?>"/></label> 
     <small><?php _e('In milliseconds, ie: 1000 = 1 second',$ssm_domain); ?></small>
     </li>
   </ul>
  </fieldset>

</div><!-- close frag4 -->

<div id="fragment-5" class="ss-tabs-panel">
	<h3 class="title">Advanced</h3>
				<fieldset style="border:1px solid grey;margin:10px;padding:10px 10px 10px 30px;"><!-- Toggle objects options start -->  
   <legend><b><?php _e('Object Options - Advanced usage',$ssm_domain); ?>:</b></legend>
   <ul style="list-style-type: none;">
    <li>
    	<label for="op_user_objects"><input type="checkbox" 
    	<?php if($ssmNewOptions['user_objects'] == "on") echo $checked; ?> name="op_user_objects" id="op_user_objects" />
    	<?php _e('Use a different object structure.',$ssm_domain); ?></label> 
    	
	</li>
	<li>
     <label for="op_holder"><?php _e('Object holder to use',$ssm_domain); ?>:
     <input type="text" name="op_holder" id="op_holder" size="20" maxlength="50"
     value="<?php echo ($ssmNewOptions['holder']); ?>"></input></label>
     <br /><small><?php _e(' Default is #ssMenuList ',$ssm_domain); ?></small>
     </li>
    <li>
     <label for="op_toggler"><?php _e('Toggler to use',$ssm_domain); ?>:
     <input type="text" name="op_toggler" id="op_toggler" size="20" maxlength="50"
     value="<?php echo ($ssmNewOptions['toggler']); ?>"></input></label>
     <br /><small><?php _e(' Default is  dt span.show_ ',$ssm_domain); ?></small>
     </li>
    <li>
     <label for="op_content"><?php _e('Content to use',$ssm_domain); ?>:
     <input type="text" name="op_content" id="op_content" size="20" maxlength="50"
     value="<?php echo ($ssmNewOptions['content']); ?>"></input></label>
     <br /><small><?php _e(' Default is  dd.showme_ ',$ssm_domain); ?></small>
     </li>
     <li>
     <label for="op_toglink"><?php _e('Toglink to use',$ssm_domain); ?>:
     <input type="text" name="op_toglink" id="op_toglink" size="20" maxlength="50"
     value="<?php echo ($ssmNewOptions['toglink']); ?>"></input></label>
     <br /><small><?php _e(' Default is  dt a ',$ssm_domain); ?></small>
     </li>
   </ul>
  </fieldset>

  <h3><?php _e(' Use with caution ',$ssm_domain); ?></h3><p><?php _e('Selecting this option will disable the SuperSlider widgets. You can then add your own objects to apply the accordion animation effects to. You will need to create your own corresponding css objects.',$ssm_domain); ?></p>
    
</div><!-- close frag 5 -->

<div id="fragment-6" class="ss-tabs-panel">
	
	<div
<?php if ($this->base_over_ride != "on") { 
  		 echo '';
  		} else {
  		echo 'style="display:none;"';
  		}?> 
	>    
	<h3 class="title">File Storage</h3>
    <fieldset style="border:1px solid grey;margin:10px;padding:10px 10px 10px 30px;"><!-- Header files options start -->
   			<legend><b><?php _e('File Storage - Loading Options'); ?>:</b></legend>
  		 <ul style="list-style-type: none;">
    <li>
    	<label for="op_load_moo">
    	<input type="checkbox" 
    	<?php if($ssmNewOptions['load_moo'] == "on") echo $checked; ?> name="op_load_moo" id="op_load_moo" />
    	<?php _e('Load Mootools 1.2 into your theme header.',$ssm_domain); ?></label>
    	<hr />
	</li>
    <li><optgroup>
    	<label for="op_css_load1">
    	<input type="radio" name="op_css_load" id="op_css_load1"
    	<?php if($ssmNewOptions['css_load'] == "default") echo $checked; ?> value="default" />
    	<?php _e('Load css from default location. SuperSlider-Menu plugin folder.',$ssm_domain); ?></label><br />
    	<label for="op_css_load2"><input type="radio" name="op_css_load"  id="op_css_load2"
    	<?php if($ssmNewOptions['css_load'] == "pluginData") echo $checked; ?> value="pluginData" />
    	<?php _e('Load css from plugin-data folder. (Recommended)',$ssm_domain); ?></label><br />
    	<label for="op_css_load3"><input type="radio" name="op_css_load"  id="op_css_load3"
    	<?php if($ssmNewOptions['css_load'] == "off") echo $checked; ?> value="off" />
    	<?php _e('Don\'t load css, manually add to your theme css file.',$ssm_domain); ?></label>
    	</optgroup>
    </li>
    </ul>
     </fieldset>

		<div>
<?php if ($base_over_ride != "on") { 
  		 echo '<p ';
  		} else {
  		echo '<p style="display:none;">';
  		}?>
		><?php _e(' If your theme or any other plugin loads the mootools 1.2 javascript framework into your file header, you can deactivate it here.',$ssShow_domain); ?></p>
		<p><?php _e(' Via ftp, move the folder named plugin-data from this plugin folder into your wp-content folder. This is recomended to avoid over writing any changes you make to the css files when you update this plugin.',$ssShow_domain); ?>
		</p>
		</div>
	</div>
	
</div><!-- close frag 6 -->
</div><!--  close tabs -->


<p class="submit">
		<input type="submit" name="set_defaults" value="<?php _e('Reload Default Options',$ssm_domain); ?> &raquo;" />
		<input type="submit" id="update" class="button-primary" value="<?php _e('Update options',$ssm_domain); ?> &raquo;" />
		<input type="hidden" name="action" id="action" value="update" />
 	</p>
 </form
</div>
<?php
	echo "";
?>
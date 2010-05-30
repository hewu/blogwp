<script>

jQuery(document).ready(function($){
	var h = wpCookies.getHash('TinyMCE_content_size');

	if ( getUserSetting( 'editor' ) == 'html' ) {
		if ( h )
			$('#content').css('height', h.ch - 15 + 'px');
	} else {
		$('#content').css('color', 'white');
		$('#quicktags').hide();
	}
});

var switchEditors = {

	mode : '',

	I : function(e) {
		return document.getElementById(e);
	},

	edInit : function() {
	},

	saveCallback : function(el, content, body) {

		if ( tinyMCE.activeEditor.isHidden() )
			content = this.I(el).value;
		else
			content = this.pre_wpautop(content);

		return content;
	},

	pre_wpautop : function(content) {
		var blocklist1, blocklist2;

		// Protect pre|script tags
		content = content.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
			a = a.replace(/<br ?\/?>[\r\n]*/g, '<wp_temp>');
			return a.replace(/<\/?p( [^>]*)?>[\r\n]*/g, '<wp_temp>');
		});

		// Pretty it up for the source editor
		blocklist1 = 'blockquote|ul|ol|li|table|thead|tbody|tr|th|td|div|h[1-6]|p';
		content = content.replace(new RegExp('\\s*</('+blocklist1+')>\\s*', 'mg'), '</$1>\n');
		content = content.replace(new RegExp('\\s*<(('+blocklist1+')[^>]*)>', 'mg'), '\n<$1>');

		// Mark </p> if it has any attributes.
		content = content.replace(new RegExp('(<p [^>]+>.*?)</p>', 'mg'), '$1</p#>');

		// Sepatate <div> containing <p>
		content = content.replace(new RegExp('<div([^>]*)>\\s*<p>', 'mgi'), '<div$1>\n\n');

		// Remove <p> and <br />
		content = content.replace(new RegExp('\\s*<p>', 'mgi'), '');
		content = content.replace(new RegExp('\\s*</p>\\s*', 'mgi'), '\n\n');
		content = content.replace(new RegExp('\\n\\s*\\n', 'mgi'), '\n\n');
		content = content.replace(new RegExp('\\s*<br ?/?>\\s*', 'gi'), '\n');

		// Fix some block element newline issues
		content = content.replace(new RegExp('\\s*<div', 'mg'), '\n<div');
		content = content.replace(new RegExp('</div>\\s*', 'mg'), '</div>\n');
		content = content.replace(new RegExp('\\s*\\[caption([^\\[]+)\\[/caption\\]\\s*', 'gi'), '\n\n[caption$1[/caption]\n\n');
		content = content.replace(new RegExp('caption\\]\\n\\n+\\[caption', 'g'), 'caption]\n\n[caption');

		blocklist2 = 'blockquote|ul|ol|li|table|thead|tr|th|td|h[1-6]|pre';
		content = content.replace(new RegExp('\\s*<(('+blocklist2+') ?[^>]*)\\s*>', 'mg'), '\n<$1>');
		content = content.replace(new RegExp('\\s*</('+blocklist2+')>\\s*', 'mg'), '</$1>\n');
		content = content.replace(new RegExp('<li([^>]*)>', 'g'), '\t<li$1>');

		if ( content.indexOf('<object') != -1 ) {
			content = content.replace(/<object[\s\S]+?<\/object>/g, function(a){
				return a.replace(/[\r\n]+/g, '');
			});
		}

		// Unmark special paragraph closing tags
		content = content.replace(new RegExp('</p#>', 'g'), '</p>\n');
		content = content.replace(new RegExp('\\s*(<p [^>]+>.*</p>)', 'mg'), '\n$1');

		// Trim whitespace
		content = content.replace(new RegExp('^\\s*', ''), '');
		content = content.replace(new RegExp('[\\s\\u00a0]*$', ''), '');

		// put back the line breaks in pre|script
		content = content.replace(/<wp_temp>/g, '\n');

		// Hope.
		return content;
	},

	go : function(id, mode) {
		id = id || 'content';
		mode = mode || this.mode || '';

		var ed, qt = this.I('quicktags'), H = this.I('edButtonHTML'), P = this.I('edButtonPreview'), ta = this.I(id);

		try { ed = tinyMCE.get(id); }
		catch(e) { ed = false; }

		if ( 'tinymce' == mode ) {
			if ( ed && ! ed.isHidden() )
				return false;

			setUserSetting( 'editor', 'tinymce' );
			this.mode = 'html';

			P.className = 'active';
			H.className = '';
			edCloseAllTags(); // :-(
			qt.style.display = 'none';

			ta.value = this.wpautop(ta.value);

			if ( ed ) {
				ed.show();
			} else {
				try{tinyMCE.execCommand("mceAddControl", false, id);}
				catch(e){}
			}
		} else {
			setUserSetting( 'editor', 'html' );
			ta.style.color = '#000';
			this.mode = 'tinymce';
			H.className = 'active';
			P.className = '';

			if ( ed && !ed.isHidden() ) {
				ta.style.height = ed.getContentAreaContainer().offsetHeight + 24 + 'px';
				ed.hide();
			}

			qt.style.display = 'block';
		}
		return false;
	},

	wpautop : function(pee) {
		var blocklist = 'table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6]';

		if ( pee.indexOf('<object') != -1 ) {
			pee = pee.replace(/<object[\s\S]+?<\/object>/g, function(a){
				return a.replace(/[\r\n]+/g, '');
			});
		}

		pee = pee.replace(/<[^<>]+>/g, function(a){
			return a.replace(/[\r\n]+/g, ' ');
		});

		pee = pee + "\n\n";
		pee = pee.replace(new RegExp('<br />\\s*<br />', 'gi'), "\n\n");
		pee = pee.replace(new RegExp('(<(?:'+blocklist+')[^>]*>)', 'gi'), "\n$1");
		pee = pee.replace(new RegExp('(</(?:'+blocklist+')>)', 'gi'), "$1\n\n");
		pee = pee.replace(new RegExp("\\r\\n|\\r", 'g'), "\n");
		pee = pee.replace(new RegExp("\\n\\s*\\n+", 'g'), "\n\n");
		pee = pee.replace(new RegExp('([\\s\\S]+?)\\n\\n', 'mg'), "<p>$1</p>\n");
		pee = pee.replace(new RegExp('<p>\\s*?</p>', 'gi'), '');
		pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')[^>]*>)\\s*</p>', 'gi'), "$1");
		pee = pee.replace(new RegExp("<p>(<li.+?)</p>", 'gi'), "$1");
		pee = pee.replace(new RegExp('<p>\\s*<blockquote([^>]*)>', 'gi'), "<blockquote$1><p>");
		pee = pee.replace(new RegExp('</blockquote>\\s*</p>', 'gi'), '</p></blockquote>');
		pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')[^>]*>)', 'gi'), "$1");
		pee = pee.replace(new RegExp('(</?(?:'+blocklist+')[^>]*>)\\s*</p>', 'gi'), "$1");
		pee = pee.replace(new RegExp('\\s*\\n', 'gi'), "<br />\n");
		pee = pee.replace(new RegExp('(</?(?:'+blocklist+')[^>]*>)\\s*<br />', 'gi'), "$1");
		pee = pee.replace(new RegExp('<br />(\\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)', 'gi'), '$1');
		pee = pee.replace(new RegExp('(?:<p>|<br ?/?>)*\\s*\\[caption([^\\[]+)\\[/caption\\]\\s*(?:</p>|<br ?/?>)*', 'gi'), '[caption$1[/caption]');

		// Fix the pre|script tags
		pee = pee.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
			a = a.replace(/<br ?\/?>[\r\n]*/g, '\n');
			return a.replace(/<\/?p( [^>]*)?>[\r\n]*/g, '\n');
		});

		return pee;
	}
};


</script>



<?php
/**
 * Post advanced form for inclusion in the administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');


require_once('admin-header.php');

?>

<div class="wrap">

<div id="<?php echo $this_table_obj->pluginname?>_icon32" class="icon32"><br/></div>
<h2><?php echo esc_html( $title ); ?></h2>


<form name="post" action="admin.php?page=<?php echo $this_table_obj->pluginprefix.$this_table_obj->pluginname?>/<?php echo $this_table_obj->pluginname?>.php&myaction=update" method="post" id="post">
<?php

$columns = $this_table_obj->get_editdef();

?>

	<input type="hidden" id="post" name="post" value="<?php echo (int) $post_ID ?>" />
	<?php writeFilters($this_table_obj); ?>


<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
<div id="side-info-column" class="inner-sidebar">

<?php do_action('submitpost_box'); ?>

<?php $side_meta_boxes = do_meta_boxes('post', 'side', $post); ?>
</div>

<div id="post-body">
<div id="post-body-content">


<!--*************************************************-->

<?php
	
	
	echo "<table>";
		
	for ( $i = 1; $i <= sizeof($columns); $i++ ) {

		if (isset($columns[$i]["extra"]) && $columns[$i]["extra"] == "AUTO_INCREMENT" )
			continue;
		
		$colspan = isset($columns[$i]["colspan"])? " colspan=".$columns[$i]["colspan"] : '';
		$width = isset($columns[$i]["width"])? " width=".$columns[$i]["width"] : '';
	
		if (isset($columns[$i]["row"])){
			
			if (isset($columns[$i]["start"])){
				echo "<tr><td$colspan$width>";
			}else{
				echo "<td$colspan$width>";
			}
		
		}else{
			echo "<tr><td$colspan$width>";
		}
			
		$htmlelement = isset($columns[$i]["htmlelement"])? $columns[$i]["htmlelement"] : "default";
			
		switch ($htmlelement){
			case "editor";
?>
			<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
			<?php 
			jcl_the_editor($post->$columns[$i]["field"], $columns[$i]["field"], $columns[$i]["field"],true);
			?>
			<table id="post-status-info" cellspacing="0"><tbody><tr>
				<td id="wp-word-count"></td>
				<td class="autosave-info">
				<span id="autosave">&nbsp;</span>
				</td>
			</tr></tbody></table>

			</div>


<?php		
			break;
			case "checkbox";
?>
				<span class="jcl_label"><?php echo $columns[$i]["header"]?></span>
				<?php $checked = ($post->$columns[$i]["field"] == "1")? "checked='true'" : '';?>
				<input type="checkbox" name="<?php echo $columns[$i]["field"]?>" size="30" tabindex="1" <?php echo $checked?>/>

<?php			
			break;
			case "textarea";
			
				$textcontent = $post->$columns[$i]["field"];
				$textcontent = str_replace("<p>","",$textcontent);
				$textcontent = str_replace("</p>","",$textcontent);
				$textcontent = str_replace("<br/>","\r\n",$textcontent);
				$textcontent = str_replace("<br />","\r\n",$textcontent);
?>
				<div class="jcl_label"><?php echo $columns[$i]["header"]?></div>
				<textarea name="<?php echo $columns[$i]["field"]?>" size="30" tabindex="1" style="width:100%"><?php echo $textcontent?></textarea>

<?php			
			break;
			case "select":
					$query = "SELECT * FROM ".$wpdb->prefix.str_replace("_ID","",$columns[$i]["field"]);
					if (isset($columns[$i]["sort"])){
						$query .= " ORDER BY ".$columns[$i]["sort"];
					}	
					$options =  $wpdb->get_results($wpdb->prepare($query));

?>
				<div class="jcl_label"><?php echo $columns[$i]["header"]?></div>
<?php
				echo "<select name='".$columns[$i]["field"]."'>";
				echo "<option value='0'></option>";
				foreach ($options as $option){
					if ($post->$columns[$i]["field"] == $option->ID || $this_table_obj->filterfield == $columns[$i]["field"] && $this_table_obj->filterid == $option->ID){
					echo "<option value='".$option->ID."' selected='true'>".$option->$columns[$i]["selectfield"]."</option>";
					}else{
					echo "<option value='".$option->ID."'>".$option->$columns[$i]["selectfield"]."</option>";
					}
				}
				echo "</select>";
			break;
			case "default":
?>
	<div class="jcl_label"><?php echo $columns[$i]["header"]?></div>
	<input type="text" style="width:100%" name="<?php echo $columns[$i]["field"]?>" value="<?php echo esc_attr( htmlspecialchars( $post->$columns[$i]["field"] ) ); ?>" id="titlex" autocomplete="off" />

<?php
		}
		
		$cellafter = isset($columns[$i]["cellafter"])? "<td>&nbsp;</td>" : '';
		if (isset($columns[$i]["row"])){
			if (isset($columns[$i]["end"])){
				echo "</td>$cellafter</tr>";
			}else{
				echo "</td>$cellafter";
			}		
		}else{
			echo "</td>$cellafter</tr>";
		}
	}

	echo "</table>";
	
?>
			<br/>
			<span id="">
				<input class="button-primary" type="submit" name="cancelbutton" value="Cancel"/>
				<input class="button-primary" type="submit" value="Save"/>
				<input class="button-primary" type="submit" name="finishbutton" value="Finish"/>
			</span>	

</div>




<!--************************************************-->
</div><!--post-body-->
</div><!--post-body-content-->

<br class="clear" />
</div><!-- /poststuff -->
</form>
</div>

<?php wp_comment_reply(); ?>

<?php if ((isset($post->post_title) && '' == $post->post_title) || (isset($_GET['message']) && 2 > $_GET['message'])) : ?>
<script type="text/javascript">
try{document.post.title.focus();}catch(e){}
</script>
<?php endif; 


function jcl_the_editor($content, $id = 'content', $prev_id = 'title', $media_buttons = true, $tab_index = 2) {
	$rows = get_option('default_post_edit_rows');
	if (($rows < 3) || ($rows > 100))
		$rows = 12;


	$richedit =  true; //user_can_richedit();
	$class = '';

	if ( true ) { ?>
	<div id="editor-toolbar">
<?php
	if ( true ) {
		$wp_default_editor = wp_default_editor(); ?>
		<div class="zerosize"><input accesskey="e" type="button" onclick="switchEditors.go('<?php echo $id; ?>')" /></div>
<?php	if ( 'html' == $wp_default_editor ) {
			add_filter('the_editor_content', 'wp_htmledit_pre'); ?>
			<a id="edButtonHTML" class="active hide-if-no-js" onclick="switchEditors.go('<?php echo $id; ?>', 'html');"><?php _e('HTML'); ?></a>
			<a id="edButtonPreview" class="hide-if-no-js" onclick="switchEditors.go('<?php echo $id; ?>', 'tinymce');"><?php _e('Visual'); ?></a>
<?php	} else {
			$class = " class='theEditor'";
			add_filter('the_editor_content', 'wp_richedit_pre'); ?>
			<a id="edButtonHTML" class="hide-if-no-js" onclick="switchEditors.go('<?php echo $id; ?>', 'html');"><?php _e('HTML'); ?></a>
			<a id="edButtonPreview" class="active hide-if-no-js" onclick="switchEditors.go('<?php echo $id; ?>', 'tinymce');"><?php _e('Visual'); ?></a>
<?php	}
	}

	if ( true ) { ?>
	
		<div id="media-buttons" class="hide-if-no-js">
		
<?php	
//here are the Upload/Insert buttons top line
do_action( 'media_buttons' ); 
?>
		</div>
<?php
	} ?>
	</div>
<?php
	}
?>
	<div id="quicktags"><?php
	//Quicktags don't seem to do much
	
	wp_print_scripts( 'quicktags' ); ?>
	
	<script type="text/javascript">
		document.write('<div id="ed_toolbar">');
		 for (var i = 0; i < edButtons.length; i++) {
				edShowButton(edButtons[i], i);
		 }
		document.write('<input type="button" id="ed_spell" class="ed_button" onclick="edSpell(edCanvas);" title="' + quicktagsL10n.dictionaryLookup + '" value="' + quicktagsL10n.lookup + '" />');
		document.write('<input type="button" id="ed_close" class="ed_button" onclick="edCloseAllTags();" title="' + quicktagsL10n.closeAllOpenTags + '" value="' + quicktagsL10n.closeTags + '" />');

		document.write('</div>');
	
	</script>
	</div>

<?php
	$the_editor = apply_filters('the_editor', "<div id='editorcontainer'><textarea rows='$rows'$class cols='40' name='$id' tabindex='$tab_index' id='$id'>%s</textarea></div>\n");
	$the_editor_content = apply_filters('the_editor_content', $content);
	printf($the_editor, $the_editor_content);
?>
	<script type="text/javascript">
	edCanvas = document.getElementById('<?php echo $id; ?>');
	</script>
<?php
}
?>

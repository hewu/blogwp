<?php if (isset($_GET['settings']) && $_GET['settings'] == "saved") { ?>
<div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);"><p><strong><?php _e("Settings saved.", "gd-press-tools"); ?></strong></p></div>
<?php } ?>

<div class="gdsr"><div class="wrap">
<form method="post">
<h2 class="gdptlogopage">GD Press Tools: <?php _e("Settings", "gd-press-tools"); ?></h2>
<?php gdpt_upgrade_notice(); ?>
<div id="gdpt_tabs" class="gdpttabs">
<ul>
    <li><a href="#fragment-1"><span><?php _e("Integration", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-6"><span><?php _e("Short URL", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-7"><span><?php _e("Widgets", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-2"><span><?php _e("Security", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-3"><span><?php _e("Administration", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-5"><span><?php _e("Tracking", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-0"><span><?php _e("Debug", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-4"><span><?php _e("RSS", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-9"><span><?php _e("PHP", "gd-press-tools"); ?></span></a></li>
    <?php if ($user_level > 8) { ?>
    <li><a href="#fragment-11"><span><?php _e("Access", "gd-press-tools"); ?></span></a></li>
    <?php } ?>
</ul>
<div style="clear: both"></div>
<div id="fragment-1">
<?php include PRESSTOOLS_PATH."modules/settings/integration.php"; ?>
</div>
<div id="fragment-6">
<?php include PRESSTOOLS_PATH."modules/settings/shorturl.php"; ?>
</div>
<div id="fragment-7">
<?php include PRESSTOOLS_PATH."modules/settings/widgets.php"; ?>
</div>
<div id="fragment-2">
<?php include PRESSTOOLS_PATH."modules/settings/security.php"; ?>
</div>
<div id="fragment-3">
<?php include PRESSTOOLS_PATH."modules/settings/administration.php"; ?>
</div>
<div id="fragment-5">
<?php include PRESSTOOLS_PATH."modules/settings/tracking.php"; ?>
</div>
<div id="fragment-0">
<?php include PRESSTOOLS_PATH."modules/settings/debug.php"; ?>
</div>
<div id="fragment-4">
<?php include PRESSTOOLS_PATH."modules/settings/rss.php"; ?>
</div>
<div id="fragment-9">
<?php include PRESSTOOLS_PATH."modules/settings/php.php"; ?>
</div>
<?php if ($user_level > 8) { ?>
<div id="fragment-11">
<?php include PRESSTOOLS_PATH."modules/settings/access.php"; ?>
</div>
<?php } ?>
</div>

<input type="submit" class="inputbutton" value="<?php _e("Save Settings", "gd-press-tools"); ?>" name="gdpt_saving"/>
</form>
</div></div>
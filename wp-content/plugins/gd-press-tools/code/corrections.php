
<?php if ($this->admin_plugin && $this->admin_plugin_page != "settings" && $this->admin_plugin_page != "server") { ?>
<script type="text/javascript">
jQuery(document).ready(function() {
    var htmlColor = jQuery("html").css("background-color");
    jQuery(".form-table td").css("border-bottom-color", htmlColor);
    jQuery(".form-table th").css("border-bottom-color", htmlColor);
});
</script>
<?php } ?>

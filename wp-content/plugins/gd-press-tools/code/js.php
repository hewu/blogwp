<script type="text/javascript">
function areYouSure() {
    return confirm("<?php _e("Are you sure? Operation is not reversible.", "gd-press-tools"); ?>");
}
function areYouSureSimple() {
    return confirm("<?php _e("Are you sure?", "gd-press-tools"); ?>");
}
jQuery(document).ready(function() {
    jQuery("#gdpt_tabs<?php echo $this->wp_version < 28 ? ' > ul' : ''; ?>").tabs({fx: {height: "toggle"}});
});
</script>

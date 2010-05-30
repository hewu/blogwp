<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Filter by category", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><input class="widefat gdsw-input-text" id="<?php echo $this->get_field_id('filter_category'); ?>" name="<?php echo $this->get_field_name('filter_category'); ?>" type="text" value="<?php echo $instance["filter_category"]; ?>" />
        <br />
            <span class="gdsw-info">
            <?php _e("comma separated list of category ids", "gd-simple-widgets"); ?>.<br />
            <?php _e("leave empty to include all categories", "gd-simple-widgets"); ?>.
            </span></td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Minimum published posts", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><input class="widefat gdsw-input-number" id="<?php echo $this->get_field_id('filter_min_posts'); ?>" name="<?php echo $this->get_field_name('filter_min_posts'); ?>" type="text" value="<?php echo $instance["filter_min_posts"]; ?>" /></td>
    </tr>
</table>
<div class="gdsw-table-split"></div>

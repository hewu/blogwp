<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Filter by post publish date", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
        <select class="widefat gdsw-input-text" id="<?php echo $this->get_field_id('filter_published'); ?>" name="<?php echo $this->get_field_name('filter_published'); ?>">
            <option value="allp"<?php echo $instance['filter_published'] == 'allp' ? ' selected="selected"' : ''; ?>><?php _e("All posts", "gd-simple-widgets"); ?></option>
            <option value="lwek"<?php echo $instance['filter_published'] == 'lwek' ? ' selected="selected"' : ''; ?>><?php _e("Last week", "gd-simple-widgets"); ?></option>
            <option value="lmnt"<?php echo $instance['filter_published'] == 'tmnt' ? ' selected="selected"' : ''; ?>><?php _e("Last month", "gd-simple-widgets"); ?></option>
            <option value="lyea"<?php echo $instance['filter_published'] == 'tyea' ? ' selected="selected"' : ''; ?>><?php _e("Last year", "gd-simple-widgets"); ?></option>
        </select>
        </td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Filter by category", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><input class="widefat gdsw-input-text" id="<?php echo $this->get_field_id('filter_category'); ?>" name="<?php echo $this->get_field_name('filter_category'); ?>" type="text" value="<?php echo $instance["filter_category"]; ?>" />
        <br />
            <span class="gdsw-info">
            <?php _e("comma separated list of category ids", "gd-simple-widgets"); ?>.<br />
            <?php _e("leave empty to include all categories", "gd-simple-widgets"); ?>.
            </span></td>
    </tr>
</table>
<div class="gdsw-table-split"></div>

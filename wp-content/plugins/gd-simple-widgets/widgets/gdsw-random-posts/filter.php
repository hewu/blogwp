<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Filter by date", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
        <select class="widefat gdsw-input-text" id="<?php echo $this->get_field_id('filter_recency'); ?>" name="<?php echo $this->get_field_name('filter_recency'); ?>">
            <option value="allp"<?php echo $instance['filter_recency'] == 'allp' ? ' selected="selected"' : ''; ?>><?php _e("All time", "gd-simple-widgets"); ?></option>
            <option value="lday"<?php echo $instance['filter_recency'] == 'lday' ? ' selected="selected"' : ''; ?>><?php _e("Last 24 hours", "gd-simple-widgets"); ?></option>
            <option value="lwek"<?php echo $instance['filter_recency'] == 'lwek' ? ' selected="selected"' : ''; ?>><?php _e("Last week", "gd-simple-widgets"); ?></option>
            <option value="lmnt"<?php echo $instance['filter_recency'] == 'tmnt' ? ' selected="selected"' : ''; ?>><?php _e("Last month", "gd-simple-widgets"); ?></option>
            <option value="lyea"<?php echo $instance['filter_recency'] == 'tyea' ? ' selected="selected"' : ''; ?>><?php _e("Last year", "gd-simple-widgets"); ?></option>
        </select>
        </td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Filter by type", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
        <select class="widefat gdsw-input-text" id="<?php echo $this->get_field_id('filter_type'); ?>" name="<?php echo $this->get_field_name('filter_type'); ?>">
            <option value="postpage"<?php echo $instance['filter_type'] == 'postpage' ? ' selected="selected"' : ''; ?>><?php _e("Both posts and pages", "gd-simple-widgets"); ?></option>
            <option value="post"<?php echo $instance['filter_type'] == 'post' ? ' selected="selected"' : ''; ?>><?php _e("Only posts", "gd-simple-widgets"); ?></option>
            <option value="page"<?php echo $instance['filter_type'] == 'page' ? ' selected="selected"' : ''; ?>><?php _e("Only pages", "gd-simple-widgets"); ?></option>
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

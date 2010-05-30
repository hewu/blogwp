<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Filter by type", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <select class="gdsw-input-text" id="<?php echo $this->get_field_id('filter_type'); ?>" name="<?php echo $this->get_field_name('filter_type'); ?>">
                <option value="comm"<?php echo $instance['filter_type'] == 'comm' ? ' selected="selected"' : ''; ?>><?php _e("Comments only", "gd-simple-widgets"); ?></option>
                <option value="ping"<?php echo $instance['filter_type'] == 'ping' ? ' selected="selected"' : ''; ?>><?php _e("Pings only", "gd-simple-widgets"); ?></option>
                <option value="both"<?php echo $instance['filter_type'] == 'both' ? ' selected="selected"' : ''; ?>><?php _e("Comments and pings", "gd-simple-widgets"); ?></option>
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

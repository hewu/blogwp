<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Use to get relations", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <select class="widefat gdsw-input-text" id="<?php echo $this->get_field_id('filter_related'); ?>" name="<?php echo $this->get_field_name('filter_related'); ?>">
                <option<?php echo $instance["filter_related"] == "tagcat" ? ' selected="selected"' : ''; ?> value="tagcat"><?php _e("Tags and Categories", "gd-simple-widgets"); ?></option>
                <option<?php echo $instance["filter_related"] == "cat" ? ' selected="selected"' : ''; ?> value="cat"><?php _e("Categories only", "gd-simple-widgets"); ?></option>
                <option<?php echo $instance["filter_related"] == "tag" ? ' selected="selected"' : ''; ?> value="tag"><?php _e("Tags only", "gd-simple-widgets"); ?></option>
            </select>
        </td>
    </tr>
</table>
<div class="gdsw-table-split"></div>

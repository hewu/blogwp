<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Posts to show", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><input class="widefat gdsw-input-number" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $instance["count"]; ?>" /></td>
    </tr>
</table>
<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Hide widget if no results found", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <input <?php echo $instance['hide_empty'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id('hide_empty'); ?>" name="<?php echo $this->get_field_name('hide_empty'); ?>" />
        </td>
    </tr>
</table>
<div class="gdsw-table-split"></div>

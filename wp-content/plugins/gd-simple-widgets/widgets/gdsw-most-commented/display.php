<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Additional CSS class", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><input class="widefat gdsw-input-text" id="<?php echo $this->get_field_id('display_css'); ?>" name="<?php echo $this->get_field_name('display_css'); ?>" type="text" value="<?php echo $instance["display_css"]; ?>" /></td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Show comments count", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <input <?php echo $instance['display_comments_count'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id('display_comments_count'); ?>" name="<?php echo $this->get_field_name('display_comments_count'); ?>" />
        </td>
    </tr>
</table>

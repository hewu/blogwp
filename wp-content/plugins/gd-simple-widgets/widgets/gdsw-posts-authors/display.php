<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Additional CSS class", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><input class="widefat gdsw-input-text" id="<?php echo $this->get_field_id('display_css'); ?>" name="<?php echo $this->get_field_name('display_css'); ?>" type="text" value="<?php echo $instance["display_css"]; ?>" /></td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Display author's gravatar", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <input <?php echo $instance['display_gravatar'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id('display_gravatar'); ?>" name="<?php echo $this->get_field_name('display_gravatar'); ?>" />
        </td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Gravatar dimension (px)", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><input class="widefat gdsw-input-number" id="<?php echo $this->get_field_id('display_gravatar_size'); ?>" name="<?php echo $this->get_field_name('display_gravatar_size'); ?>" type="text" value="<?php echo $instance["display_gravatar_size"]; ?>" /></td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Show posts count", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <input <?php echo $instance['display_posts_count'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id('display_posts_count'); ?>" name="<?php echo $this->get_field_name('display_posts_count'); ?>" />
        </td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Show author's full name", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <input <?php echo $instance['display_full_name'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id('display_full_name'); ?>" name="<?php echo $this->get_field_name('display_full_name'); ?>" />
        </td>
    </tr>
</table>

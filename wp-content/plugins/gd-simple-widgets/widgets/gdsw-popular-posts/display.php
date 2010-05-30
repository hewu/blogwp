<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Additional CSS class", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><input class="widefat gdsw-input-text" id="<?php echo $this->get_field_id('display_css'); ?>" name="<?php echo $this->get_field_name('display_css'); ?>" type="text" value="<?php echo $instance["display_css"]; ?>" /></td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Show number of views", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <input <?php echo $instance['display_views'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id('display_views'); ?>" name="<?php echo $this->get_field_name('display_views'); ?>" />
        </td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Short excerpt from the post", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <input <?php echo $instance['display_excerpt'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id('display_excerpt'); ?>" name="<?php echo $this->get_field_name('display_excerpt'); ?>" />
        </td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Excerpt words limit", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><input class="widefat gdsw-input-number" id="<?php echo $this->get_field_id('display_excerpt_length'); ?>" name="<?php echo $this->get_field_name('display_excerpt_length'); ?>" type="text" value="<?php echo $instance["display_excerpt_length"]; ?>" /></td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Display post date", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <input <?php echo $instance['display_post_date'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id('display_post_date'); ?>" name="<?php echo $this->get_field_name('display_post_date'); ?>" />
        </td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Post date format", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><input class="widefat gdsw-input-number" id="<?php echo $this->get_field_id('display_post_date_format'); ?>" name="<?php echo $this->get_field_name('display_post_date_format'); ?>" type="text" value="<?php echo $instance["display_post_date_format"]; ?>" /></td>
    </tr>
</table>

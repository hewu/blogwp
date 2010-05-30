<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Title", "gd-simple-widgets"); ?>:</td>
        <td class="tdright">
            <input class="widefat gdsw-input-text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance["title"]; ?>" />
            <br />
            <span class="gdsw-info">
            <?php _e("leave empty to skip rendering title", "gd-simple-widgets"); ?>
            </span>
        </td>
    </tr>
</table>
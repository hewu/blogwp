<table class="gdsw-table">
    <tr>
        <td class="tdleft"><?php _e("Ad left", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><textarea class="widefat gdsw-input-area" id="<?php echo $this->get_field_id('ad_001'); ?>" name="<?php echo $this->get_field_name('ad_001'); ?>"><?php echo wp_specialchars($instance["ad_001"]); ?></textarea></td>
    </tr>
    <tr>
        <td class="tdleft"><?php _e("Ad right", "gd-simple-widgets"); ?>:</td>
        <td class="tdright"><textarea class="widefat gdsw-input-area" id="<?php echo $this->get_field_id('ad_002'); ?>" name="<?php echo $this->get_field_name('ad_002'); ?>"><?php echo wp_specialchars($instance["ad_002"]); ?></textarea></td>
    </tr>
</table>
<div class="gdsw-table-split"></div>

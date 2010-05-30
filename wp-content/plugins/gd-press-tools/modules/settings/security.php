<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Header", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="remove_wp_version" id="remove_wp_version"<?php if ($options["remove_wp_version"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_dashboard"><?php _e("Remove WordPress version.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="remove_rds" id="remove_rds"<?php if ($options["remove_rds"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_dashboard"><?php _e("Remove Really Simple Discovery link from header.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="remove_wlw" id="remove_wlw"<?php if ($options["remove_wlw"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_dashboard"><?php _e("Remove Windows Live Writer link from header.", "gd-press-tools"); ?></label>
    </td>
</tr>
<tr><th scope="row"><?php _e("Login", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="remove_login_error" id="remove_login_error"<?php if ($options["remove_login_error"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_dashboard"><?php _e("Remove error messages from login screen.", "gd-press-tools"); ?></label>
    </td>
</tr>
<tr><th scope="row"><?php _e("Authorization", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="auth_require_login" id="auth_require_login"<?php if ($options["auth_require_login"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="auth_require_login"><?php _e("Prevent website access if the user is not logged in.", "gd-press-tools"); ?></label>
    </td>
</tr>
<tr><th scope="row"><?php _e("Plugin Update", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="update_report_usage" id="update_report_usage"<?php if ($options["update_report_usage"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="update_report_usage"><?php _e("Report basic usage data that will be used for statistical purposes only.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <?php _e("This report will include your WordPress version and website URL. Report will be sent only when plugin needs to be updated.", "gd-press-tools"); ?>
    </td>
</tr>
</tbody></table>

<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Revisions", "gd-press-tools"); ?></th>
    <td>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="400"><?php _e("Number of revisions to save", "gd-press-tools"); ?>:</td>
                <td><input type="text" name="revisions_to_save" id="revisions_to_save" value="<?php echo $options["revisions_to_save"]; ?>" style="width: 100px; text-align: right;" /></td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <strong>-1:</strong> <?php _e("to store every revision", "gd-press-tools"); ?><br />
        <strong>0:</strong> <?php _e("to store only one autosave revision", "gd-press-tools"); ?>
    </td>
</tr>
<tr><th scope="row"><?php _e("Updates", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="updates_disable_core" id="updates_disable_core"<?php if ($options["updates_disable_core"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="updates_disable_core"><?php _e("Disable WordPress core auto update check.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="updates_disable_plugins" id="updates_disable_plugins"<?php if ($options["updates_disable_plugins"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="updates_disable_plugins"><?php _e("Disable plugins auto upgrade check.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="updates_disable_themes" id="updates_disable_themes"<?php if ($options["updates_disable_themes"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="updates_disable_plugins"><?php _e("Disable themes auto upgrade check.", "gd-press-tools"); ?> <strong>[<?php _e("WP 2.8 and higher.", "gd-press-tools"); ?>]</strong></label>
    </td>
</tr>
<tr><th scope="row"><?php _e("Misc", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="disable_flash_uploader" id="disable_flash_uploader"<?php if ($options["disable_flash_uploader"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="disable_flash_uploader"><?php _e("Disable Flash uploader.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="disable_auto_save" id="disable_auto_save"<?php if ($options["disable_auto_save"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="disable_auto_save"><?php _e("Disable post auto save.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="enable_db_autorepair" id="enable_db_autorepair"<?php if ($options["enable_db_autorepair"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="enable_db_autorepair"><?php _e("Enable database auto repair feature.", "gd-press-tools"); ?> <strong>[<?php _e("WP 2.9 and higher.", "gd-press-tools"); ?>]</strong></label>
    </td>
</tr>
<tr><th scope="row"><?php _e("Footer Stats", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="footer_stats" id="footer_stats"<?php if ($options["footer_stats"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="footer_stats"><?php _e("Display page loading stats in the footer.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <?php _e("This will show page loading statistics data (executed queries, load time and used memory) in the admin page footer.", "gd-press-tools"); ?>
    </td>
</tr>
</tbody></table>

<form method="post">
<div id="gdpt_server" class="postbox gdrgrid frontright">
    <h3 class="hndle"><span><?php _e("Available Widgets", "gd-simple-widgets"); ?></span></h3>
    <div class="inside">
        <p class="sub"><?php _e("Unchecked widgets will not be available on the Widgets panel.", "gd-simple-widgets"); ?></p>
        <div class="table">
            <table><tbody>
                <tr class="first">
                    <td class="first b">gdSW Recent Comments</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_recent_comments"] == 1) echo " checked"; ?> type="checkbox" name="widgets_recent_comments" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Related Posts</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_related_posts"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_related_posts" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Recent Posts</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_recent_posts"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_recent_posts" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Most Commented</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_most_commented"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_most_commented" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Posts Authors</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_posts_authors"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_posts_authors" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Future Posts</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_future_posts"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_future_posts" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Random Posts</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_random_posts"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_random_posts" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Simple 125 Ads</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_simple_125_ads"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_simple_125_ads" /></td>
                </tr>
                <tr>
                    <td class="first b<?php if ($options["lock_popular_posts"] == 1) echo " disabled"; ?>">gdSW Popular Posts</td>
                    <td class="t" style="text-align: right;"><?php if ($options["lock_popular_posts"] == 1) { ?>
                        <a href="http://www.dev4press.com/plugins/gd-press-tools/" target="_blank">GD Press Tools</a>
                    <?php _e("is required.", "gd-simple-widgets"); } ?></td>
                    <td class="t options"><input<?php if ($options["widgets_popular_posts"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_popular_posts"<?php if ($options["lock_popular_posts"] == 1) echo " disabled"; ?> /></td>
                </tr>
            </tbody></table>
        </div>
        <div style="text-align: right">
        <input style="width: 80px; cursor: pointer;" type="submit" name="widgets_saving" value="<?php _e("Save", "gd-simple-widgets"); ?>">
        </div>
    </div>
</div>
<div id="gdpt_server" class="postbox gdrgrid frontright">
    <h3 class="hndle"><span><?php _e("Default WordPress Widgets", "gd-simple-widgets"); ?></span></h3>
    <div class="inside">
        <p class="sub"><?php _e("Unchecked widgets will not be available on the Widgets panel.", "gd-simple-widgets"); ?></p>
        <div class="table">
            <table><tbody>
                <tr class="first">
                    <td class="first b">Recent Comments</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["default_recent_comments"] == 1) echo " checked"; ?> type="checkbox" name="default_recent_comments" /></td>
                </tr>
                <tr>
                    <td class="first b">Recent Posts</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["default_recent_posts"] == 1) echo " checked"; ?>  type="checkbox" name="default_recent_posts" /></td>
                </tr>
            </tbody></table>
        </div>
        <div style="text-align: right">
        <input style="width: 80px; cursor: pointer;" type="submit" name="widgets_saving" value="<?php _e("Save", "gd-simple-widgets"); ?>">
        </div>
    </div>
</div>
<div id="gdpt_server" class="postbox gdrgrid frontright">
    <h3 class="hndle"><span><?php _e("Other Settings", "gd-simple-widgets"); ?></span></h3>
    <div class="inside">
        <div class="table">
            <table><tbody>
                <tr class="first">
                    <td class="first b"><?php _e("Load default CSS file", "gd-simple-widgets"); ?></td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["load_default_css"] == 1) echo " checked"; ?> type="checkbox" name="load_default_css" /></td>
                </tr>
                <tr>
                    <td class="first b"><?php _e("Cache data to minimize number of database queries", "gd-simple-widgets"); ?></td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["cache_data"] == 1) echo " checked"; ?> type="checkbox" name="cache_data" /></td>
                </tr>
                <tr>
                    <td class="first b"><?php _e("Write debug info into file", "gd-simple-widgets"); ?></td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["debug_into_file"] == 1) echo " checked"; ?> type="checkbox" name="debug_into_file" /></td>
                </tr>
            </tbody></table>
        </div>
        <div style="text-align: right">
        <input style="width: 80px; cursor: pointer;" type="submit" name="widgets_saving" value="<?php _e("Save", "gd-simple-widgets"); ?>">
        </div>
    </div>
</div>
</form>
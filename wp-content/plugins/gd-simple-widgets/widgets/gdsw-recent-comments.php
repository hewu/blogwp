<?php

class gdswRecentComments extends gdsw_Widget {
    var $folder_name = "gdsw-recent-comments";
    var $cache = array("posts", "comments");
    var $defaults = array(
        "title" => "Recent Comments",
        "count" => 10,
        "hide_empty" => 0,
        "filter_type" => "comm",
        "filter_category" => "",
        "display_css" => "",
        "display_gravatar" => 0,
        "display_gravatar_size" => 32
    );

    function gdswRecentComments() {
        $widget_ops = array('classname' => 'widget_gdsw_recentcomments', 'description' => __("Expanded widget with recent comments.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswrecentcomments', 'gdSW Recent Comments', $widget_ops, $control_ops);
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['count'] = intval(strip_tags(stripslashes($new_instance['count'])));
        $instance['hide_empty'] = isset($new_instance['hide_empty']) ? 1 : 0;
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));

        $instance['filter_category'] = trim(strip_tags(stripslashes($new_instance['filter_category'])));
        $instance['filter_type'] = $new_instance['filter_type'];
        $instance['display_gravatar'] = isset($new_instance['display_gravatar']) ? 1 : 0;
        $instance['display_gravatar_size'] = intval(strip_tags(stripslashes($new_instance['display_gravatar_size'])));

        return $instance;
    }

    function results($instance) {
        global $wpdb, $table_prefix;

        $select = array("c.comment_ID", "c.comment_author", "c.comment_author_email", 
            "c.comment_author_url", "p.ID", "p.post_title", "p.post_name", "p.post_type",
            "'' as post_permalink", "'' as comment_permalink");
        $from = array();
        $where = array("comment_approved = '1'");

        $from[] = sprintf("%scomments c INNER JOIN %sposts p ON c.comment_post_ID = p.ID", $table_prefix, $table_prefix);
        if ($instance["filter_type"] == "ping") $where[] = "comment_type = 'pingback'";
        if ($instance["filter_type"] == "comm") $where[] = "comment_type = ''";
        if ($instance["filter_category"] != "") {
            $from[] = sprintf("INNER JOIN %sterm_relationships tr ON tr.object_id = p.ID", $table_prefix);
            $from[] = sprintf("INNER JOIN %sterm_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id", $table_prefix);
            $where[] = sprintf("tt.term_id in (%s)", $instance["filter_category"]);
        }

        $sql = $this->prepare_sql($instance,
            "DISTINCT ".join(", ", $select),
            join(" ", $from),
            join(" AND ", $where),
            "",
            "c.comment_date_gmt DESC",
            $instance["count"]
        );

        wp_gdsw_log_sql("widget_gdws_recent_comments", $sql);
        return $this->prepare($instance, $wpdb->get_results($sql));
    }

    function render($results, $instance) {
        $render = '<div class="gdsw-widget gdsw-recent-comments '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            $render.= '<li>';
            if ($instance["display_gravatar"] == 1) {
                $render.= '<table><tr><td>';
                $render.= sprintf('<a href="%s" rel="external nofollow" class="gdsw-url">%s</a>', $r->comment_author_url, get_avatar($r->comment_author_email, $instance["display_gravatar_size"]));
                $render.= '</td><td>';
            }
            if ($r->comment_author_url != "" && $r->comment_author_url != "http://") {
                $render.= sprintf('<a href="%s" rel="external nofollow" class="gdsw-url gdsw-url-author">%s</a>', $r->comment_author_url, $r->comment_author);
            } else $render.= $r->comment_author;
            $render.= sprintf(' on <a href="%s" class="gdsw-url gdsw-url-post">%s</a>', get_comment_link($r->comment_ID), $r->post_title);
            if ($instance["display_gravatar"] == 1) $render.= '</td></tr></table>';
            $render.= '</li>';
        }
        $render.= '</ul></div>';
        return $render;
    }
}

?>
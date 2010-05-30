<?php

class gdswMostCommented extends gdsw_Widget {
    var $folder_name = "gdsw-most-commented";
    var $cache = array("posts");
    var $defaults = array(
        "title" => "Most Commented",
        "count" => 10,
        "hide_empty" => 0,
        "filter_published" => "allp",
        "filter_category" => "",
        "display_css" => "",
        "display_comments_count" => 1
    );

    function gdswMostCommented() {
        $widget_ops = array('classname' => 'widget_gdsw_mostcommented', 'description' => __("Most commented posts.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswmostcommented', 'gdSW Most Commented', $widget_ops, $control_ops);
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['count'] = intval(strip_tags(stripslashes($new_instance['count'])));
        $instance['hide_empty'] = isset($new_instance['hide_empty']) ? 1 : 0;
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));

        $instance['filter_published'] = $new_instance['filter_published'];
        $instance['filter_category'] = strip_tags(stripslashes($new_instance['filter_category']));
        $instance['display_comments_count'] = isset($new_instance['display_comments_count']) ? 1 : 0;

        return $instance;
    }

    function results($instance) {
        global $wpdb, $table_prefix;

        $select = array("p.ID", "p.post_title", "p.post_name", "p.post_type", "p.comment_count", "'' as post_permalink");
        $from = array(sprintf("%sposts p", $table_prefix));
        $where = array("p.post_status = 'publish'", "p.comment_count > 0");

        if ($instance["filter_category"] != "") {
            $from[] = sprintf("INNER JOIN %sterm_relationships tr ON tr.object_id = p.ID", $table_prefix);
            $from[] = sprintf("INNER JOIN %sterm_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id", $table_prefix);
            $where[] = sprintf("tt.term_id in (%s)", $instance["filter_category"]);
        }
        if ($instance["filter_published"] != "allp") {
            $days = 0;
            switch ($instance["filter_published"]) {
                case "lwek":
                    $days = 7;
                    break;
                case "lmnt":
                    $days = 31;
                    break;
                case "lyea":
                    $days = 365;
                    break;
            }
            $where[] = sprintf("DATE_SUB(CURDATE(), INTERVAL %s DAY) < p.post_date", $days);
        }

        $sql = $this->prepare_sql($instance,
            "DISTINCT ".join(", ", $select),
            join(" ", $from),
            join(" AND ", $where),
            "",
            "p.comment_count DESC",
            $instance["count"]
        );

        wp_gdsw_log_sql("widget_gdws_popular_posts", $sql);
        return $this->prepare($instance, $wpdb->get_results($sql));
    }

    function render($results, $instance) {
        $render = '<div class="gdsw-widget gdsw-most-commented '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            $render.= '<li>';
            $render.= sprintf('<a href="%s#comments" class="gdsw-url">%s</a>', $r->post_permalink, $r->post_title);
            if ($instance["display_comments_count"] == 1) $render.= sprintf(" %s", $r->comment_count);
            $render.= '</li>';
        }
        $render.= '</ul></div>';
        return $render;
    }
}

?>
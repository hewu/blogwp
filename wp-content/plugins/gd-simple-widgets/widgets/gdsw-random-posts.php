<?php

class gdswRandomPosts extends gdsw_Widget {
    var $folder_name = "gdsw-random-posts";
    var $cache = array("posts");
    var $defaults = array(
        "title" => "Random Posts",
        "count" => 10,
        "hide_empty" => 0,
        "filter_category" => "",
        "filter_recency" => "allp",
        "filter_type" => "post",
        "display_css" => "",
        "display_excerpt" => 0,
        "display_excerpt_length" => 15,
        "display_post_date" => 0,
        "display_post_date_format" => "F j, Y"
    );

    function gdswRandomPosts() {
        $widget_ops = array('classname' => 'widget_gdsw_randomposts',
            'description' => __("Display random posts.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswrandomposts', 'gdSW Random Posts', $widget_ops, $control_ops);
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['count'] = intval(strip_tags(stripslashes($new_instance['count'])));
        $instance['hide_empty'] = isset($new_instance['hide_empty']) ? 1 : 0;
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));

        $instance['filter_recency'] = $new_instance['filter_recency'];
        $instance['filter_category'] = strip_tags(stripslashes($new_instance['filter_category']));
        $instance['filter_type'] = $new_instance['filter_type'];
        $instance['display_excerpt'] = isset($new_instance['display_excerpt']) ? 1 : 0;
        $instance['display_excerpt_length'] = intval(strip_tags(stripslashes($new_instance['display_excerpt_length'])));
        $instance['display_post_date'] = isset($new_instance['display_post_date']) ? 1 : 0;
        $instance['display_post_date_format'] = trim(strip_tags(stripslashes($new_instance['display_post_date_format'])));

        return $instance;
    }

    function results($instance) {
        global $wpdb, $table_prefix;

        $order = "";
        $select = array("p.ID", "p.post_title", "p.post_name", "p.post_type", "'' as post_permalink");
        $from = array(sprintf("%sposts p", $table_prefix));
        $where = array("p.post_status = 'publish'");
        if ($instance["display_post_date"] == 1) $select[] = "p.post_date";
        if ($instance["display_excerpt"] == 1) {
            $select[] = "p.post_content";
            $select[] = "p.post_excerpt";
            $select[] = "'' as excerpt";
        }
        if ($instance["filter_type"] != "postpage") $where[] = sprintf("p.post_type = '%s'", $instance["filter_type"]);

        if ($instance["filter_category"] != "") {
            $from[] = sprintf("INNER JOIN %sterm_relationships tr ON tr.object_id = p.ID", $table_prefix);
            $from[] = sprintf("INNER JOIN %sterm_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id", $table_prefix);
            $where[] = sprintf("tt.term_id in (%s)", $instance["filter_category"]);
        }
        if ($instance["filter_recency"] != "allp") {
            $days = 0;
            switch ($instance["filter_recency"]) {
                case "lday":
                    $days = 1;
                    break;
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
            "RAND()",
            $instance["count"]
        );

        wp_gdsw_log_sql("widget_gdws_random_posts", $sql);
        return $this->prepare($instance, $wpdb->get_results($sql));
    }

    function render($results, $instance) {
        $render = '<div class="gdsw-widget gdsw-random-posts '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            $render.= '<li>';
            $render.= sprintf('<a href="%s" class="gdsw-url">%s</a>', $r->post_permalink, $r->post_title);
            if ($instance["display_post_date"] == 1) $render.= sprintf(' <span class="gdws-date">%s</span>', $r->post_date);
            if ($instance["display_excerpt"] == 1) $render.= sprintf('<p class="gdws-excerpt">%s</p>', $r->excerpt);
            $render.= '</li>';
        }
        $render.= '</ul></div>';
        return $render;
    }
}

?>
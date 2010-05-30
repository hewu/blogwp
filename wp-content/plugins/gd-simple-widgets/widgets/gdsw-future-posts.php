<?php

class gdswFuturePosts extends gdsw_Widget {
    var $folder_name = "gdsw-future-posts";
    var $defaults = array(
        "title" => "Future Posts",
        "count" => 10,
        "hide_empty" => 0,
        "filter_category" => "",
        "display_css" => "",
        "display_excerpt" => 0,
        "display_excerpt_length" => 15,
        "display_post_date" => 0,
        "display_post_date_format" => "F j, Y"
    );

    function gdswFuturePosts() {
        $widget_ops = array('classname' => 'widget_gdsw_futureposts',
            'description' => __("Display scheduled posts.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswfutureposts', 'gdSW Future Posts', $widget_ops, $control_ops);
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['count'] = intval(strip_tags(stripslashes($new_instance['count'])));
        $instance['hide_empty'] = isset($new_instance['hide_empty']) ? 1 : 0;
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));

        $instance['filter_category'] = strip_tags(stripslashes($new_instance['filter_category']));
        $instance['display_excerpt'] = isset($new_instance['display_excerpt']) ? 1 : 0;
        $instance['display_excerpt_length'] = intval(strip_tags(stripslashes($new_instance['display_excerpt_length'])));
        $instance['display_post_date'] = isset($new_instance['display_post_date']) ? 1 : 0;
        $instance['display_post_date_format'] = trim(strip_tags(stripslashes($new_instance['display_post_date_format'])));

        return $instance;
    }

    function results($instance) {
        global $wpdb, $table_prefix;

        $select = array("p.ID", "p.post_title", "p.post_name", "p.post_type");
        $from = array();
        $where = array();

        $from[] = sprintf("%sposts p", $table_prefix);
        $where[] = "p.post_status = 'future'";
        $where[] = "p.post_type = 'post'";
        if ($instance["display_post_date"] == 1) $select[] = "p.post_date";
        if ($instance["display_excerpt"] == 1) {
            $select[] = "p.post_content";
            $select[] = "p.post_excerpt";
            $select[] = "'' as excerpt";
        }
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
            "p.post_date_gmt ASC",
            $instance["count"]
        );

        wp_gdsw_log_sql("widget_gdws_future_posts", $sql);
        return $this->prepare($instance, $wpdb->get_results($sql));
    }

    function render($results, $instance) {
        $render = '<div class="gdsw-widget gdsw-future-posts '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            $render.= '<li>';
            $render.= $r->post_title;
            if ($instance["display_post_date"] == 1) $render.= sprintf(' <span class="gdws-date">%s</span>', $r->post_date);
            if ($instance["display_excerpt"] == 1) $render.= sprintf('<p class="gdws-excerpt">%s</p>', $r->excerpt);
            $render.= '</li>';
        }
        $render.= '</ul></div>';
        return $render;
    }
}

?>
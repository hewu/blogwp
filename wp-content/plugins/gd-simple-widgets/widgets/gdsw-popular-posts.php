<?php

class gdswPopularPosts extends gdsw_Widget {
    var $folder_name = "gdsw-popular-posts";
    var $cache = array("posts");
    var $defaults = array(
        "title" => "Popular Posts",
        "count" => 10,
        "hide_empty" => 0,
        "filter_recency" => "allp",
        "filter_type" => "postpage",
        "filter_category" => "",
        "filter_views" => "all",
        "display_css" => "",
        "display_views" => 1,
        "display_excerpt" => 0,
        "display_excerpt_length" => 15,
        "display_post_date" => 0,
        "display_post_date_format" => "F j, Y"
    );

    function gdswPopularPosts() {
        $widget_ops = array('classname' => 'widget_gdsw_popularposts', 'description' => __("Display popular posts.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswpopularposts', 'gdSW Popular Posts', $widget_ops, $control_ops);
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
        $instance['filter_views'] = $new_instance['filter_views'];
        $instance['display_views'] = isset($new_instance['display_views']) ? 1 : 0;
        $instance['display_excerpt'] = isset($new_instance['display_excerpt']) ? 1 : 0;
        $instance['display_excerpt_length'] = intval(strip_tags(stripslashes($new_instance['display_excerpt_length'])));
        $instance['display_post_date'] = isset($new_instance['display_post_date']) ? 1 : 0;
        $instance['display_post_date_format'] = trim(strip_tags(stripslashes($new_instance['display_post_date_format'])));

        return $instance;
    }

    function inner_select($instance) {
        global $wpdb, $table_prefix;

        $select = array("p.ID", "p.post_title", "p.post_name", "p.post_type", "'' as post_permalink");
        $from = array(
            sprintf("%s p", $wpdb->posts),
            sprintf("INNER JOIN %s u ON u.ID = p.post_author", $wpdb->users));
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

        $sql = $this->prepare_sql($instance,
            "DISTINCT ".join(", ", $select),
            join(" ", $from),
            join(" AND ", $where),
            "", "", ""
        );

        return $sql;
    }

    function results($instance) {
        global $wpdb, $table_prefix;
        $inner_sql = $this->inner_select($instance);

        $order = "";
        $select = array("x.*");
        $from = array(sprintf("%sgdpt_posts_views v",  $wpdb->prefix), sprintf("INNER JOIN (%s) x ON x.ID = v.post_id", $inner_sql));
        $where = array();

        switch ($instance["filter_views"]) {
            case "all":
                $select[] = "sum(v.usr_views + v.vst_views) AS views";
                $order = "sum(v.usr_views + v.vst_views) DESC";
                break;
            case "users":
                $select[] = "sum(v.usr_views) AS views";
                $order = "sum(v.usr_views) DESC";
                break;
            case "visitors":
                $select[] = "sum(v.vst_views) AS views";
                $order = "sum(v.vst_views) DESC";
                break;
        }

        if ($instance["filter_recency"] != "allp") {
            $days = 0;
            if ($instance["filter_recency"] == "tday") {
                $where[] = "v.day = '".date("Y-m-d")."'";
            } else {
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
                $where[] = sprintf("DATE_SUB(CURDATE(), INTERVAL %s DAY) < STR_TO_DATE(v.day, '%s')", $days, '%Y-%m-%d');
            }
        }

        $sql = $this->prepare_sql($instance,
            "DISTINCT ".join(", ", $select),
            join(" ", $from),
            join(" AND ", $where),
            "x.ID",
            $order,
            $instance["count"]
        );

        wp_gdsw_log_sql("widget_gdws_popular_posts", $sql);
        return $this->prepare($instance, $wpdb->get_results($sql));
    }

    function render($results, $instance) {
        $render = '<div class="gdsw-widget gdsw-popular-posts '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            $render.= '<li>';
            $render.= sprintf('<a href="%s" class="gdsw-url">%s</a>', get_permalink($r->ID), $r->post_title);
            if ($instance["display_views"] == 1) $render.= sprintf(" (%s %s)", $r->views, $r->views == 1 ? __("view", "gd-simple-widgets") : __("views", "gd-simple-widgets"));
            if ($instance["display_post_date"] == 1) $render.= sprintf(' <span class="gdws-date">%s</span>', $r->post_date);
            if ($instance["display_excerpt"] == 1) $render.= sprintf('<p class="gdws-excerpt">%s</p>', $r->excerpt);
            $render.= '</li>';
        }
        $render.= '</ul></div>';
        return $render;
    }
}

?>
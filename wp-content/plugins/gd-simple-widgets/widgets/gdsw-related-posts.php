<?php

class gdswRelatedPosts extends gdsw_Widget {
    var $folder_name = "gdsw-related-posts";
    var $cache = array("posts");
    var $defaults = array(
        "title" => "Related Posts",
        "count" => 10,
        "hide_empty" => 0,
        "show_only_single" => 1,
        "filter_related" => "tagcat",
        "display_css" => "",
        "display_excerpt" => 0,
        "display_excerpt_length" => 15,
        "display_post_date" => 0,
        "display_post_date_format" => "F j, Y"
    );

    function gdswRelatedPosts() {
        $widget_ops = array('classname' => 'widget_gdsw_relatedposts',
            'description' => __("Display related posts.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswrelatedposts', 'gdSW Related Posts', $widget_ops, $control_ops);
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['count'] = intval(strip_tags(stripslashes($new_instance['count'])));
        $instance['hide_empty'] = isset($new_instance['hide_empty']) ? 1 : 0;
        $instance['show_only_single'] = isset($new_instance['show_only_single']) ? 1 : 0;
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));

        $instance['filter_related'] = strip_tags(stripslashes($new_instance['filter_related']));
        $instance['display_excerpt'] = isset($new_instance['display_excerpt']) ? 1 : 0;
        $instance['display_excerpt_length'] = intval(strip_tags(stripslashes($new_instance['display_excerpt_length'])));
        $instance['display_post_date'] = isset($new_instance['display_post_date']) ? 1 : 0;
        $instance['display_post_date_format'] = trim(strip_tags(stripslashes($new_instance['display_post_date_format'])));

        return $instance;
    }

    function related_categories($post_id) {
        global $wpdb;

        $sql = "SELECT t.term_id FROM $wpdb->term_taxonomy t, $wpdb->term_relationships r WHERE t.taxonomy = 'category' AND t.term_taxonomy_id = r.term_taxonomy_id AND r.object_id = ".$post_id;
        return $wpdb->get_results($sql);
    }

    function related_tags($post_id) {
        global $wpdb;

        $sql = "SELECT t.term_id FROM $wpdb->term_taxonomy t, $wpdb->term_relationships r WHERE t.taxonomy = 'post_tag' AND t.term_taxonomy_id = r.term_taxonomy_id AND r.object_id = ".$post_id;
        return $wpdb->get_results($sql);
    }

    function results($instance) {
        if ($instance["count"] == 0) return array();

        global $gdsw, $wpdb, $table_prefix;

        $select = array("p.ID", "p.post_title", "p.post_name", "p.post_type",
            "count(r.object_id) as count", "'' as post_permalink");
        $where = array("t.term_taxonomy_id = r.term_taxonomy_id", "r.object_id = p.ID", 
            "p.post_type = 'post'", "p.post_status = 'publish'", "p.ID != ".$gdsw->related_post_id);

        if ($instance["display_post_date"] == 1) $select[] = "p.post_date";
        if ($instance["display_excerpt"] == 1) {
            $select[] = "p.post_content";
            $select[] = "p.post_excerpt";
            $select[] = "'' as excerpt";
        }

        $use_tags = $instance["filter_related"] == "tagcat" || $instance["filter_related"] == "tag";
        $use_cats = $instance["filter_related"] == "tagcat" || $instance["filter_related"] == "cat";

        $relations = $use_tags ? $this->related_tags($gdsw->related_post_id) : array();
        $categories = $use_cats ? $this->related_categories($gdsw->related_post_id) : array();

        foreach ($categories as $cat) $relations[] = $cat;
        $tags = array();
        foreach ($relations as $r) $tags[] = $r->term_id;
        if (count($tags) > 0) $where[] = "t.term_id IN (".join(", ", $tags).")";

        $sql = $this->prepare_sql($instance,
            "DISTINCT ".join(", ", $select),
            sprintf("%sterm_taxonomy t, %sterm_relationships r, %sposts p",
                $table_prefix, $table_prefix, $table_prefix),
            join(" AND ", $where), 
            "r.object_id",
            "RAND()",
            $instance["count"]
        );

        wp_gdsw_log_sql("widget_gdws_related_posts", $sql);
        return $this->prepare($instance, $wpdb->get_results($sql));
    }

    function render($results, $instance) {
        echo '<div class="gdsw-widget gdsw-related-posts '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            echo '<li>';
            echo sprintf('<a href="%s" class="gdsw-url">%s</a>', $r->post_permalink, $r->post_title);
            if ($instance["display_post_date"] == 1) echo sprintf(' <span class="gdws-date">%s</span>', $r->post_date);
            if ($instance["display_excerpt"] == 1) echo sprintf('<p class="gdws-excerpt">%s</p>', $r->excerpt);
            echo '</li>';
        }
        echo '</ul></div>';
    }
}

?>
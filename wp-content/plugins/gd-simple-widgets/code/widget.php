<?php

class gdsw_Widget extends WP_Widget {
    var $folder_name = "";
    var $cache = array();
    var $defaults = array();

    function gdsw_Widget() { }

    function widget($args, $instance) {
        global $gdsr, $userdata;
        extract($args, EXTR_SKIP);

        if ($this->folder_name == "gdsw-related-posts") {
            if ($instance["show_only_single"] == 1 && !is_single()) return;
        }

        $results = $this->results($instance);
        if (count($results) == 0 && $instance["hide_empty"] == 1) return;

        echo $before_widget;
        if ($instance["title"] != '') echo $before_title.$instance["title"].$after_title;
        echo $this->render($results, $instance);
        echo $after_widget;
    }

    function get_filter_name($name = "results") {
        $base = substr($this->folder_name, 5);
        return "gdsw_".$name."_".str_replace("-", "", $base);
    }

    function simple_render($instance = array()) {
        $instance = shortcode_atts($this->defaults, $instance);
        $results = $this->results($instance);
        return $this->render($results, $instance);
    }

    function form($instance) {
        $instance = wp_parse_args((array)$instance, $this->defaults);

        include(GDSIMPLEWIDGETS_PATH.'widgets/shared/shared.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/'.$this->folder_name.'/basic.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/'.$this->folder_name.'/filter.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/'.$this->folder_name.'/display.php');
    }

    function cache_authors($authors) {
        global $wpdb;
        $_authors = $wpdb->get_results(sprintf("SELECT * FROM $wpdb->users WHERE ID in (%s)", join(", ", $authors)));
        foreach ($_authors as $_auth) {
            wp_cache_add($_auth->ID, $_auth, "users");
        }
    }

    function cache_comments($comments) {
        global $wpdb;
        $_comments = $wpdb->get_results(sprintf("SELECT * FROM $wpdb->comments WHERE comment_ID in (%s)", join(", ", $comments)));
        foreach ($_comments as $_cmm) {
            wp_cache_add($_cmm->comment_ID, $_cmm, "comment");
        }
    }

    function cache_posts($posts) {
        global $wpdb;

        $permalink = get_option('permalink_structure');
        $authors = $categories = array();
        $_posts = $wpdb->get_results(sprintf("SELECT * FROM $wpdb->posts WHERE ID in (%s)", join(", ", $posts)));
        foreach ($_posts as $_post) {
            wp_cache_add($_post->ID, $_post, "posts");
            if (!in_array($_post->post_author, $authors)) $authors[] = $_post->post_author;
        }

        if ($permalink != '' && strpos($permalink, '%author%') !== false) {
            $this->cache_authors($authors);
        } if ($permalink != '' && strpos($permalink, '%category%') !== false) {
            $_cats = $wpdb->get_results(sprintf("SELECT t.*, tt.*, tr.object_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ('category') AND tr.object_id IN (%s) ORDER BY t.name ASC", join(", ", $posts)));
            foreach ($_cats as $c) $categories[$c->object_id][] = $c;
            foreach ($categories as $post_id => $cats) wp_cache_add($post_id, $cats, "category_relationships");
        }
    }

    function cache_objects($results) {
        global $gdsw;

        if ($gdsw->cache_active) {
            if (in_array("posts", $this->cache)) {
                $posts = array();
                foreach ($results as $p) {
                    if (!in_array($p->ID, $gdsw->cached["posts"])) {
                        $posts[] = $p->ID;
                        $gdsw->cached["posts"][] = $p->ID;
                    }
                }
                if (count($posts) > 0) $this->cache_posts($posts);
            }
            if (in_array("comments", $this->cache)) {
                $comments = array();
                foreach ($results as $p) {
                    if (!in_array($p->ID, $gdsw->cached["comments"])) {
                        $comments[] = $p->comment_ID;
                        $gdsw->cached["comments"][] = $p->comment_ID;
                    }
                }
                if (count($comments) > 0) $this->cache_comments($comments);
            }
            if (in_array("authors", $this->cache)) {
                $authors = array();
                foreach ($results as $p) {
                    if (!in_array($p->ID, $gdsw->cached["authors"])) {
                        $authors[] = $p->ID;
                        $gdsw->cached["authors"][] = $p->ID;
                    }
                }
                if (count($authors) > 0) $this->cache_authors($authors);
            }
        }
    }

    function prepare($instance, $results) {
        if (count($results) == 0) return array();
        if (count($this->cache) > 0) $this->cache_objects($results);

        foreach ($results as $r) {
            if (isset($instance["display_post_date"]) && $instance["display_post_date"] == 1) $r->post_date = mysql2date($instance["display_post_date_format"], $r->post_date);
            if (isset($instance["display_excerpt"]) && $instance["display_excerpt"] == 1) $r->excerpt = $this->get_excerpt($instance, $r);
        }
        $results = apply_filters($this->get_filter_name(), $results, $instance);
        return $this->prepare_urls($instance, $results);
    }

    function prepare_urls($instance, $results) {
        foreach ($results as $r) {
            if (property_exists($r, "post_permalink") && $r->post_permalink == "") $r->post_permalink = get_permalink($r->ID);
            if (property_exists($r, "comment_permalink") && $r->comment_permalink == "") $r->comment_permalink = get_comment_link($r->comment_ID);
            if (property_exists($r, "author_permalink") && $r->author_permalink == "") {
                if ($this->folder_name == "gdsw-posts-authors")
                    $r->author_permalink = get_author_posts_url($r->ID);
                else
                    $r->author_permalink = get_author_posts_url($r->post_author);
            }
        }
        return $results;
    }

    function prepare_sql($instance, $select, $from, $where, $group, $order, $limit) {
        $select = apply_filters($this->get_filter_name("sql_select"), $select, $instance);
        $from = apply_filters($this->get_filter_name("sql_from"), $from, $instance);
        $where = apply_filters($this->get_filter_name("sql_where"), $where, $instance);
        $group = apply_filters($this->get_filter_name("sql_group"), $group, $instance);
        $order = apply_filters($this->get_filter_name("sql_order"), $order, $instance);
        $limit = apply_filters($this->get_filter_name("sql_limit"), $limit, $instance);

        $sql = "SELECT ".$select." FROM ".$from;
        if ($where != "") $sql.= " WHERE ".$where;
        if ($group != "") $sql.= " GROUP BY ".$group;
        if ($order != "") $sql.= " ORDER BY ".$order;
        if ($limit != "") $sql.= " LIMIT ".$limit;

        return $sql;
    }

    function update($new_instance, $old_instance) { }

    function render($results, $instance) { }

    function results($instance) { return ''; }

    function get_excerpt($instance, $r) {
        $text = trim($r->post_excerpt);

        if ($text == "") {
            $text = strip_shortcodes($r->post_content);
            $text = str_replace(']]>', ']]&gt;', $text);
            $text = strip_tags($text);
            $text = str_replace('"', '\'', $text);
        }

        $text = gdFunctionsGDSW::trim_to_words($text, $instance["display_excerpt_length"]);
        return $text;
    }
}

?>
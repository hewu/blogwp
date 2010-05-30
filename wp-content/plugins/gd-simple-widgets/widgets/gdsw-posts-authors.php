<?php

class gdswPostsAuthors extends gdsw_Widget {
    var $folder_name = "gdsw-posts-authors";
    var $cache = array("authors");
    var $defaults = array(
        "title" => "Posts Authors",
        "count" => 10,
        "hide_empty" => 0,
        "filter_category" => "",
        "filter_min_posts" => 1,
        "display_css" => "",
        "display_gravatar" => 0,
        "display_gravatar_size" => 32,
        "display_posts_count" => 1,
        "display_full_name" => 1
    );

    function gdswPostsAuthors() {
        $widget_ops = array('classname' => 'widget_gdsw_postsauthors', 'description' => __("List of posts authors.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswpostsauthors', 'gdSW Posts Authors', $widget_ops, $control_ops);
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['count'] = intval(strip_tags(stripslashes($new_instance['count'])));
        $instance['hide_empty'] = isset($new_instance['hide_empty']) ? 1 : 0;
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));

        $instance['filter_category'] = strip_tags(stripslashes($new_instance['filter_category']));
        $instance['filter_min_posts'] = intval(strip_tags(stripslashes($new_instance['filter_min_posts'])));
        if ($instance['filter_min_posts'] < 1) $instance['filter_min_posts'] = 1;
        $instance['display_gravatar'] = isset($new_instance['display_gravatar']) ? 1 : 0;
        $instance['display_gravatar_size'] = intval(strip_tags(stripslashes($new_instance['display_gravatar_size'])));
        $instance['display_posts_count'] = isset($new_instance['display_posts_count']) ? 1 : 0;
        $instance['display_full_name'] = isset($new_instance['display_full_name']) ? 1 : 0;

        return $instance;
    }

    function results($instance) {
        global $wpdb, $table_prefix;

        $select = array("u.ID", "u.display_name", "u.user_email", "count(*) as posts", "'' as post_permalink", "'' as author_permalink");
        $from = array();
        $where = array("p.post_status = 'publish'", "p.post_type = 'post'");

        $from[] = sprintf("%s u inner join %sposts p on p.post_author = u.ID", $wpdb->users, $table_prefix);
        if ($instance["display_full_name"] == 1) {
            $select[] = "mf.meta_value as first_name";
            $select[] = "ml.meta_value as last_name";
            $from[] = sprintf("left join %susermeta mf on mf.user_id = u.ID and mf.meta_key = 'first_name'", $table_prefix);
            $from[] = sprintf("left join %susermeta ml on ml.user_id = u.ID and ml.meta_key = 'last_name'", $table_prefix);
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
            "u.ID HAVING count(*) >= ".$instance["filter_min_posts"],
            "count(*) DESC",
            $instance["count"]
        );

        wp_gdsw_log_sql("widget_gdws_posts_authors", $sql);
        return $this->prepare($instance, $wpdb->get_results($sql));
    }

    function render($results, $instance) {
        $render = '<div class="gdsw-widget gdsw-posts-authors '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            $render.= '<li>';
            if ($instance["display_gravatar"] == 1) {
                $render.= '<table><tr><td>';
                $render.= sprintf('<a href="%s" class="gdsw-url">%s</a>', $r->author_permalink, get_avatar($r->user_email, $instance["display_gravatar_size"]));
                $render.= '</td><td>';
            }
            $name = "";
            if ($instance["display_full_name"] == 1) $name = trim($r->first_name." ".$r->last_name);
            if ($name == "") $name = $r->display_name;
            $render.= sprintf('<a href="%s" class="gdsw-url">%s</a>', $r->author_permalink, $name);
            if ($instance["display_posts_count"] == 1) $render.= sprintf(" %s", $r->posts);
            if ($instance["display_gravatar"] == 1) $render.= '</td></tr></table>';
            $render.= '</li>';
        }
        $render.= '</ul></div>';
        return $render;
    }
}

?>
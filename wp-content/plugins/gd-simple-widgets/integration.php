<?php

/**
 * Get results for any of the supported widgets.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_widget_results($widget, $instance = array()) {
    switch ($widget) {
        case "simple_125_ads":
            return "";
            break;
        case "future_posts":
            return gdws_future_posts_results($instance);
            break;
        case "related_posts":
            return gdws_related_posts_results($instance);
            break;
        case "most_commented":
            return gdws_most_commented_results($instance);
            break;
        case "popular_posts":
            return gdws_popular_posts_results($instance);
            break;
        case "post_authors":
            return gdws_post_authors_results($instance);
            break;
        case "random_posts":
            return gdws_random_posts_results($instance);
            break;
        case "recent_comments":
            return gdws_recent_comments_results($instance);
            break;
        case "recent_posts":
            return gdws_recent_posts_results($instance);
            break;
    }
}

/**
 * Render results for any of the supported widgets.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_widget_render($widget, $instance = array(), $echo = true) {
    switch ($widget) {
        case "simple_125_ads":
            return gdws_simple_125_ads_render($instance, $echo);
            break;
        case "future_posts":
            return gdws_future_posts_render($instance, $echo);
            break;
        case "related_posts":
            return gdws_related_posts_render($instance, $echo);
            break;
        case "most_commented":
            return gdws_most_commented_render($instance, $echo);
            break;
        case "popular_posts":
            return gdws_popular_posts_render($instance, $echo);
            break;
        case "post_authors":
            return gdws_post_authors_render($instance, $echo);
            break;
        case "random_posts":
            return gdws_random_posts_render($instance, $echo);
            break;
        case "recent_comments":
            return gdws_recent_comments_render($instance, $echo);
            break;
        case "recent_posts":
            return gdws_recent_posts_render($instance, $echo);
            break;
    }
}

/**
 * Render results for Simple 125 Adss widget.
 *
 * Array with widget settings contains several parameters:
 * - ad_001: left ad code
 * - ad_002: right ad code
 * - encoded: status of ads, html encoded or not. if your ads are clean html, set this to false.
 * - display_css: additional css class(es) to be added to widget
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_simple_125_ads_render($instance = array(), $echo = true) {
    $widget = new gdswFuturePosts();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Related Posts widget.
 * 
 * Array with widget settings contains several parameters:
 * - count: number of posts to show
 * - hide_empty: 1 will not render widget with no results found
 * - show_only_single: 1 will show widget only on single post pages
 * - filter_related: what to use for related calculations: tagcat, tag, cat.
 * - display_css: additional css class(es) to be added to widget
 * - display_excerpt: 1 will show post excerpt
 * - display_excerpt_length: number of words to display from excerpt
 * - display_post_date: 1 will show post date
 * - display_post_date_format: php format for post date
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_related_posts_results($instance = array()) {
    $widget = new gdswRelatedPosts();
    return $widget->results($instance);
}

/**
 * Render results for Related Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_related_posts_render($instance = array(), $echo = true) {
    $widget = new gdswRelatedPosts();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Future Posts widget.
 *
 * Array with widget settings contains several parameters:
 * - count: number of posts to show
 * - hide_empty: 1 will not render widget with no results found
 * - filter_category: category id to filter with
 * - display_css: additional css class(es) to be added to widget
 * - display_excerpt: 1 will show post excerpt
 * - display_excerpt_length: number of words to display from excerpt
 * - display_post_date: 1 will show post date
 * - display_post_date_format: php format for post date
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_future_posts_results($instance = array()) {
    $widget = new gdswFuturePosts();
    return $widget->results($instance);
}

/**
 * Render results for Future Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_future_posts_render($instance = array(), $echo = true) {
    $widget = new gdswFuturePosts();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Most Commented widget.
 *
 * Array with widget settings contains several parameters:
 * - count: number of posts to show
 * - hide_empty: 1 will not render widget with no results found
 * - filter_published: when post is published. lwek: last week, tmnt: last month, tyea: last year.
 * - filter_category: category id to filter with
 * - display_css: additional css class(es) to be added to widget
 * - display_comments_count: show number of comments
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_most_commented_results($instance = array()) {
    $widget = new gdswMostCommented();
    return $widget->results($instance);
}

/**
 * Render results for Most Commented widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_most_commented_render($instance = array(), $echo = true) {
    $widget = new gdswMostCommented();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Popular Posts widget.
 *
 * Array with widget settings contains several parameters:
 * - count: number of posts to show
 * - hide_empty: 1 will not render widget with no results found
 * - filter_category: category id to filter with
 * - filter_recency: what date range to use. tday: today, lday: last 24 hours, lwek: last week, tmnt: last month, tyea: last year.
 * - filter_type: what to use: postpage, post, page
 * - filter_views: all, users, visitors
 * - display_css: additional css class(es) to be added to widget
 * - display_views: show number of post/page views count
 * - display_excerpt: 1 will show post excerpt
 * - display_excerpt_length: number of words to display from excerpt
 * - display_post_date: 1 will show post date
 * - display_post_date_format: php format for post date
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_popular_posts_results($instance = array()) {
    $widget = new gdswPopularPosts();
    return $widget->results($instance);
}

/**
 * Render results for Popular Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_popular_posts_render($instance = array(), $echo = true) {
    $widget = new gdswPopularPosts();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Post Authors widget.
 *
 * Array with widget settings contains several parameters:
 * - count: number of posts to show
 * - hide_empty: 1 will not render widget with no results found
 * - filter_category: category id to filter with
 * - filter_min_posts: minimal number of posts
 * - display_css: additional css class(es) to be added to widget
 * - display_gravatar: post author gravatar
 * - display_gravatar_size: gravatar size in pixels
 * - display_posts_count: display number of posts
 * - display_full_name: display authorts full name
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_post_authors_results($instance = array()) {
    $widget = new gdswPostsAuthors();
    return $widget->results($instance);
}

/**
 * Render results for Post Authors widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_post_authors_render($instance = array(), $echo = true) {
    $widget = new gdswPostsAuthors();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Random Posts widget.
 *
 * Array with widget settings contains several parameters:
 * - count: number of posts to show
 * - hide_empty: 1 will not render widget with no results found
 * - filter_category: category id to filter with
 * - filter_recency: what date range to use. tday: today, lday: last 24 hours, lwek: last week, tmnt: last month, tyea: last year.
 * - filter_type: what to use: postpage, post, page
 * - display_css: additional css class(es) to be added to widget
 * - display_excerpt: 1 will show post excerpt
 * - display_excerpt_length: number of words to display from excerpt
 * - display_post_date: 1 will show post date
 * - display_post_date_format: php format for post date
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_random_posts_results($instance = array()) {
    $widget = new gdswRandomPosts();
    return $widget->results($instance);
}

/**
 * Render results for Random Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_random_posts_render($instance = array(), $echo = true) {
    $widget = new gdswRandomPosts();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Recent Comments widget.
 *
 * Array with widget settings contains several parameters:
 * - count: number of posts to show
 * - hide_empty: 1 will not render widget with no results found
 * - filter_type: what types of comments to use. comm, ping, both.
 * - filter_category: category id to filter with
 * - display_css: additional css class(es) to be added to widget
 * - display_gravatar: 1 will show comment author gravatar
 * - display_gravatar_size: gravatar size in pixels
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_recent_comments_results($instance = array()) {
    $widget = new gdswRecentComments();
    return $widget->results($instance);
}

/**
 * Render results for Recent Comments widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_recent_comments_render($instance = array(), $echo = true) {
    $widget = new gdswRecentComments();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Recent Posts widget.
 *
 * Array with widget settings contains several parameters:
 * - count: number of posts to show
 * - hide_empty: 1 will not render widget with no results found
 * - filter_category: category id to filter with
 * - display_css: additional css class(es) to be added to widget
 * - display_excerpt: 1 will show post excerpt
 * - display_excerpt_length: number of words to display from excerpt
 * - display_post_date: 1 will show post date
 * - display_post_date_format: php format for post date
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_recent_posts_results($instance = array()) {
    $widget = new gdswRecentPosts();
    return $widget->results($instance);
}

/**
 * Render results for Recent Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_recent_posts_render($instance = array(), $echo = true) {
    $widget = new gdswRecentPosts();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

?>
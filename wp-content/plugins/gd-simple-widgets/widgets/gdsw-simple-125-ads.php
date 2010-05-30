<?php

class gdswSimple125Ads extends gdsw_Widget {
    var $folder_name = "gdsw-simple-125-ads";
    var $defaults = array(
        "title" => "",
        "ad_001" => "",
        "ad_002" => "",
        "encoded" => true,
        "display_css" => ""
    );

    function gdswSimple125Ads() {
        $widget_ops = array('classname' => 'widget_gdsw_simple125ads',
            'description' => __("Display two 125x125 ads.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswsimple125ads', 'gdSW Simple 125 Ads', $widget_ops, $control_ops);
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['encoded'] = true;
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));

        $instance['ad_001'] = stripslashes(htmlentities($new_instance['ad_001'], ENT_QUOTES, GDSIMPLEWIDGETS_ENCODING));
        $instance['ad_002'] = stripslashes(htmlentities($new_instance['ad_002'], ENT_QUOTES, GDSIMPLEWIDGETS_ENCODING));

        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));

        return $instance;
    }

    function render($results, $instance) {
        $render = '<div class="gdsw-widget gdsw-simple-125-ads '.$instance["display_css"].'">';
        $render.= '<div class="gdsw-ad gdsw-left">'.($instance['encoded'] ? html_entity_decode($instance['ad_001']) : $instance['ad_001']).'</div>';
        $render.= '<div class="gdsw-ad gdsw-right">'.($instance['encoded'] ? html_entity_decode($instance['ad_002']) : $instance['ad_002']).'</div>';
        $render.= '<div class="gdsw-clear"></div>';
        $render.= '</div>';
        return $render;
    }
}

?>
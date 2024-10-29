<?php

namespace Waod\App\Controllers\Site;

use Waod\App\Controllers\Base;

class Manage extends Base
{
    function setHooks()
    {
        $hooks = array();
        $valid_rule_types = $this->ruleTypes();
        foreach ($valid_rule_types as $type => $rule_type) {
            $rule_class = $rule_type['class'];
            $rule = new $rule_class();
            $rule->hooks();
        }
        return $hooks;
    }

    /**
     * Add the required scripts
     */
    function addSiteScripts()
    {
        wp_enqueue_style(AODFW_PLUGIN_SLUG . '-waod-main', AODFW_URL . 'Assets/Site/Css/main.css', array(), '1.0.0');
        wp_enqueue_style(AODFW_PLUGIN_SLUG . '-off-canvas', AODFW_URL . 'Assets/Site/Css/tinyDrawer.min.css', array(), '1.0.0');
        wp_enqueue_style(AODFW_PLUGIN_SLUG . '-spin-wheel', AODFW_URL . 'Assets/Site/Css/premiumFortuneWheel.min.css', array(), '1.0.0');
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-off-canvas', AODFW_URL . 'Assets/Site/Js/tinyDrawer.min.js', array(), '1.0.0');
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-waod-main', AODFW_URL . 'Assets/Site/Js/main.js', array(), '1.0.0', true);
        //wp_enqueue_script(AODFW_PLUGIN_SLUG . '-tween-maxresetWheel', AODFW_URL . 'Assets/Admin/Js/TweenMax.min.js');
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-jquery-easing', AODFW_URL . 'Assets/Site/Js/jquery.easing.1.3.js');
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-spin-wheel', AODFW_URL . 'Assets/Site/Js/premiumFortuneWheel.min.js');
        wp_enqueue_style(AODFW_PLUGIN_SLUG . '-ui-notify', AODFW_URL . 'Assets/Admin/Css/noty.css', array(), '1.0.0');
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-ui-notify', AODFW_URL . 'Assets/Admin/Js/noty.min.js', array(), '1.0.0', true);
    }
}
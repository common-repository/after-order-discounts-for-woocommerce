<?php

namespace Waod\App;

use Waod\App\Controllers\Admin\Manage;
use Waod\App\Controllers\Base;

class Router
{
    public static $instance;

    /**
     * initiate the woocommerce after order rule plugins
     * @return Router
     */
    public static function init()
    {
        return (self::$instance == NULL) ? new Router() : self::$instance;
    }

    /**
     * Main constructor.
     */
    function __construct()
    {
        //init all the hooks
        $this->initHooks();
        //init plugin
        $this->initPluginCoreHooks();
    }

    /**
     * Init the plugin core hooks
     */
    function initPluginCoreHooks()
    {
        $base_controller = new Base();
        //my plugin going to activated
        register_activation_hook(AODFW_PLUGIN_FILE, array($base_controller, 'activate'));
        //My plugin going to deactivated
        register_deactivation_hook(AODFW_PLUGIN_FILE, array($base_controller, 'deactivate'));
        //My plugin going to delete
        register_uninstall_hook(AODFW_PLUGIN_FILE, '$base_controller::uninstall');
        //settings links
        add_filter('plugin_action_links_' . AODFW_PLUGIN_BASE_FILE, array($base_controller, 'pluginActionLinks'));
    }

    /**
     * init all the hooks need for plugin run
     */
    function initHooks()
    {
        //Users and admin can do this actions
        $site = new Controllers\Site\Manage();
        $site->setHooks();
        if (!is_admin()) {
            add_action('wp_enqueue_scripts', array($site, 'addSiteScripts'), 11);
        }
        //admin can only do this actions
        if (is_admin()) {
            /*
             * manage plugin admin actions
             */
            //add menu pages
            add_action('admin_menu', array($this, 'registerAdminMenus'));
            //Add admin footer text
            $admin = new Manage();
            add_filter('admin_footer_text', array($admin, 'addAdminFooterText'), 1);
            //add admin version text
            add_filter('update_footer', array($admin, 'addAdminFooterVersion'), 11);
            //add admin scripts
            add_filter('admin_enqueue_scripts', array($admin, 'addAdminScripts'), 11);
            //add ajax data
            add_action('wp_ajax_get_rule_conditions', array($admin, 'getRuleConditions'));
            add_action('wp_ajax_get_condition', array($admin, 'getCondition'));
            add_action('wp_ajax_save_the_rule', array($admin, 'saveRule'));
            add_action('wp_ajax_save_settings', array($admin, 'saveSettings'));
            add_action('wp_ajax_update_rule_status', array($admin, 'updateRuleStatus'));
            add_action('wp_ajax_remove_rule', array($admin, 'removeRule'));
            add_action('wp_ajax_select2_search', array($admin, 'selectSearch'));
            add_action('wp_ajax_aodfw_order_the_rules', array($admin, 'orderTheRules'));
        }
    }

    /**
     * Add menus required for my plugin
     */
    function registerAdminMenus()
    {
        global $submenu;
        //Check woocommerce menu is found or not
        if (isset($submenu['woocommerce'])) {
            $admin = new Manage();
            add_submenu_page('woocommerce', 'After Order Discounts', 'After order discounts', 'manage_woocommerce', AODFW_PLUGIN_SLUG, array($admin, 'handlePages'));
        }
    }
}
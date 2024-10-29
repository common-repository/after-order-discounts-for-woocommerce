<?php

namespace Waod\App\Controllers;

use Waod\App\Helpers\File;
use Waod\App\Helpers\Form;
use Waod\App\Helpers\Template;
use Waod\App\Helpers\Woocommerce;

class Base
{
    public static $template, $rule_types, $rule_conditions, $config, $woocommerce, $form, $language;

    /**
     * BaseController constructor
     */
    function __construct()
    {
        self::$template = (empty(self::$template)) ? new Template(AODFW_TEMPLATE_PATH) : self::$template;
        self::$config = (empty(self::$config)) ? new Configuration() : self::$config;
        self::$woocommerce = (empty(self::$woocommerce)) ? new Woocommerce() : self::$woocommerce;
        self::$form = (empty(self::$form)) ? new Form() : self::$form;
        self::$language = (empty(self::$language)) ? new Language() : self::$language;
    }

    /**
     * Handle my plugin un-installation
     */
    public static function uninstall()
    {
        //Todo: Delete all custom tables needed by my plugin
    }

    /**
     * Handle my plugin deactivation
     */
    public function deactivate()
    {
        $plugin_base_file = plugin_basename(AODFW_PLUGIN_FILE);
        //Tell other dependent plugin about my plugin going to deactivated
        do_action('aodfw_deactivate', $plugin_base_file);
    }

    /**
     * check for dependencies of my plugin
     */
    public function activate()
    {
        if ($this->checkDependency()) {
            $plugin_base_file = plugin_basename(AODFW_PLUGIN_FILE);
            //Tell others plugin about my plugin going to activate
            do_action('aodfw_activate', $plugin_base_file);
            //Create required tables
            $this->createRequiredTables();
        } else {
            $lang_string = self::$language->adminStrings();
            exit($lang_string->install_woocommerce);
        }
    }

    /**
     * Check if all plugin requirements were met
     * @return bool
     */
    public function checkDependency()
    {
        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            return false;
        }
        return true;
    }

    /**
     * Link to manage the plugin
     * @param $links
     * @return array
     */
    function pluginActionLinks($links)
    {
        $admin_language = self::$language->adminStrings();
        $action_links = array(
            'create_rule' => '<a href="' . admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG . '&task=create') . '">' . $admin_language->create_discount . '</a>',
            'manage_rules' => '<a href="' . admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG) . '">' . $admin_language->manage_discount . '</a>',
            'settings' => '<a href="' . admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG . '&task=settings') . '">' . $admin_language->settings . '</a>'
        );
        return array_merge($action_links, $links);
    }

    /**
     * Create plugin required table
     */
    function createRequiredTables()
    {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_collate = $wpdb->get_charset_collate();
        $create_rules_table_query = '
        CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'aodfw_rules (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `status` tinyint(4) NOT NULL,
            `priority` tinyint(4) NOT NULL,
            `title` varchar(255) DEFAULT \'Untitled Rule\',
            `start_from` varchar(255) DEFAULT NULL,
            `end_on` varchar(255) DEFAULT NULL,
            `class` text,
            `description` text,
            `used_conditions` text,
            `conditions` text,
            `discounts` text,
            `rule_type` text NOT NULL,
            PRIMARY KEY (ID)
        )  ' . $table_collate . ';
        ';
        $create_coupons_table_query = '
        CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'aodfw_coupons (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `status` tinyint(4) NOT NULL,
            `email_opened` tinyint(4) DEFAULT 0,
            `is_used` tinyint(4) DEFAULT 0,
            `coupon` varchar(255) DEFAULT NULL,
            `coupon_type` varchar(255) DEFAULT NULL,
            `coupon_value` int(11) DEFAULT NULL,
            `rule_id` int(11) DEFAULT NULL,
            `sent_to` varchar(255) DEFAULT NULL,
            `order_id` int(11) DEFAULT NULL,
            `coupon_for` varchar(255) NOT NULL DEFAULT "order_coupon",
            `usage_restriction` text,
            PRIMARY KEY (ID)
        )  ' . $table_collate . ';
        ';
        $create_lucky_win_table_query = '
        CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'aodfw_spins (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `status` tinyint(4) NOT NULL,
            `spins` tinyint(4) DEFAULT 0,
            `used` tinyint(4) DEFAULT 0,
            `details` TEXT,
            `order_id` int(11) DEFAULT NULL,
            `user_id` int(11) DEFAULT NULL,
            `rule_id` int(11) DEFAULT NULL,
            PRIMARY KEY (ID)
        )  ' . $table_collate . ';
        ';
        $create_spin_log_table_query = '
        CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'aodfw_spins_log (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `spin_id` int(11) DEFAULT NULL,
            `user_id` int(11) DEFAULT NULL,
            `coupon_id` int(11) DEFAULT NULL,
            `rule_id` int(11) DEFAULT NULL,
            `array_key` int(11) DEFAULT NULL,
            PRIMARY KEY (ID)
        )  ' . $table_collate . ';
        ';
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($create_rules_table_query);
        dbDelta($create_coupons_table_query);
        dbDelta($create_lucky_win_table_query);
        dbDelta($create_spin_log_table_query);
    }

    /**
     * returns the array about what are available rules and it's
     * name,class and so on..
     * @return array
     */
    final function ruleTypes()
    {
        if (!empty(self::$rule_types)) {
            return self::$rule_types;
        }
        $discounts = array();
        $file_helper = new File();
        $rule_class_names = $file_helper->getFiles(AODFW_PATH . 'App/Controllers/Rules', array('Base'), false);
        //Validate rule type defined by us
        $rule_keys = array('class', 'name', 'label');
        if (!empty($rule_class_names)) {
            foreach ($rule_class_names as $class_name) {
                $class = 'Waod\App\Controllers\Rules\\' . $class_name;
                if (class_exists($class)) {
                    $class_obj = new $class();
                    if (method_exists($class_obj, 'ruleDetails') && $class_obj instanceof Rules\Base) {
                        $rule_details = $class_obj->ruleDetails();
                        if (is_array($rule_details)) {
                            if (count($rule_keys) == count(array_intersect($rule_keys, array_keys($rule_details)))) {
                                $discounts[$rule_details['name']] = $rule_details;
                            }
                        }
                    }
                }
            }
        }
        //Get 3rd party rule types
        $external_rule_types = apply_filters('aodfw_register_rule_type', array());
        if (is_array($external_rule_types)) {
            foreach ($external_rule_types as $rule_details) {
                if (is_array($rule_details)) {
                    if (count($rule_keys) == count(array_intersect($rule_keys, array_keys($rule_details)))) {
                        $class = $rule_details['class'];
                        if (class_exists($class)) {
                            $class_obj = new $class();
                            if ($class_obj instanceof Rules\Base) {
                                $rule_details['default'] = false;
                                $discounts[$rule_details['name']] = $rule_details;
                            }
                        }
                    }
                }
            }
        }
        self::$rule_types = $discounts;
        return $discounts;
    }

    /**
     * returns the array about what are available conditions and it's
     * name,class and so on..
     * @return array
     */
    final function discountConditions()
    {
        if (!empty(self::$rule_conditions)) {
            return self::$rule_conditions;
        }
        $conditions = array();
        $file_helper = new File();
        $condition_class_names = $file_helper->getFiles(AODFW_PATH . 'App/Controllers/Conditions', array('Base'), false);
        //Validate rule type defined by us
        $condition_keys = array('class', 'name', 'label');
        if (!empty($condition_class_names)) {
            foreach ($condition_class_names as $class_name) {
                $class = 'Waod\App\Controllers\Conditions\\' . $class_name;
                if (class_exists($class)) {
                    $class_obj = new $class();
                    if (method_exists($class_obj, 'conditionDetails') && $class_obj instanceof Conditions\Base) {
                        $condition_details = $class_obj->conditionDetails();
                        if (is_array($condition_details)) {
                            if (count($condition_keys) == count(array_intersect($condition_keys, array_keys($condition_details)))) {
                                $conditions[$condition_details['name']] = $condition_details;
                            }
                        }
                    }
                }
            }
        }
        //Get 3rd party conditions
        $external_conditions = apply_filters('aodfw_register_conditions', array());
        if (is_array($external_conditions)) {
            foreach ($external_conditions as $condition_details) {
                if (is_array($condition_details)) {
                    if (count($condition_keys) == count(array_intersect($condition_keys, array_keys($condition_details)))) {
                        $class = $condition_details['class'];
                        if (class_exists($class)) {
                            $class_obj = new $class();
                            if ($class_obj instanceof Conditions\Base) {
                                $conditions[$condition_details['name']] = $condition_details;
                            }
                        }
                    }
                }
            }
        }
        self::$rule_conditions = $conditions;
        return $conditions;
    }
}
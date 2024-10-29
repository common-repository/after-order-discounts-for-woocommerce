<?php
/**
 * Plugin Name: After Order Discounts For Woocommerce
 * Plugin URI: https://premiumwoo.com
 * Description: This WooCommerce Coupon Plugin helps you to Create Discount Coupon Code for your Regular Customer.
 * Version: 1.0.4
 * Author: PremiumWoo
 * Author URI: https://premiumwoo.com
 * License: GPLv3 or later
 */
defined('ABSPATH') or die;
//Define plugin version
defined('AODFW_VERSION') or define('AODFW_VERSION', '1.0.3');
//Define plugin prefix
defined('AODFW_PREFIX') or define('AODFW_PREFIX', 'waod');
//Define plugin path
defined('AODFW_PATH') or define('AODFW_PATH', plugin_dir_path(__FILE__));
//Define template path
defined('AODFW_TEMPLATE_PATH') or define('AODFW_TEMPLATE_PATH', AODFW_PATH . 'App/Views');
//Define plugin url
defined('AODFW_URL') or define('AODFW_URL', plugin_dir_url(__FILE__));
//Define plugin file
defined('AODFW_PLUGIN_FILE') or define('AODFW_PLUGIN_FILE', __FILE__);
//define plugin base file
defined('AODFW_PLUGIN_BASE_FILE') or define('AODFW_PLUGIN_BASE_FILE', plugin_basename(__FILE__));
//Define the text domain
defined('AODFW_TEXT_DOMAIN') or define('AODFW_TEXT_DOMAIN', 'woocommerce-after-order-discounts');
//Define plugin slug
defined('AODFW_PLUGIN_SLUG') or define('AODFW_PLUGIN_SLUG', 'woocommerce-after-order-discounts');
//Initiate the plugin
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    return false;
}
include __DIR__ . '/vendor/autoload.php';

use Waod\App\Router;

Router::init();

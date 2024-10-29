<?php

namespace Waod\App\Helpers;

class Woocommerce
{
    /**
     * Get order object
     * @param $order_id
     * @return bool|WC_Order|null
     */
    static function getOrder($order_id)
    {
        if (function_exists('wc_get_order')) {
            return wc_get_order($order_id);
        }
        return NULL;
    }

    /**
     * Get order items object
     * @param $order
     * @return bool|array|null
     */
    static function getOrderItems($order)
    {
        if (method_exists($order, 'get_items')) {
            return $order->get_items();
        }
        return array();
    }

    /**
     * Get order items object
     * @param $order
     * @return bool|array|null
     */
    static function getOrderPaymentMethod($order)
    {
        if (method_exists($order, 'get_payment_method')) {
            return $order->get_payment_method();
        }
        return NULL;
    }

    /**
     * Get order items object
     * @param $item
     * @return bool|array|null
     */
    static function getItemId($item)
    {
        if (method_exists($item, 'get_product_id') && method_exists($item, 'get_variation_id')) {
            $variation_id = $item->get_variation_id();
            if (!empty($variation_id)) {
                return $variation_id;
            } else {
                return $item->get_product_id();
            }
        }
        return 0;
    }

    /**
     * Get category Id of product
     * @param $item_id
     * @return null
     */
    function getItemCategories($item_id)
    {
        if (function_exists('wp_get_post_terms')) {
            return wp_get_post_terms($item_id, 'product_cat', array('fields' => 'slugs'));
        }
        return array();
    }

    /**
     * Get used coupons of order
     * @param $order
     * @return null
     */
    static function getUsedCoupons($order)
    {
        if (method_exists($order, 'get_used_coupons')) {
            return $order->get_used_coupons();
        }
        return NULL;
    }

    /**
     * Get used coupons of order
     * @param $order
     * @return null
     */
    static function getOrderUser($order)
    {
        if (method_exists($order, 'get_user')) {
            return $order->get_user();
        } else {
            $email = self::getOrderEmail($order);
            if (empty($email)) {
                $email = (isset($_REQUEST['_billing_email']) && !empty($_REQUEST['_billing_email'])) ? $_REQUEST['_billing_email'] : '';
            }
            return get_user_by('email', $email);
        }
    }

    /**
     * Get order Email form order object
     * @param $order
     * @return null
     */
    static function getOrderEmail($order)
    {
        if (method_exists($order, 'get_billing_email')) {
            return $order->get_billing_email();
        } elseif (isset($order->billing_email)) {
            return $order->billing_email;
        }
        return NULL;
    }

    /**
     * Get order Email form order object
     * @param $order
     * @return null
     */
    static function getOrderTotal($order)
    {
        if (method_exists($order, 'get_total')) {
            return $order->get_total();
        }
        return 0;
    }

    /**
     * Get Order Id
     * @param $order
     * @return String|null
     */
    static function getOrderId($order)
    {
        if (method_exists($order, 'get_id')) {
            return $order->get_id();
        } elseif (isset($order->id)) {
            return $order->id;
        }
        return NULL;
    }

    /**
     * Format the price
     * @param $price
     * @return string
     */
    static function formatPrice($price)
    {
        if (function_exists('wc_price'))
            return wc_price($price);
        else
            return $price;
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    static function setPHPSession($key, $value)
    {
        if (empty($key) || empty($value))
            return false;
        if (!session_id()) {
            session_start();
        }
        $_SESSION[$key] = $value;
        return true;
    }

    /**
     * Get data from session
     * @param $key
     * @return array|string|null
     */
    static function getPHPSession($key)
    {
        if (empty($key))
            return NULL;
        if (!session_id()) {
            session_start();
        }
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return NULL;
    }

    /**
     * Remove data from session
     * @param $key
     * @return bool
     */
    static function removePHPSession($key)
    {
        if (empty($key))
            return false;
        if (!session_id()) {
            session_start();
        }
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        return true;
    }

    /**
     * Get cart items
     * @return array
     */
    static function getCart()
    {
        if (method_exists(WC()->cart, 'get_cart')) {
            return WC()->cart->get_cart();
        }
        return array();
    }

    /**
     * Add discount to cart
     * @param $discount_code
     * @return bool
     */
    static function addDiscount($discount_code)
    {
        if (empty($discount_code))
            return false;
        if (method_exists(WC()->cart, 'add_discount')) {
            return WC()->cart->add_discount($discount_code);
        }
        return false;
    }

    /**
     * get all coupons in cart
     * @return array|bool
     */
    static function getAppliedCouponsOfCart()
    {
        if (method_exists(WC()->cart, 'get_applied_coupons')) {
            return WC()->cart->get_applied_coupons();
        }
        return false;
    }

    /**
     * get all coupons in cart
     * @return array|bool
     */
    static function getAvailablePaymentGateways()
    {
        if (method_exists(WC()->payment_gateways, 'get_available_payment_gateways')) {
            return WC()->payment_gateways->get_available_payment_gateways();
        }
        return array();
    }
}
<?php

namespace Waod\App\Controllers\Rules;

use Waod\App\Models\CouponModel;

class orderCoupon extends Base
{
    public $name = 'order_coupon';

    function ruleName()
    {
        return $this->name;
    }

    function ruleDetails()
    {
        return array(
            'class' => __CLASS__,
            'name' => $this->name,
            'label' => self::$language_strings->title_order_coupon,
            'default' => true
        );
    }

    function ruleSettings()
    {
        $attach_coupon_at = self::$config->getConfig('attach_coupon_at', 'woocommerce_email_after_order_table');
        $coupon_message = self::$config->getConfig('coupon_message', '');
        return array(
            array(
                'label' => self::$form->label(self::$language_strings->setting_attach_coupon_at),
                'field' => self::$form->dropdown('attach_coupon_at', array(
                    'woocommerce_email_after_order_table' => self::$language_strings->setting_attach_after_order_details,
                    'woocommerce_email_before_order_table' => self::$language_strings->setting_attach_before_order_details
                ), $attach_coupon_at),
                'description' => ''
            ),
            array(
                'label' => self::$form->label(self::$language_strings->setting_coupon_message_label),
                'field' => self::$form->editor('coupon_message', $coupon_message),
                'description' => self::$language_strings->setting_coupon_message_description
            )
        );
    }

    function conditions()
    {
        $conditions = array(
            'order_total',
            'order_items',
            'order_categories',
            'order_customers',
            'order_payment_methods',
        );
        $third_party_conditions = apply_filters('aodfw_register_conditions_for_rules', $this->name, array());
        if (is_array($third_party_conditions)) {
            $conditions = array_merge($conditions, $third_party_conditions);
        }
        return $conditions;
    }

    function hooks()
    {
        $email_hook = self::$config->getConfig('attach_coupon_at', 'woocommerce_email_after_order_table');
        add_action('woocommerce_order_status_changed', array($this, 'orderStatusChanged'), 100, 3);
        add_action('woocommerce_checkout_update_order_meta', array($this, 'orderMetaUpdated'), 100, 2);
        add_action('woocommerce_init', array($this, 'setCouponToSession'), 100);
        add_action('woocommerce_removed_coupon', array($this, 'removeCouponFromCart'), 100);
        add_action('woocommerce_get_shop_coupon_data', array($this, 'addVirtualCoupon'), 100, 2);
        add_action('woocommerce_cart_loaded_from_session', array($this, 'addCouponToCheckout'), 100);
        add_action($email_hook, array($this, 'attachOrderCoupon'), 100, 4);
    }

    /**
     * create coupon when order status changed
     * @param $order_id
     * @param $old_status
     * @param $new_status
     */
    function orderStatusChanged($order_id, $old_status, $new_status)
    {
        $this->createCoupon($order_id);
    }

    /**
     * Create coupon when order meta updated
     * @param $order_id
     * @param $data
     */
    function orderMetaUpdated($order_id, $data)
    {
        $this->createCoupon($order_id);
    }

    /**
     * attach coupon to mail
     * @param $order
     * @param $sent_to_admin
     * @param $plain_text
     * @param $email
     */
    function attachOrderCoupon($order, $sent_to_admin, $plain_text, $email)
    {
        $order_id = self::$woocommerce->getOrderId($order);
        $this->createCoupon($order_id);
        $coupon_model = new CouponModel();
        $coupon_details = $coupon_model->getOrderCoupon($order_id);
        if (!empty($coupon_details)) {
            $coupon = $coupon_details->coupon;
            if ($coupon_details->coupon_type) {
                $coupon_value = self::$woocommerce->formatPrice($coupon_details->coupon_value);
            } else {
                $coupon_value = $coupon_details->coupon_value . '%';
            }
            $details = array(
                'coupon_value' => $coupon_value,
                'coupon_code' => $coupon,
                'coupon_apply_url' => site_url() . '?' . http_build_query(array('coupon' => $coupon, 'task' => 'apply'))
            );
            $html = self::$config->getConfig('coupon_message', '');
            foreach ($details as $key => $value) {
                $html = str_replace('{{' . $key . '}}', $value, $html);
            }
            echo $html;
        }
    }

    /**
     * Set coupon t session
     */
    function setCouponToSession()
    {
        if (isset($_REQUEST['coupon']) && isset($_REQUEST['task'])) {
            $coupon = sanitize_text_field($_REQUEST['coupon']);
            $task = sanitize_text_field($_REQUEST['task']);
            if (!empty($coupon) && $task == 'apply') {
                $coupon_model = new CouponModel();
                $coupon_details = $coupon_model->getCoupon($coupon);
                if (!empty($coupon_details)) {
                    self::$woocommerce->setPHPSession('aodfw_coupon_code', $coupon);
                }
            }
        }
    }

    /**
     * Add coupon to checkout
     */
    function addCouponToCheckout()
    {
        $coupon_code = self::$woocommerce->getPHPSession('aodfw_coupon_code');
        $already_applied_coupons = self::$woocommerce->getAppliedCouponsOfCart();
        if (!empty($coupon_code) && !in_array($coupon_code, $already_applied_coupons) && !empty(self::$woocommerce->getCart())) {
            self::$woocommerce->addDiscount($coupon_code);
            $this->removeCouponFromCart($coupon_code);
        }
    }

    /**
     * Remove coupon on user request
     * @param $remove_coupon
     */
    function removeCouponFromCart($remove_coupon)
    {
        $coupon_code = self::$woocommerce->getPHPSession('aodfw_coupon_code');
        if (strtoupper($remove_coupon) == strtoupper($coupon_code)) {
            self::$woocommerce->removePHPSession('aodfw_coupon_code');
        }
    }

    /**
     * Create the virtual coupon
     * @param $response
     * @param $coupon_code
     * @return array|bool
     */
    function addVirtualCoupon($response, $coupon_code)
    {
        if (empty($coupon_code))
            return $response;
        $coupon_model = new CouponModel();
        $coupon_details = $coupon_model->getCoupon($coupon_code);
        if (!empty($coupon_details)) {
            $third_party_filter = true;
            $third_party_filter = apply_filters('aodfw_validate_coupon', $third_party_filter, $coupon_details->ID, $coupon_details->coupon_for);
            if ($coupon_details->is_used == 0 && $third_party_filter) {
                $discount_type = 'fixed_cart';
                if ($coupon_details->coupon_type == 'percent' || $coupon_details->coupon_type == 'percentage')
                    $discount_type = 'percent';
                $coupon_value = $coupon_details->coupon_value;
                $coupon = array(
                    'id' => 321123 . rand(2, 9),
                    'amount' => $coupon_value,
                    'individual_use' => true,
                    'product_ids' => array(),
                    'excluded_product_ids' => array(),
                    'usage_limit' => '',
                    'usage_limit_per_user' => '',
                    'limit_usage_to_x_items' => '',
                    'usage_count' => '',
                    'expiry_date' => '',
                    'apply_before_tax' => 'yes',
                    'free_shipping' => false,
                    'product_categories' => array(),
                    'excluded_product_categories' => array(),
                    'exclude_sale_items' => false,
                    'minimum_amount' => '',
                    'maximum_amount' => '',
                    'customer_email' => '',
                    'discount_type' => $discount_type,
                    'virtual' => true
                );
                return $coupon;
            }
        }
        return $response;
    }

    /**
     * Create Order Coupon
     * @param $order_id
     * @param array $data
     * @return bool|null
     */
    function createCoupon($order_id, $data = array())
    {
        if (empty($order_id))
            return false;
        $order = self::$woocommerce->getOrder($order_id);
        $rule = $this->getValidRules(__CLASS__, $order);
        if (empty($rule)) {
            return false;
        }
        $order_id = intval($order_id);
        $coupon_id = NULL;
        $coupon_model = new CouponModel();
        $rule_id = $rule->id();
        $coupon_details = $coupon_model->getOrderCoupon($order_id, $rule_id);
        if (empty($coupon_details)) {
            $discount_details = $rule->discounts();
            $discount = (isset($discount_details[$this->name])) ? $discount_details[$this->name] : array();
            $new_coupon_code = strtoupper(uniqid());
            $new_coupon_code = chunk_split($new_coupon_code, 5, '-');
            $new_coupon_code = rtrim($new_coupon_code, '-');
            $new_coupon_code = sanitize_text_field($new_coupon_code);
            $email = self::$woocommerce->getOrderEmail($order);
            if (empty($email)) {
                $email = (isset($_REQUEST['_billing_email']) && !empty($_REQUEST['_billing_email'])) ? $_REQUEST['_billing_email'] : '';
            }
            $new_coupon_details = array(
                'coupon' => $new_coupon_code,
                'order_id' => $order_id,
                'sent_to' => $email,
                'coupon_type' => isset($discount['type']) ? $discount['type'] : 'flat',
                'coupon_value' => isset($discount['value']) ? $discount['value'] : 0,
                'rule_id' => $rule_id
            );
            $coupon_id = $coupon_model->saveCoupon($new_coupon_details);
        }
        $used_coupons = self::$woocommerce->getUsedCoupons($order);
        if (!empty($used_coupons)) {
            foreach ($used_coupons as $used_coupon) {
                if (empty($used_coupon))
                    continue;
                $used_coupon_details = $coupon_model->getCoupon($used_coupon);
                if (!empty($coupon_details)) {
                    $key = $coupon_model->primary_key;
                    $coupon_id = isset($used_coupon_details->$key) ? $used_coupon_details->$key : NULL;
                    $coupon_model->update(array('is_used' => 1), $coupon_id);
                }
            }
        }
        return $coupon_id;
    }

    /**
     * Discount
     * @param array $values
     * @return array|false|string
     */
    function discounts($values)
    {
        $params = array(
            'language' => self::$language->adminStrings(),
            'form' => self::$form,
            'coupon_value' => (isset($values["value"])) ? $values["value"] : '',
            'coupon_type' => (isset($values["type"])) ? $values["type"] : ''
        );
        return self::$template->render('Admin/Rules/Discounts/order_coupon.php', $params)->get();
    }
}
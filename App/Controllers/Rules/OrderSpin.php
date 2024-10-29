<?php

namespace Waod\App\Controllers\Rules;

use Waod\App\Models\CouponModel;
use Waod\App\Models\SpinLogModel;
use Waod\App\Models\SpinModel;

class orderSpin extends Base
{
    public $name = 'order_spin';

    function ruleName()
    {
        return $this->name;
    }

    function ruleDetails()
    {
        return array(
            'class' => __CLASS__,
            'name' => $this->name,
            'label' => self::$language_strings->spin_wheel_title,
            'default' => false
        );
    }

    function ruleSettings()
    {
        $show_spinner_at = self::$config->getConfig('pages', array());
        $spin_message = self::$config->getConfig('spin_message', '');
        $show_sw_for_non_logged_in_users = self::$config->getConfig('show_sw_for_non_logged_in_users', '1');
        $spin_to_win_text = self::$config->getConfig('spin_to_win_text', 'Spin and win');
        $spin_to_win_title_text = self::$config->getConfig('spin_gift_menu_title', 'Lucky Gifts');
        $config_spin_count = self::$config->getConfig('number_of_spins_allow', 1);
        $pages = get_pages();
        $page_list = array();
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $page_list[$page->ID] = $page->post_title;
            }
        }
        $spin_count = array('500' => self::$language_strings->setting_spin_unlimited_label);
        for ($i = 1; $i <= 20; $i++) {
            $spin_count[$i] = $i;
        }
        $attach_coupon_at = self::$config->getConfig('attach_spin_details_at', 'woocommerce_email_after_order_table');
        return array(
            array(
                'label' => self::$form->label(self::$language_strings->setting_spin_wheel_text_label),
                'field' => self::$form->input('spin_to_win_text', $spin_to_win_text),
                'description' => ''
            ), array(
                'label' => self::$form->label(self::$language_strings->setting_spin_menu_title_label),
                'field' => self::$form->input('spin_gift_menu_title', $spin_to_win_title_text),
                'description' => ''
            ), array(
                'label' => self::$form->label(self::$language_strings->setting_spin_at_label),
                'field' => self::$form->multiselect('pages[]', $page_list, $show_spinner_at, array('class' => 'select-2')),
                'description' => self::$language_strings->setting_spin_at_desctption
            ), array(
                'label' => self::$form->label(self::$language_strings->setting_spin_pre_day),
                'field' => self::$form->dropdown('number_of_spins_allow', $spin_count, $config_spin_count),
                'description' => ''
            ),
            array(
                'label' => self::$form->label(self::$language_strings->setting_spin_attatach_mail_at),
                'field' => self::$form->dropdown('attach_spin_details_at', array(
                    'woocommerce_email_after_order_table' => self::$language_strings->setting_attach_after_order_details,
                    'woocommerce_email_before_order_table' => self::$language_strings->setting_attach_before_order_details
                ), $attach_coupon_at),
                'description' => ''
            ),
            array(
                'label' => self::$form->label(self::$language_strings->setting_spin_show_for_non_loggedin_label),
                'field' => self::$form->dropdown('show_sw_for_non_logged_in_users', array(
                    '0' => self::$language_strings->no,
                    '1' => self::$language_strings->yes
                ), $show_sw_for_non_logged_in_users),
                'description' => ''
            ),
            array(
                'label' => self::$form->label(self::$language_strings->setting_spin_wheel_message_label),
                'field' => self::$form->editor('spin_message', $spin_message),
                'description' => self::$language_strings->setting_spin_wheel_message_description
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
        add_action('wp_footer', array($this, 'addSiteSpinner'));
        add_action('woocommerce_order_status_changed', array($this, 'orderStatusChanged'), 100, 3);
        add_action('woocommerce_checkout_update_order_meta', array($this, 'orderMetaUpdated'), 100, 2);
        add_action('wp_ajax_create_lucky_prizes', array($this, 'createPrizes'));
        add_action('init', array($this, 'addEndPoint'));
        add_filter('query_vars', array($this, 'addQueryVar'), 0);
        add_action('wp_loaded', array($this, 'flushRewriteRules'));
        add_filter('woocommerce_account_menu_items', array($this, 'addMenuItem'), 0);
        add_action('woocommerce_account_lucky-gifts_endpoint', array($this, 'renderEndPoint'));
        $email_hook = self::$config->getConfig('attach_spin_details_at', 'woocommerce_email_after_order_table');
        add_action($email_hook, array($this, 'attachOrderSpin'), 100, 4);
    }

    /**
     * attach coupon to mail
     * @param $order
     * @param $sent_to_admin
     * @param $plain_text
     * @param $email
     */
    function attachOrderSpin($order, $sent_to_admin, $plain_text, $email)
    {
        $order_id = self::$woocommerce->getOrderId($order);
        $this->addSpins($order_id);
        $spin_model = new SpinModel();
        $spin_details = $spin_model->getOrderSpin($order_id);
        if (!empty($spin_details)) {
            $spins = $spin_details->spins;
            $spin_prize_details = '';
            if (isset($spin_details->details) && $spin_details->details != '[]' && $spin_details->details != '{}') {
                $gifts = json_decode($spin_details->details, true);
                if (!empty($gifts)) {
                    $spin_detail_arr = array();
                    foreach ($gifts as $gift) {
                        $value = isset($gift['value']) ? $gift['value'] : 0;
                        if ($value > 0 && !empty($gift['label'])) {
                            $spin_detail_arr[] = $gift['label'];
                        }
                    }
                    $spin_prize_details = implode(', ', $spin_detail_arr);
                }
            }
            $details = array(
                'spin_point' => $spins,
                'spin_details' => $spin_prize_details
            );
            $html = self::$config->getConfig('spin_message', '');
            foreach ($details as $key => $value) {
                $html = str_replace('{{' . $key . '}}', $value, $html);
            }
            echo $html;
        }
    }

    /**
     * Add my account sub-page
     */
    function addEndPoint()
    {
        add_rewrite_endpoint('lucky-gifts', EP_ROOT | EP_PAGES);
    }

    /**
     * Flush the rules
     */
    function flushRewriteRules()
    {
        flush_rewrite_rules();
    }

    /**
     * Render the prizes
     */
    function renderEndPoint()
    {
        $spin_log_model = new SpinLogModel();
        $user_id = get_current_user_id();
        $language_strings = self::$language->siteStrings();
        $params = array(
            'gifts' => $spin_log_model->getGifts($user_id),
            'language' => $language_strings
        );
        self::$template->render('Site/Spins/prize_list.php', $params)->display();
    }

    /**
     * add query
     * @param $vars
     * @return array
     */
    function addQueryVar($vars)
    {
        $vars[] = 'lucky-gifts';
        return $vars;
    }

    /**
     * add account menu item
     * @param $menu_links
     * @return array
     */
    function addMenuItem($menu_links)
    {
        $menu_title = self::$config->getConfig('spin_gift_menu_title', 'Lucky gifts');
        $new = array('lucky-gifts' => $menu_title);
        $menu_links = array_slice($menu_links, 0, 2, true)
            + $new
            + array_slice($menu_links, 1, NULL, true);
        return $menu_links;
    }

    /**
     * make lucky prizes
     */
    function createPrizes()
    {
        $response = array('stop' => true);
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            if (!empty($user_id)) {
                $last_spin = get_user_meta($user_id, 'last_spin_details', true);
                $allow_spin = true;
                if (!empty($last_spin)) {
                    $last_spin_arr = json_decode($last_spin, true);
                    $spin_count = isset($last_spin_arr['total']) ? $last_spin_arr['total'] : 0;
                    $spin_time = isset($last_spin_arr['date']) ? $last_spin_arr['date'] : strtotime(date('Y-m-d'));
                    $config_spin_count = self::$config->getConfig('number_of_spins_allow', 1);
                    if (strtotime(date('Y-m-d')) == $spin_time && $spin_count >= $config_spin_count) {
                        $allow_spin = false;
                    }
                }
                if (isset($_REQUEST['spin_id']) && !empty(isset($_REQUEST['spin_id'])) && $allow_spin) {
                    $spin_id = sanitize_key($_REQUEST['spin_id']);
                    $spin_model = new SpinModel();
                    $spin_details = $spin_model->getByKey($spin_id);
                    if (!empty($spin_details)) {
                        if (isset($spin_details->details) && $spin_details->details != '[]' && $spin_details->details != '{}') {
                            $gifts = json_decode($spin_details->details, true);
                            if (!empty($gifts)) {
                                $gift_ids = array_keys($gifts);
                                $filtered_gifts = array_filter($gift_ids, 'is_int');
                                shuffle($filtered_gifts);
                                $key = isset($filtered_gifts[0]) ? $filtered_gifts[0] : 0;
                                if (isset($gifts[$key])) {
                                    $user_id = get_current_user_id();
                                    $value = isset($gifts[$key]['value']) ? $gifts[$key]['value'] : 0;
                                    if ($value > 0) {
                                        $type = isset($gifts[$key]['type']) ? $gifts[$key]['type'] : 'flat';
                                        $coupon_model = new CouponModel();
                                        $new_coupon_code = strtoupper(uniqid());
                                        $new_coupon_code = chunk_split($new_coupon_code, 5, '-');
                                        $new_coupon_code = rtrim($new_coupon_code, '-');
                                        $new_coupon_code = sanitize_text_field($new_coupon_code);
                                        $data = array(
                                            'rule_id' => $spin_details->rule_id,
                                            'coupon_type' => $type,
                                            'coupon_value' => $value,
                                            'order_id' => NULL,
                                            'coupon' => $new_coupon_code,
                                            'coupon_for' => 'spin_coupon',
                                        );
                                        $coupon_id = $coupon_model->saveCoupon($data);
                                        $used = $spin_details->used + 1;
                                        $spin_model->update(array('used' => $used), $spin_details->ID);
                                        $spin_log_model = new SpinLogModel();
                                        $data = array(
                                            'spin_id' => $spin_details->ID,
                                            'user_id' => $user_id,
                                            'rule_id' => $spin_details->rule_id,
                                            'coupon_id' => $coupon_id,
                                            'array_key' => $key);
                                        $spin_log_model->saveSpinLog($data);
                                    } else {
                                        $used = $spin_details->used + 1;
                                        $spin_model->update(array('used' => $used), $spin_details->ID);
                                    }
                                    $last_spin = get_user_meta($user_id, 'last_spin_details', true);
                                    $last_spin_arr = json_decode($last_spin, true);
                                    $spin_count = isset($last_spin_arr['total']) ? $last_spin_arr['total'] : 0;
                                    $spin_time = isset($last_spin_arr['date']) ? $last_spin_arr['date'] : strtotime(date('Y-m-d'));
                                    $spin_count++;
                                    if (strtotime(date('Y-m-d')) > $spin_time) {
                                        $spin_count = 1;
                                        $spin_time = strtotime(date('Y-m-d'));
                                    }
                                    if (empty($spin_time)) {
                                        $spin_time = strtotime(date('Y-m-d'));
                                    }
                                    $user_spin_details = array(
                                        'total' => $spin_count,
                                        'date' => $spin_time
                                    );
                                    $user_spin_details_string = json_encode($user_spin_details);
                                    update_user_meta($user_id, 'last_spin_details', $user_spin_details_string);
                                    $response = array(
                                        'selector' => 'id',
                                        'winner' => $key
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        wp_send_json($response);
    }

    /**
     * Show spinner to user
     */
    function addSiteSpinner()
    {
        global $wp_query;
        $current_page_id = $wp_query->post->ID;
        $show_sw_for_non_logged_in_users = self::$config->getConfig('show_sw_for_non_logged_in_users', '1');
        $show_spinner_at = self::$config->getConfig('pages', array());
        $spin_to_win_text = self::$config->getConfig('spin_to_win_text', 'Spin and win');
        if (is_string($show_spinner_at)) {
            $show_spinner_at = (array)$show_spinner_at;
        }
        $show_spin_wheel = true;
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            if (!empty($user_id)) {
                $last_spin = get_user_meta($user_id, 'last_spin_details', true);
                if (!empty($last_spin)) {
                    $last_spin_arr = json_decode($last_spin, true);
                    $spin_count = isset($last_spin_arr['total']) ? $last_spin_arr['total'] : 0;
                    $spin_time = isset($last_spin_arr['date']) ? $last_spin_arr['date'] : strtotime(date('Y-m-d'));
                    $config_spin_count = self::$config->getConfig('number_of_spins_allow', 1);
                    if (strtotime(date('Y-m-d')) == $spin_time && $spin_count >= $config_spin_count) {
                        $show_spin_wheel = false;
                    }
                }
                $spin_model = new SpinModel();
                $un_used_spin = $spin_model->getUnUsedSpin($user_id);
                if (empty($un_used_spin)) {
                    $show_spin_wheel = false;
                }
            } else {
                $show_spin_wheel = false;
            }
        }
        if ((empty($show_spinner_at) || in_array($current_page_id, $show_spinner_at)) && !is_admin() && $show_spin_wheel) {
            if (($show_sw_for_non_logged_in_users && !is_user_logged_in()) || is_user_logged_in()) {
                $language_strings = self::$language->siteStrings();
                $roulette = '';
                if (is_user_logged_in()) {
                    $user_id = get_current_user_id();
                    if (!empty($user_id)) {
                        $spin_model = new SpinModel();
                        $un_used_spin = $spin_model->getUnUsedSpin($user_id);
                        if (!empty($un_used_spin)) {
                            $params = array(
                                'spin_details' => $un_used_spin,
                                'language' => $language_strings
                            );
                            $roulette = self::$template->render('Site/Spins/spin_to_win.php', $params)->get();
                        }
                    }
                } else {
                    $params = array(
                        'language' => $language_strings,
                        'login_url' => wp_login_url(get_permalink())
                    );
                    $roulette = self::$template->render('Site/Spins/login_to_spin.php', $params)->get();
                }
                $params = array(
                    'roulette' => $roulette,
                    'spin_to_win_text' => $spin_to_win_text
                );
                self::$template->render('Site/Spins/menu.php', $params)->display();
            }
        }
    }

    /**
     * create coupon when order status changed
     * @param $order_id
     * @param $old_status
     * @param $new_status
     */
    function orderStatusChanged($order_id, $old_status, $new_status)
    {
        $this->addSpins($order_id);
    }

    /**
     * Create Order Coupon
     * @param $order_id
     * @param array $data
     * @return bool|null
     */
    function addSpins($order_id, $data = array())
    {
        if (empty($order_id))
            return false;
        $order_id = intval($order_id);
        $order = self::$woocommerce->getOrder($order_id);
        $rule = $this->getValidRules(__CLASS__, $order);
        if (empty($rule)) {
            return false;
        }
        $spin_id = NULL;
        $spin_model = new SpinModel();
        $rule_id = $rule->id();
        $coupon_details = $spin_model->getOrderSpin($order_id, $rule_id);
        if (empty($coupon_details)) {
            $discount_details = $rule->discounts();
            $discount = (isset($discount_details[$this->name])) ? $discount_details[$this->name] : array();
            if (empty($discount)) {
                return false;
            }
            $need_dynamic_spin_points = isset($discount['need_dynamic_point']) ? $discount['need_dynamic_point'] : 0;
            if (empty($need_dynamic_spin_points)) {
                $spins = isset($discount['count']) ? $discount['count'] : 0;
            } else {
                $amount_spent = intval(isset($discount['amount_spent']) ? $discount['amount_spent'] : 0);
                $dynamic_spin_points = floatval(isset($discount['dynamic_spin_points']) ? $discount['dynamic_spin_points'] : 0);
                $spins = 0;
                if (!empty($amount_spent) && !empty($dynamic_spin_points)) {
                    $order_total = floatval(self::$woocommerce->getOrderTotal($order));
                    if (!empty($order_total) && $order_total > $amount_spent) {
                        $float_spin_points = $order_total / $amount_spent;
                        $spins = floor($float_spin_points) * $dynamic_spin_points;
                    }
                }
            }
            $rows = isset($discount['row']) ? $discount['row'] : array();
            if (empty($spins) || empty($rows)) {
                return false;
            }
            $user = self::$woocommerce->getOrderUser($order);
            if (empty($user)) {
                return false;
            }
            $new_spin_details = array(
                'user_id' => $user->ID,
                'order_id' => $order_id,
                'spins' => $spins,
                'details' => json_encode($rows),
                'rule_id' => $rule_id
            );
            $spin_id = $spin_model->saveSpin($new_spin_details);
        }
        return $spin_id;
    }

    /**
     * Create coupon when order meta updated
     * @param $order_id
     * @param $data
     */
    function orderMetaUpdated($order_id, $data)
    {
        $this->addSpins($order_id);
    }

    /**
     * Discount
     * @param array $values
     * @return array|false|string
     */
    function discounts($values)
    {
        $language_strings = self::$language->adminStrings();
        $colors_list = array(
            'ffc107' => $language_strings->discount_color_yellow,
            '008000' => $language_strings->discount_color_green,
            '3498db' => $language_strings->discount_color_blue,
            'f44336' => $language_strings->discount_color_red,
            'FF8C00' => $language_strings->discount_color_orange
        );
        $params = array(
            'spin_value' => (isset($values["count"])) ? $values["count"] : '',
            'need_dynamic_point' => (isset($values["need_dynamic_point"])) ? $values["need_dynamic_point"] : 0,
            'amount_spent' => (isset($values["amount_spent"])) ? $values["amount_spent"] : '',
            'dynamic_spin_points' => (isset($values["dynamic_spin_points"])) ? $values["dynamic_spin_points"] : '',
            'rows' => (isset($values["row"])) ? $values["row"] : array(),
            'name' => $this->ruleName(),
            'colors_list' => $colors_list,
            'form' => self::$form,
            'language' => $language_strings
        );
        return self::$template->render('Admin/Rules/Discounts/order_spin.php', $params)->get();
    }
}
<?php

namespace Waod\App\Controllers\Admin;

use Exception;
use Waod\App\Controllers\Base;
use Waod\App\Helpers\Rule;
use Waod\App\Models\DiscountModel;
use Waod\App\Models\ProductModel;
use Waod\App\Models\UserModel;

class Manage extends Base
{
    static $language_strings;

    /**
     * Manage constructor.
     */
    function __construct()
    {
        parent::__construct();
        self::$language_strings = self::$language->adminStrings();
    }

    /**
     * Order the rules by priority
     */
    function orderTheRules()
    {
        if (isset($_REQUEST['rule-no']) && !empty($_REQUEST['rule-no'])) {
            $discount_model = new DiscountModel();
            if (is_array($_REQUEST['rule-no'])) {
                $arranged_rules = array();
                $rules = $_REQUEST['rule-no'];
                foreach ($rules as $rule_id) {
                    $row = $discount_model->getWhere('priority', array($discount_model->primary_key . '=' . $rule_id), true);
                    $arranged_rules[$rule_id] = $row->priority;
                }
                $temp = $arranged_rules;
                sort($temp);
                $final_sorted_array = array_combine(array_keys($arranged_rules), $temp);
                foreach ($final_sorted_array as $rule_id => $priority) {
                    $discount_model->update(array('priority' => $priority), $rule_id);
                }
            }
        }
    }

    /**
     * select 2 search
     */
    function selectSearch()
    {
        $list = array();
        if (isset($_REQUEST['method']) && isset($_REQUEST['query'])) {
            $method = sanitize_text_field($_REQUEST['method']);
            $query = sanitize_text_field($_REQUEST['query']);
            if (!empty($method) && !empty($query)) {
                switch ($method) {
                    case 'user':
                        $user_model = new UserModel();
                        $results = $user_model->getWhere(array('display_name', 'user_email', 'ID'), array('display_name LIKE "%' . $query . '%" OR user_email LIKE "%' . $query . '%"'), false, 'ORDER BY display_name ASC', 10);
                        if (!empty($results)) {
                            foreach ($results as $result) {
                                $list[] = array(
                                    'id' => $result->ID,
                                    'text' => $result->display_name . ' (' . $result->user_email . ')'
                                );
                            }
                        }
                        break;
                    default:
                    case 'product':
                        $product_model = new ProductModel();
                        $results = $product_model->getWhere(array('post_title', 'ID'), array('post_title LIKE "%' . $query . '%"', 'post_type = "product"'), false, 'ORDER BY post_title ASC', 10);
                        if (!empty($results)) {
                            foreach ($results as $result) {
                                $list[] = array(
                                    'id' => $result->ID,
                                    'text' => "#" . $result->ID . ' ' . $result->post_title
                                );
                            }
                        }
                        break;
                }
            }
        }
        $response = array(
            'data' => $list,
            'success' => !empty($list)
        );
        wp_send_json($response);
    }

    /**
     * Save the rule
     */
    function saveRule()
    {
        $rule = new DiscountModel();
        $request_id = (isset($_REQUEST[$rule->primary_key]) && !empty($_REQUEST[$rule->primary_key])) ? $_REQUEST[$rule->primary_key] : 0;
        if (empty($request_id)) {
            $row = $rule->get('max(priority) as priority', true);
            $priority = $row->priority;
            if (empty($priority)) {
                $priority = 0;
            }
            $priority = $priority + 1;
        } else {
            $row = $rule->getWhere('priority', array($rule->primary_key . '=' . $request_id), true);
            $priority = $row->priority;
        }
        $_REQUEST['priority'] = $priority;
        if ($rule_id = $rule->save($_REQUEST)) {
            $redirect = admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG);
            $response = array(
                'error' => false,
                'message' => self::$language_strings->rules_saved_successfully,
                'id' => $rule_id,
                'redirect' => isset($_REQUEST['save_and_close_rule']) ? $redirect : '0'
            );
        } else {
            $response = array(
                'error' => true,
                'message' => self::$language_strings->unable_to_rules,
            );
        }
        wp_send_json($response);
    }

    /**
     * Save settings
     */
    function saveSettings()
    {
        $nonce = $_REQUEST['_wpnonce'];
        if (wp_verify_nonce($nonce, 'save_settings')) {
            unset($_REQUEST['action'], $_REQUEST['_wp_http_referer'], $_REQUEST['_wpnonce'], $_REQUEST['woocommerce-login-nonce'], $_REQUEST['woocommerce-reset-password-nonce']);
            $data_arr = array();
            if (!empty($_REQUEST)) {
                foreach ($_REQUEST as $key => $req) {
                    if (is_string($req)) {
                        $data_arr[$key] = stripslashes($req);
                    } else {
                        $data_arr[$key] = $req;
                    }
                }
            }
            $data = json_encode($data_arr);
            if (self::$config->save($data)) {
                $response = array(
                    'error' => false,
                    'message' => self::$language_strings->settings_saved_successfully,
                );
            } else {
                $response = array(
                    'error' => true,
                    'message' => self::$language_strings->settings_already_saved_successfully,
                );
            }
        } else {
            $response = array(
                'error' => true,
                'message' => self::$language_strings->invalid_request,
            );
        }
        wp_send_json($response);
    }

    /**
     * Handle all the pages
     */
    function handlePages()
    {
        try {
            $valid_rule_types = $this->ruleTypes();
            if (isset($_REQUEST['task']) && !empty($_REQUEST['task'])) {
                $task = $_REQUEST['task'];
                if (in_array($task, array('create', 'edit'))) {
                    if (!empty($valid_rule_types)) {
                        if ($task == "create") {
                            $values = array();
                        } else {
                            $id = isset($_GET['id']) ? $_GET['id'] : 0;
                            $id = intval($id);
                            $rule = new DiscountModel();
                            $values = $rule->getByKey($id, ARRAY_A);
                        }
                        $rule_type = NULL;
                        //Get the default rule type
                        foreach ($valid_rule_types as $type => $discount) {
                            if (isset($values['rule_type']) && $values['rule_type'] == $type) {
                                $rule_type = $type;
                                break;
                            }
                            if (!empty($discount['default'])) {
                                $rule_type = $type;
                            }
                        }
                        //If default rule type is not found then set array's first rule type as default one.
                        if (empty($rule_type)) {
                            $rule_type = array_key_first($valid_rule_types);
                        }
                        $rule_class = $valid_rule_types[$rule_type]['class'];
                        $rule = new $rule_class();
                        $rule_conditions = array();
                        if (method_exists($rule, 'conditions')) {
                            $needed_conditions = $rule->conditions();
                            $rule_conditions = $this->generateConditionDetails($needed_conditions);
                        }
                        $rule_name = $rule->ruleName();
                        $discount_data = (isset($values['discounts'])) ? $values['discounts'] : '{}';
                        $discount_values = json_decode($discount_data, true);
                        $used_conditions = (isset($values['used_conditions'])) ? $values['used_conditions'] : '{}';
                        $used_conditions = json_decode($used_conditions, true);
                        $conditions = (isset($values['conditions'])) ? $values['conditions'] : '{}';
                        $conditions = json_decode($conditions, true);
                        $params = array(
                            'choosed_rule_type' => $rule_type,
                            'language' => self::$language_strings,
                            'rule_types' => $valid_rule_types,
                            'rule' => $rule,
                            'form' => self::$form,
                            'conditions' => $conditions,
                            'used_conditions' => $used_conditions,
                            'rule_class' => $rule_class,
                            'condition_fields' => $this->renderConditionFields($rule_conditions, $values), 'condition_btn' => $this->renderConditionButtons($rule_conditions, $values),
                            'values' => $values,
                            'discount_fields' => $rule->discounts(isset($discount_values[$rule_name]) ? $discount_values[$rule_name] : array()),
                            'self' => $this);
                        self::$template->render('Admin/manage.php', $params)->display();
                    } else {
                        self::$template->render('Admin/no_rule_types.php', ['language' => self::$language_strings])->display();
                    }
                } elseif ($task == 'settings') {
                    $rule_fields = array();
                    if (!empty($valid_rule_types)) {
                        foreach ($valid_rule_types as $rule_type) {
                            $rule_class = $rule_type['class'];
                            $rule = new $rule_class();
                            $name = $rule->ruleName();
                            $rule_fields[$name] = $rule->ruleSettings();
                        }
                    }
                    $vars = array(
                        'language' => self::$language_strings,
                        'rules' => $valid_rule_types,
                        'form' => self::$form,
                        'rule_fields' => $rule_fields
                    );
                    self::$template->render('Admin/settings.php', $vars)->display();
                }
            } else {
                $model = new DiscountModel();
                $rules = $model->get(array(), false, 'ORDER BY priority ASC');
                $rules_with_obj = Rule::makeObj($rules);
                $vars = array(
                    'language' => self::$language_strings,
                    'rules' => $rules_with_obj,
                    'available_rule_types' => $valid_rule_types,
                    'create_new_link' => admin_url('admin.php?' . http_build_query(array('page' => AODFW_PLUGIN_SLUG, 'task' => 'create'))),
                    'settings_link' => admin_url('admin.php?' . http_build_query(array('page' => AODFW_PLUGIN_SLUG, 'task' => 'settings')))
                );
                self::$template->render('Admin/list.php', $vars)->display();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Update rule status
     */
    function updateRuleStatus()
    {
        if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
            $rule_id = intval($_REQUEST['id']);
            $discount_model = new DiscountModel();
            $rule = $discount_model->getByKey($rule_id);
            if (!empty($rule)) {
                $status = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : 0;
                if ($discount_model->update(array('status' => $status), $rule->ID)) {
                    $response = array(
                        'error' => false,
                        'message' => self::$language_strings->successfully_processed
                    );
                } else {
                    $response = array(
                        'error' => true,
                        'message' => self::$language_strings->not_successfully_processed
                    );
                }
            } else {
                $response = array(
                    'error' => true,
                    'message' => self::$language_strings->invalid_request
                );
            }
        } else {
            $response = array(
                'error' => true,
                'message' => self::$language_strings->invalid_request
            );
        }
        wp_send_json($response);
    }

    /**
     * remove rule
     */
    function removeRule()
    {
        if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
            $rule_id = intval($_REQUEST['id']);
            $discount_model = new DiscountModel();
            $rule = $discount_model->getByKey($rule_id);
            if (!empty($rule)) {
                if ($discount_model->delete($rule->ID)) {
                    $response = array(
                        'error' => false,
                        'message' => self::$language_strings->successfully_processed
                    );
                } else {
                    $response = array(
                        'error' => true,
                        'message' => self::$language_strings->not_successfully_processed
                    );
                }
            } else {
                $response = array(
                    'error' => true,
                    'message' => self::$language_strings->invalid_request
                );
            }
        } else {
            $response = array(
                'error' => true,
                'message' => self::$language_strings->invalid_request
            );
        }
        wp_send_json($response);
    }

    /**
     * get rule condition details
     * @param $needed_conditions
     * @return array
     */
    function generateConditionDetails($needed_conditions)
    {
        $rule_conditions = array();
        if (!empty($needed_conditions)) {
            $available_conditions = self::discountConditions();
            foreach ($needed_conditions as $key) {
                if (array_key_exists($key, $available_conditions)) {
                    $rule_conditions[$key] = $available_conditions[$key];
                }
            }
        }
        return $rule_conditions;
    }

    /**
     * render condition buttons
     * @param $conditions
     * @param $values
     * @return string
     */
    function renderConditionButtons($conditions, $values = array())
    {
        $html = '';
        if (!empty($conditions)) {
            $used_conditions = (isset($values['used_conditions'])) ? $values['used_conditions'] : '{}';
            $used_conditions = json_decode($used_conditions, true);
            foreach ($conditions as $condition) {
                $checked = (in_array($condition['name'], $used_conditions)) ? ' checked' : '';
                $disabled = (!empty($checked)) ? "hidden" : "";
                $html .= '<label class="button ' . $disabled . '" id="label-' . $condition['name'] . '">
                    <input type="checkbox" id="input-' . $condition['name'] . '"
                           value="' . $condition['name'] . '" name="used_conditions[]"
                           data-class="' . $condition['class'] . '"
                           class="hidden available-conditions" ' . $checked . '>' . $condition['label'] . '
                </label>';
            }
        }
        return $html;
    }

    /**
     * render condition fields
     * @param $conditions
     * @param $values
     * @return string
     */
    function renderConditionFields($conditions, $values)
    {
        $html = '';
        if (!empty($conditions)) {
            foreach ($conditions as $condition) {
                $condition_obj = new $condition['class']();
                if (method_exists($condition_obj, 'render')) {
                    $used_conditions = (isset($values['used_conditions'])) ? $values['used_conditions'] : '{}';
                    $used_conditions = json_decode($used_conditions, true);
                    if (in_array($condition['name'], $used_conditions)) {
                        $html .= $this->conditionHtml($condition_obj, $condition, $values);
                    }
                }
            }
        }
        return $html;
    }

    /**
     * condition html
     * @param $condition_obj
     * @param $condition
     * @param $value
     * @return string
     */
    function conditionHtml($condition_obj, $condition, $value = array())
    {
        $values = (isset($value['conditions'])) ? $value['conditions'] : '{}';
        $condition_data = json_decode($values, true);
        $condition_value = (isset($condition_data[$condition['name']])) ? $condition_data[$condition['name']] : array();
        ob_start();
        $condition_obj->render($condition_value);
        $condition_html = ob_get_clean();
        return '<section class="condition-boxes box-' . $condition['name'] . '"><div class="column twelve ">
                    <div class="remove align-right remove-condition" data-remove="' . $condition['name'] . '"><i
                            class="icon-close"></i></div>
                    <div class="input">' . $condition_html . '</div>
                </div></section>';
    }

    /**
     * Ajax get get discount conditions
     */
    function getRuleConditions()
    {
        $response = array();
        try {
            if (isset($_REQUEST['class']) && !empty($_REQUEST['class'])) {
                $class_name = str_replace('\\\\', '\\', $_REQUEST['class']);
                if (class_exists($class_name)) {
                    $rule = new $class_name();
                    $conditions = $rule->conditions();
                    $needed_conditions = $this->generateConditionDetails($conditions);
                    $response['error'] = false;
                    $response['message']['conditions'] = $this->renderConditionButtons($needed_conditions);
                    $response['message']['discounts'] = $rule->discounts(array());
                } else {
                    $response['error'] = true;
                    $response['message'] = self::$language_strings->rule_type_not_found;
                }
            } else {
                $response['error'] = true;
                $response['message'] = self::$language_strings->invalid_request;
            }
        } catch (Exception $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        }
        wp_send_json($response);
    }

    /**
     * Ajax get get discount conditions
     */
    function getCondition()
    {
        $response = array();
        try {
            if (isset($_REQUEST['class']) && !empty($_REQUEST['class'])) {
                $class_name = str_replace('\\\\', '\\', $_REQUEST['class']);
                if (class_exists($class_name)) {
                    $condition_obj = new $class_name();
                    $condition = $condition_obj->conditionDetails();
                    $response['message'] = $this->conditionHtml($condition_obj, $condition);
                } else {
                    $response['error'] = true;
                    $response['message'] = self::$language_strings->rule_type_not_found;
                }
            } else {
                $response['error'] = true;
                $response['message'] = self::$language_strings->invalid_request;
            }
        } catch (Exception $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        }
        wp_send_json($response);
    }

    /**
     * Add admin footer text
     * @param $footer_text
     * @return string
     */
    function addAdminFooterText($footer_text)
    {
        if (isset($_REQUEST['page'])) {
            if ($_REQUEST['page'] == AODFW_PLUGIN_SLUG) {
                $footer_text = self::$language_strings->thanks_for_using_plugin;
            }
        }
        return $footer_text;
    }

    /**
     * Add admin footer text
     */
    function addAdminFooterVersion()
    {
        if (isset($_REQUEST['page'])) {
            if ($_REQUEST['page'] == AODFW_PLUGIN_SLUG) {
                echo self::$language_strings->version . ' ' . AODFW_VERSION;
            }
        }
    }

    /**
     * Add admin scripts
     * @param $hook
     */
    function addAdminScripts($hook)
    {
        if ($hook != 'woocommerce_page_' . AODFW_PLUGIN_SLUG) {
            return;
        }
        $languages = self::$language->jsStrings();
        //add ui-kit
        wp_enqueue_style(AODFW_PLUGIN_SLUG . '-ui-kit', AODFW_URL . 'Assets/Admin/Css/uptown.css', array(), '1.0.0');
        wp_enqueue_style(AODFW_PLUGIN_SLUG . '-ui-notify', AODFW_URL . 'Assets/Admin/Css/noty.css', array(), '1.0.0');
        wp_enqueue_style(AODFW_PLUGIN_SLUG . '-multi-select', AODFW_URL . 'Assets/Admin/Css/select2.min.css');
        wp_enqueue_style(AODFW_PLUGIN_SLUG . '-date-range-picker', AODFW_URL . 'Assets/Admin/Css/datepicker.min.css', array(), '1.0.0');
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-moment', AODFW_URL . 'Asset`s/Admin/Js/moment.min.js', array(), '1.0.0', true);
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-date-range-picker', AODFW_URL . 'Assets/Admin/Js/datepicker.min.js', array(), '1.0.0', true);
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-ui-notify', AODFW_URL . 'Assets/Admin/Js/noty.min.js', array(), '1.0.0', true);
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-multi-select', AODFW_URL . 'Assets/Admin/Js/select2.min.js');
//        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-custom-editor', AODFW_URL . 'Assets/Admin/Js/Tinymce/tinymce.min.js');
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-waod-jquery-ui', AODFW_URL . 'Assets/Admin/Js/jquery-ui.min.js', array(), '1.0.0', true);
        wp_enqueue_script(AODFW_PLUGIN_SLUG . '-main', AODFW_URL . 'Assets/Admin/Js/main.js', array(), '1.0.0', true);
        wp_localize_script(AODFW_PLUGIN_SLUG . '-main', 'rule_discount', $languages);
    }
}
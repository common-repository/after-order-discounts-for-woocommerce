<?php

namespace Waod\App\Controllers\Rules;

use Waod\App\Helpers\Rule;
use Waod\App\Models\DiscountModel;

abstract class Base extends \Waod\App\Controllers\Base
{
    static $language_strings;

    /**
     * Base constructor.
     */
    public function __construct()
    {
        parent::__construct();
        self::$language_strings = self::$language->adminStrings();
    }

    /**
     * Contains the detail about Rule
     * @return mixed
     */
    abstract function ruleDetails();

    /**
     * settings of the rule
     * @return mixed
     */
    abstract function ruleSettings();

    /**
     * get the rule name
     * @return mixed
     */
    abstract function ruleName();

    /**
     * gives the array of conditions that required for rules
     * @return array
     */
    abstract function conditions();

    /**
     * returns the array of rule settings for  rules
     * @param $value array
     * @return array
     */
    abstract function discounts($value);

    /**
     * hooks needed for the smooth running of rules
     * @return mixed
     */
    abstract function hooks();

    /**
     * get all valid rules
     * @param $class
     * @param $order
     * @return array|bool
     */
    final protected function getValidRules($class, $order)
    {
        $valid_rules = array();
        if (class_exists($class)) {
            $discount_model = new DiscountModel();
            $rules = $discount_model->getWhere('*', array('class ="' . addslashes($class) . '"', 'status = 1'), false, ' ORDER BY priority ASC');
            if (empty($rules)) {
                return false;
            }
            $rule_helper = new Rule();
            $rules_obj = $rule_helper->makeObj($rules);
            $base_controller = new \Waod\App\Controllers\Base();
            $available_conditions = $base_controller->discountConditions();
            if (empty($available_conditions)) {
                return false;
            }
            if (!empty($rules_obj)) {
                foreach ($rules_obj as $rule_obj) {
                    $rule_conditions = $rule_obj->usedConditions();
                    if (empty($rule_conditions)) {
                        array_push($valid_rules, $rule_obj);
                        continue;
                    }
                    $rule_conditions_data = $rule_obj->conditions();
                    $condition_result = array();
                    foreach ($rule_conditions as $rule_condition) {
                        if (array_key_exists($rule_condition, $available_conditions)) {
                            $condition_values = (isset($rule_conditions_data[$rule_condition])) ? $rule_conditions_data[$rule_condition] : array();
                            $condition_obj = new $available_conditions[$rule_condition]['class']();
                            $result = $condition_obj->check($order, $condition_values);
                            if (is_bool($result)) {
                                array_push($condition_result, $result);
                            }
                        }
                    }
                    $condition_relationship = isset($rule_conditions_data['relation']) ? $rule_conditions_data['relation'] : 'and';
                    switch ($condition_relationship) {
                        case 'or':
                            if (in_array(true, $condition_result)) {
                                array_push($valid_rules, $rule_obj);
                            }
                            break;
                        default:
                        case 'and':
                            if (!in_array(false, $condition_result)) {
                                array_push($valid_rules, $rule_obj);
                            }
                            break;
                    }
                }
            }
        }
        return reset($valid_rules);
    }
}
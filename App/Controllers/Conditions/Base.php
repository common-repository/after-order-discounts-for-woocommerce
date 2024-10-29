<?php

namespace Waod\App\Controllers\Conditions;
abstract class Base extends \Waod\App\Controllers\Base
{
    static $in_list_condition_array, $language_strings;

    /**
     * Base constructor.
     */
    public function __construct()
    {
        parent::__construct();
        self::$language_strings = self::$language->adminStrings();
        self::$in_list_condition_array = array(
            'must_in' => self::$language_strings->in_list,
            'must_not_in' => self::$language_strings->not_in_list
        );
    }

    /**
     * an array of condition details
     * @return mixed
     */
    abstract function conditionDetails();

    /**
     * render Conditions
     * @param $value - condition value
     * @return mixed
     */
    abstract function render($value);

    /**
     * Check and validate condition
     * @param $order
     * @param $condition_data
     * @return mixed
     */
    abstract function check($order, $condition_data);

    /**
     * Arithmetic operation
     * @param $compare_value
     * @param $values
     * @return bool
     */
    function validateArithmeticOperation($compare_value, $values)
    {
        $value = floatval(isset($values['value']) ? $values['value'] : 0);
        $compare_value = floatval($compare_value);
        $operator = isset($values['operator']) ? $values['operator'] : 'greater_then';
        switch ($operator) {
            case 'greater_than':
                return ($value < $compare_value);
                break;
            case 'lesser_than':
                return ($value > $compare_value);
                break;
            case 'lesser_than_or_equal':
                return ($value >= $compare_value);
                break;
            case 'greater_than_or_equal':
                return ($value <= $compare_value);
                break;
            case 'equal_to':
                return ($value == $compare_value);
                break;
            case 'between':
                $min_value = floatval(isset($values['min_value']) ? $values['min_value'] : 0);
                $max_value = floatval(isset($values['max_value']) ? $values['max_value'] : 0);
                return ($min_value > $compare_value && $max_value < $compare_value);
                break;
            default:
                return false;
        }
    }

    /**
     * validate list operation
     * @param $operator
     * @param $compare_value
     * @param $values
     * @return bool
     */
    function validateListOperation($operator, $compare_value, $values)
    {
        $values = (array)$values;
        switch ($operator) {
            case 'must_not_in':
                if (is_array($compare_value) || is_object($compare_value)) {
                    $compare_value = (array)$compare_value;
                    $values = (array)$values;
                    return (bool)(count(array_intersect($compare_value, $values)) == 0);
                } else {
                    return (bool)!in_array($compare_value, $values);
                }
                break;
            default:
            case 'must_in':
                if (is_array($compare_value) || is_object($compare_value)) {
                    $compare_value = (array)$compare_value;
                    $values = (array)$values;
                    return (bool)(count(array_intersect($compare_value, $values)) > 0);
                } else {
                    return (bool)in_array($compare_value, $values);
                }
                break;
        }
    }
}
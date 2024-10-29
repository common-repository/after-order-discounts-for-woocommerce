<?php

namespace Waod\App\Controllers\Conditions;
class OrderPaymentMethods extends Base
{
    function conditionDetails()
    {
        return array(
            'name' => 'order_payment_methods',
            'label' => self::$language_strings->order_payment_methods,
            'class' => __CLASS__
        );
    }

    function render($values)
    {
        $gateways = self::$woocommerce->getAvailablePaymentGateways();
        $gateway_list = array();
        if (!empty($gateways)) {
            foreach ($gateways as $key => $payment) {
                $gateway_list[$key] = $payment->title;
            }
        }
        $params = array(
            'language' => self::$language_strings,
            'form' => self::$form,
            'gateway_list' => $gateway_list,
            'values' => $values,
            'in_list_condition_arr' => self::$in_list_condition_array
        );
        self::$template->render('Admin/Rules/Conditions/order_payment_method.php', $params)->display();
    }

    function check($order, $values)
    {
        $payment_method = self::$woocommerce->getOrderPaymentMethod($order);
        if (!empty($payment_method)) {
            $rows = isset($values['row']) ? $values['row'] : array();
            if (!empty($rows)) {
                $condition_result = array();
                $relation = isset($values['relation']) ? $values['relation'] : 'and';
                foreach ($rows as $key => $row) {
                    if (is_int($key)) {
                        $operator = (isset($row['operator'])) ? $row['operator'] : 'must_in';
                        $value = (isset($row['value'])) ? $row['value'] : array();
                        $result = $this->validateListOperation($operator, $payment_method, $value);
                        array_push($condition_result, $result);
                    }
                }
                switch ($relation) {
                    case 'or':
                        return in_array(true, $condition_result);
                        break;
                    default:
                    case 'and':
                        return !in_array(false, $condition_result);
                        break;
                }
            } else {
                return true;
            }
        }
        return false;
    }
}
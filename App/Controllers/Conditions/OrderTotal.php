<?php

namespace Waod\App\Controllers\Conditions;
class OrderTotal extends Base
{
    function conditionDetails()
    {
        return array(
            'name' => 'order_total',
            'label' => self::$language_strings->order_total,
            'class' => __CLASS__
        );
    }

    function render($value)
    {
        $params = array(
            'language' => self::$language_strings,
            'form' => self::$form,
            'value' => $value,
            'in_list_condition_arr' => self::$in_list_condition_array
        );
        self::$template->render('Admin/Rules/Conditions/order_total.php', $params)->display();
    }

    function check($order, $values)
    {
        $order_total = self::$woocommerce->getOrderTotal($order);
        return $this->validateArithmeticOperation($order_total, $values);
    }
}
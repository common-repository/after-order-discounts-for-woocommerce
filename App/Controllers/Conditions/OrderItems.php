<?php

namespace Waod\App\Controllers\Conditions;

use Waod\App\Models\ProductModel;

class OrderItems extends Base
{
    static $products = array();

    function conditionDetails()
    {
        return array(
            'name' => 'order_items',
            'label' => self::$language_strings->order_items,
            'class' => __CLASS__
        );
    }

    function render($values)
    {
        $params = array(
            'language' => self::$language_strings,
            'form' => self::$form,
            'product_model' => new ProductModel(),
            'values' => $values,
            'in_list_condition_arr' => self::$in_list_condition_array
        );
        self::$template->render('Admin/Rules/Conditions/order_items.php', $params)->display();
    }

    function check($order, $values)
    {
        $items = self::$woocommerce->getOrderItems($order);
        if (!empty($items)) {
            $order_items = array();
            foreach ($items as $item) {
                $item_id = self::$woocommerce->getItemId($item);
                array_push($order_items, $item_id);
            }
            $rows = isset($values['row']) ? $values['row'] : array();
            if (!empty($rows)) {
                $condition_result = array();
                $relation = isset($values['relation']) ? $values['relation'] : 'and';
                foreach ($rows as $key => $row) {
                    if (is_int($key)) {
                        $operator = (isset($row['operator'])) ? $row['operator'] : 'must_in';
                        $value = (isset($row['products'])) ? $row['products'] : array();
                        $result = $this->validateListOperation($operator, $order_items, $value);
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
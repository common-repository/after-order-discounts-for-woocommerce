<?php

namespace Waod\App\Controllers\Conditions;
class OrderCategories extends Base
{
    function conditionDetails()
    {
        return array(
            'name' => 'order_categories',
            'label' => self::$language_strings->order_categories,
            'class' => __CLASS__
        );
    }

    function render($values)
    {
        $product_categories = get_terms('product_cat', array(
            'orderby' => 'name',
            'order' => 'asc',
            'hide_empty' => false,
        ));
        $category_list = array();
        if (!empty($product_categories)) {
            foreach ($product_categories as $category) {
                $category_list[$category->slug] = $category->name;
            }
        }
        $params = array(
            'language' => self::$language_strings,
            'form' => self::$form,
            'category_list' => $category_list,
            'values' => $values,
            'in_list_condition_arr' => self::$in_list_condition_array
        );
        self::$template->render('Admin/Rules/Conditions/order_category.php', $params)->display();
    }

    function check($order, $values)
    {
        $items = self::$woocommerce->getOrderItems($order);
        if (!empty($items)) {
            $order_item_categories = array();
            foreach ($items as $item) {
                $item_id = self::$woocommerce->getItemId($item);
                $categories = self::$woocommerce->getItemCategories($item_id);
                if (!empty($categories)) {
                    foreach ($categories as $category) {
                        if (!in_array($category, $order_item_categories)) {
                            array_push($order_item_categories, $category);
                        }
                    }
                }
            }
            $rows = isset($values['row']) ? $values['row'] : array();
            if (!empty($rows)) {
                $condition_result = array();
                $relation = isset($values['relation']) ? $values['relation'] : 'and';
                foreach ($rows as $key => $row) {
                    if (is_int($key)) {
                        $operator = (isset($row['operator'])) ? $row['operator'] : 'must_in';
                        $value = (isset($row['value'])) ? $row['value'] : array();
                        $result = $this->validateListOperation($operator, $order_item_categories, $value);
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
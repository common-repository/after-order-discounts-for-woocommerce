<?php

namespace Waod\App\Models;

class CouponModel extends Base
{
    public $primary_key = 'ID', $fillable = array(
        'status' => 1,
        'email_opened' => 0,
        'is_used' => 0,
        'rule_id' => 0,
        'sent_to' => '',
        'coupon_type' => '',
        'coupon_value' => 0,
        'order_id' => NULL,
        'coupon' => '',
        'coupon_for' => 'order_coupon',
        'usage_restriction' => '{}',
    ), $table = 'aodfw_coupons';

    /**
     * get coupon details by coupon code
     * @param $coupon_code
     * @return mixed|null
     */
    function getCoupon($coupon_code)
    {
        if (empty($coupon_code)) {
            return NULL;
        }
        return $this->getWhere('*', 'coupon = "' . $coupon_code . '"', true);
    }

    /**
     * get the coupon code by order id
     * @param $order_id
     * @param $rule_id
     * @return mixed|null
     */
    function getOrderCoupon($order_id, $rule_id = NULL)
    {
        if (empty($order_id)) {
            return NULL;
        }
        $conditions = array('order_id = ' . $order_id);
        if (!empty($rule_id)) {
            array_push($conditions, 'rule_id = ' . $rule_id);
            array_push($conditions, 'coupon_for = "order_coupon"');
        }
        return $this->getWhere('*', $conditions, true);
    }

    function saveCoupon($data)
    {
        $data = $this->makeFillable($data);
        if (!empty($data)) {
            $query = $this->insertPrepare($data);
            self::$db->query($query);
            return self::$db->insert_id;
        }
        return NULL;
    }
}
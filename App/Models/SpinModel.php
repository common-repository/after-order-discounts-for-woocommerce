<?php

namespace Waod\App\Models;
class SpinModel extends Base
{
    public $primary_key = 'ID', $fillable = array(
        'status' => 1,
        'spins' => 0,
        'used' => 0,
        'rule_id' => 0,
        'details' => '{}',
        'order_id' => NULL,
        'user_id' => NULL,
    ), $table = 'aodfw_spins';

    /**
     * get the coupon code by order id
     * @param $order_id
     * @param $rule_id
     * @return mixed|null
     */
    function getOrderSpin($order_id, $rule_id = NULL)
    {
        if (empty($order_id)) {
            return NULL;
        }
        $conditions = array('order_id = ' . $order_id);
        if (!empty($rule_id)) {
            array_push($conditions, 'rule_id = ' . $rule_id);
        }
        return $this->getWhere('*', $conditions, true);
    }

    function saveSpin($data)
    {
        $data = $this->makeFillable($data);
        if (!empty($data)) {
            $query = $this->insertPrepare($data);
            self::$db->query($query);
            return self::$db->insert_id;
        }
        return NULL;
    }

    function getUnUsedSpin($user_id)
    {
        if (empty($user_id)) {
            return NULL;
        }
        $conditions = array(
            'user_id = ' . $user_id,
            'spins > used'
        );
        return $this->getWhere('*', $conditions, true, 'ORDER BY ' . $this->primary_key . ' DESC');
    }
}
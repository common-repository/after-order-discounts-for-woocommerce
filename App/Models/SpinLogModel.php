<?php

namespace Waod\App\Models;
class SpinLogModel extends Base
{
    public $primary_key = 'ID', $fillable = array(
        'spin_id' => 0,
        'user_id' => 0,
        'rule_id' => 0,
        'coupon_id' => 0,
        'array_key' => 0,
    ), $table = 'aodfw_spins_log';

    function saveSpinLog($data)
    {
        $data = $this->makeFillable($data);
        if (!empty($data)) {
            $query = $this->insertPrepare($data);
            self::$db->query($query);
            return self::$db->insert_id;
        }
        return NULL;
    }

    function getGifts($user_id)
    {
        $coupon_model = new CouponModel();
        $spin_table = $coupon_model->table;
        $query = "SELECT * FROM " . $this->table . " AS t1 INNER JOIN " . $spin_table . " AS t2 ON t1.coupon_id=t2.ID WHERE t1.user_id=" . $user_id;
        return self::$db->get_results($query);
    }
}
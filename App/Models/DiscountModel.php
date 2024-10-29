<?php

namespace Waod\App\Models;
class DiscountModel extends Base
{
    public $primary_key = 'ID', $fillable = array(
        'conditions' => '{}',
        'discounts' => '{}',
        'rule_type' => '',
        'status' => 1,
        'priority' => 1,
        'title' => 'Untitled rule',
        'start_from' => '',
        'end_on' => '',
        'class' => '',
        'used_conditions' => '{}',
        'description' => '',
    ), $table = 'aodfw_rules';
}
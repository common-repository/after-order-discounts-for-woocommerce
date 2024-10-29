<?php

namespace Waod\App\Models;
class ProductModel extends Base
{
    public $primary_key = 'ID', $fillable = array(), $table = 'posts';

    /**
     * get product title
     * @param $key
     * @return mixed
     */
    function getTitle($key)
    {
        return self::$db->get_row('Select post_title From ' . $this->table . ' Where ' . $this->primary_key . ' = ' . $key);
    }
}

<?php

namespace Waod\App\Models;
class UserModel extends Base
{
    public $primary_key = 'ID', $fillable = array(), $table = 'users';

    /**
     * get product title
     * @param $key
     * @return mixed
     */
    function getName($key)
    {
        return self::$db->get_row('Select display_name,user_email From ' . $this->table . ' Where ' . $this->primary_key . ' = ' . $key);
    }
}

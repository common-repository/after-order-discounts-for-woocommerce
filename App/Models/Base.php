<?php

namespace Waod\App\Models;
abstract class Base
{
    public static $db, $prefix;
    public $fillable = array('in'), $primary_key, $table;

    public function __construct()
    {
        global $wpdb;
        self::$prefix = $wpdb->prefix;
        $this->table = self::$prefix . $this->table;
        self::$db = $wpdb;
    }

    /**
     * get all rules
     * @param array $select
     * @param bool $single
     * @param string $order_by
     * @return mixed
     */
    function get($select = array(), $single = false, $order_by = '')
    {
        $select_str = $this->makeSelect($select);
        $select_query = 'SELECT ' . $select_str . ' FROM ' . $this->table . ' ' . $order_by;
        if ($single) {
            return self::$db->get_row($select_query);
        }
        return self::$db->get_results($select_query);
    }

    /**
     * get by where condition
     * @param array $select
     * @param array $where
     * @param bool $single
     * @param string $order_by
     * @param int $limit
     * @return mixed
     */
    function getWhere($select = array(), $where = array(), $single = false, $order_by = '', $limit = 0)
    {
        $select_str = $this->makeSelect($select);
        $where_str = '';
        if (!empty($where) && is_array($where)) {
            $where_str = ' WHERE ' . implode($where, ' AND ');
        } elseif (is_string($where)) {
            $where_str = ' WHERE ' . $where;
        }
        $limit_query = '';
        if (!empty($limit)) {
            $limit_query = ' LIMIT ' . $limit;
        }
        $select_query = 'SELECT ' . $select_str . ' FROM ' . $this->table . ' ' . $where_str . ' ' . $order_by . ' ' . $limit_query;
        if ($single) {
            return self::$db->get_row($select_query, OBJECT);
        }
        return self::$db->get_results($select_query, OBJECT);
    }

    /**
     * get select str
     * @param $select
     * @return string
     */
    function makeSelect($select)
    {
        $select_str = '*';
        if (!empty($select) && is_array($select)) {
            $select_str = implode($select, ', ');
        } elseif (is_string($select)) {
            $select_str = $select;
        }
        return $select_str;
    }

    /**
     * Get data by primary key
     * @param $key
     * @param $get_as
     * @return mixed
     */
    function getByKey($key, $get_as = OBJECT)
    {
        return self::$db->get_row('Select * From ' . $this->table . ' Where ' . $this->primary_key . ' = ' . $key, $get_as);
    }

    /**
     * Update the row
     * @param $data
     * @param $key
     * @return mixed
     */
    function update($data, $key)
    {
        $set_query = "";
        if (is_string($data)) {
            $set_query = $data;
        } elseif (is_array($data) || is_object($data)) {
            array_walk($data,
                function (&$v, $k) {
                    $v = $k . ' = ' . $v;
                }
            );
            $set_query = implode(',', $data);
        }
        $update_query = 'UPDATE `' . $this->table . '` SET ' . $set_query . ' WHERE ' . $this->primary_key . ' = ' . $key . ';';
        return self::$db->query($update_query);
    }

    /**
     * Delete the row
     * @param $key
     * @return mixed
     */
    function delete($key)
    {
        $remove_query = 'DELETE FROM `' . $this->table . '`  WHERE ' . $this->primary_key . ' = ' . $key . ';';
        return self::$db->query($remove_query);
    }

    /**
     * Save the data
     * @param $data
     * @return string|null
     */
    function save($data)
    {
        $primary_key_value = (isset($data[$this->primary_key])) ? intval($data[$this->primary_key]) : 0;
        $rule = $this->getByKey($primary_key_value);
        $data = $this->makeFillable($data);
        if (!empty($data)) {
            if (empty($rule)) {
                $query = $this->insertPrepare($data);
                self::$db->query($query);
                return self::$db->insert_id;
            } else {
                $key = $this->primary_key;
                $query = $this->updatePrepare($data, $rule->$key);
                self::$db->query($query);
                return $rule->$key;
            }
        }
        return NULL;
    }

    /**
     * Assign field type by is value
     * @param $data
     * @return array
     */
    function assignFieldType($data)
    {
        $field_types = array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (is_int($value)) {
                    $field_types[$key] = '%d';
                } elseif (is_float($value)) {
                    $field_types[$key] = '%f';
                } else {
                    $field_types[$key] = '%s';
                }
            }
        }
        return $field_types;
    }

    /**
     * prepare the insert SQL
     * @param $data
     * @return string
     */
    function insertPrepare($data)
    {
        $field_types = $this->assignFieldType($data);
        $insert_query = 'INSERT INTO `' . $this->table . '` (' . implode(array_keys($field_types), ", ") . ') VALUES (' . implode($field_types, ", ") . ');';
        return self::$db->prepare($insert_query, $data);
    }

    /**
     * prepare the insert SQL
     * @param $data
     * @param $key
     * @return string
     */
    function updatePrepare($data, $key)
    {
        $field_types = $this->assignFieldType($data);
        array_walk($field_types,
            function (&$v, $k) {
                $v = $k . ' = ' . $v;
            }
        );
        $set = implode(',', $field_types);
        $update_query = 'UPDATE `' . $this->table . '` SET ' . $set . ' WHERE ' . $this->primary_key . ' = ' . $key . ';';
        return self::$db->prepare($update_query, $data);
    }

    /**
     * make the fillable request
     * @param $data
     * @return array
     */
    protected function makeFillable($data)
    {
        $data = array_filter($data, function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $request = array();
        if (!empty($data)) {
            $fillable = array_keys($this->fillable);
            foreach ($data as $key => $value) {
                if (in_array($key, $fillable)) {
                    if (is_array($value) || is_object($value)) {
                        $value = json_encode($value);
                    }
                    $request[$key] = stripcslashes($value);
                }
            }
        }
        return array_merge($this->fillable, $request);
    }
}
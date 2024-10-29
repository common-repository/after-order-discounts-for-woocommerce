<?php

namespace Waod\App\Helpers;
class Rule
{
    public $rule;

    /**
     * Rule constructor.
     * @param null $rule_data
     */
    function __construct($rule_data = NULL)
    {
        if (!empty($rule_data)) {
            $this->rule = $rule_data;
        }
        return $this;
    }

    /**
     * get rule object from rule
     * @param $available_rules
     * @return array
     */
    static function makeObj($available_rules)
    {
        $rule_list = array();
        if (!empty($available_rules)) {
            foreach ($available_rules as $rule) {
                $rule_obj = new self($rule);
                array_push($rule_list, $rule_obj);
            }
        }
        return $rule_list;
    }

    /**
     * get the rule ID
     * @return int|null
     */
    function id()
    {
        if (isset($this->rule->ID)) {
            return $this->rule->ID;
        }
        return NULL;
    }

    /**
     * Rule title
     * @return string|null
     */
    function title()
    {
        if (isset($this->rule->title)) {
            return $this->rule->title;
        }
        return NULL;
    }

    /**
     * Rule title
     * @return string|null
     */
    function startsOn()
    {
        if (isset($this->rule->start_from)) {
            return $this->rule->start_from;
        }
        return NULL;
    }

    /**
     * Rule title
     * @return string|null
     */
    function endsOn()
    {
        if (isset($this->rule->end_on)) {
            return $this->rule->end_on;
        }
        return NULL;
    }

    /**
     * get rule type
     * @return null
     */
    function type()
    {
        if (isset($this->rule->rule_type)) {
            return $this->rule->rule_type;
        }
        return NULL;
    }

    /**
     * Rule is active or not
     * @return bool
     */
    function isActive()
    {
        if (isset($this->rule->status)) {
            if ($this->rule->status == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Rule conditions
     * @return array
     */
    function usedConditions()
    {
        if (isset($this->rule->used_conditions)) {
            if ($this->rule->used_conditions == '[]' || $this->rule->used_conditions == '{}') {
                return array();
            } else {
                return json_decode($this->rule->used_conditions, true);
            }
        }
        return array();
    }

    /**
     * Rule conditions
     * @return array
     */
    function conditions()
    {
        if (isset($this->rule->conditions)) {
            if ($this->rule->conditions == '[]' || $this->rule->conditions == '{}') {
                return array();
            } else {
                return json_decode($this->rule->conditions, true);
            }
        }
        return array();
    }

    /**
     * Rule discounts
     * @return array
     */
    function discounts()
    {
        if (isset($this->rule->discounts)) {
            if ($this->rule->discounts == '[]' || $this->rule->discounts == '{}') {
                return array();
            } else {
                return json_decode($this->rule->discounts, true);
            }
        }
        return array();
    }

    /**
     * get the status of rule
     * @return int
     */
    function status()
    {
        //1=>active,0=>expired,2=>inactive
        if (empty($this->startsOn()) && empty($this->endsOn())) {
            return 1;
        } elseif (empty($this->startsOn()) && !empty($this->endsOn())) {
            $end_date = strtotime($this->endsOn());
            if ($end_date > current_time('timestamp')) {
                return 1;
            } else {
                return 0;
            }
        } elseif (!empty($this->startsOn()) && empty($this->endsOn())) {
            $start_date = strtotime($this->startsOn());
            if ($start_date < current_time('timestamp')) {
                return 1;
            } else {
                return 2;
            }
        } else {
            $start_date = strtotime($this->startsOn());
            $end_date = strtotime($this->endsOn());
            $today = current_time('timestamp');
            if ($start_date < $today && $end_date > $today) {
                return 1;
            } elseif ($start_date > $today && $end_date > $today) {
                return 2;
            } else {
                return 0;
            }
        }
    }
}
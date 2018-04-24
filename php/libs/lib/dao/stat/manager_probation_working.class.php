<?php
/**
 * 见习市场经理作业查询
 */
namespace Dao\Stat;
use \Dao;

class Manager_probation_working extends Stat {

    protected static $_instance = null;

    /**
     * @return Manager_probation_working
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
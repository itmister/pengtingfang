<?php
/**
 * 区域管理与区域业绩明细
 */
namespace Dao\Stat;
use \Dao;

class Manager_area_performance extends Stat {

    protected static $_instance = null;

    /**
     * @return Manager_area_performance
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

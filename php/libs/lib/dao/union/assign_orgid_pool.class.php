<?php
namespace Dao\Union;
use \Dao;

/**
 * @package Dao\Union
 */
class Assign_Orgid_Pool extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Assign_Orgid_Pool
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
<?php
namespace Dao\Union;
use Dao;

/**
 * Class Config_gift
 * @package Dao\Config_gift
 */
class Config_gift extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Config_gift
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
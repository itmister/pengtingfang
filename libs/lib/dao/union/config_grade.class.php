<?php
namespace Dao\Union;
use Dao;

/**
 * Class Config_grade
 * @package Dao\Config_grade
 */
class Config_grade extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Config_grade
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
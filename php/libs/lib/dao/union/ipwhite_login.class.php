<?php
namespace Dao\Union;
use \Dao;

/**
 * @package Dao\Union
 */
class Ipwhite_login extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Ipwhite_login
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

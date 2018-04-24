<?php
namespace Dao\Union;
use Dao;

/**
 * Class User_gift_log
 * @package Dao\User_gift_log
 */
class User_gift_log extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\User_gift_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
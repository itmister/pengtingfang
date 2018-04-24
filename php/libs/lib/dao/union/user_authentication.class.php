<?php
namespace Dao\Union;
use Dao;

/**
 * Class User_authentication
 * @package Dao\Act_Badge
 */
class User_authentication extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\User_authentication
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
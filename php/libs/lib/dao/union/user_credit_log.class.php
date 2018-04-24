<?php
namespace Dao\Union;
use \Dao;
class User_Credit_Log extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\User_Credit_Log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

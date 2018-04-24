<?php
namespace Dao\Big_customer;
use \Dao;
class User extends Big_customer {

    protected static $_instance = null;
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
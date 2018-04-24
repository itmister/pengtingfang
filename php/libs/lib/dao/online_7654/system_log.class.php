<?php
namespace Dao\Online_7654;
use \Dao;
class System_log extends Online_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Online_7654\User_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

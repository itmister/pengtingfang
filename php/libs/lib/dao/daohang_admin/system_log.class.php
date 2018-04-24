<?php
namespace Dao\Daohang_admin;
use \Dao;
class System_log extends Daohang_admin {

    protected static $_instance = null;

    /**
     * @return Dao\Daohang_admin\User_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

<?php
namespace Dao\Daohang_admin;
use \Dao;
class Data_total extends Daohang_admin {

    protected static $_instance = null;


    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}

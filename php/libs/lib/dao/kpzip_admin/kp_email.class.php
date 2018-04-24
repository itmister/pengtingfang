<?php
namespace Dao\Kpzip_admin;
use \Dao;
class Kp_email extends Kpzip_admin {
    protected static $_instance = null;

    /**
     * @return Kp_email
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

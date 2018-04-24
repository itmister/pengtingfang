<?php
namespace Dao\Mykzip_admin\Stat;
use \Dao;
class Ip_library extends \Dao\Mykzip_admin\Mykzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Mykzip_admin\Ip_library
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

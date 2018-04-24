<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao;
class Rate_base extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Rate_base
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

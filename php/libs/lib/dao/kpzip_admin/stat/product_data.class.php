<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao;
class Product_data extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Product_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

<?php
namespace Dao\Winhome;
use \Dao;
class Product_data_uuid extends Winhome {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Product_data_uuid
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
<?php
namespace Dao\Uxiake_admin\Ux;
use \Dao\Uxiake_admin\Uxiake_admin;
class Product_data extends Uxiake_admin{
    protected static $_instance = null;

    /**
     * @return Product_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

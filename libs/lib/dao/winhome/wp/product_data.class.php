<?php
namespace Dao\Winhome\Wp;
use \Dao;
class Product_data extends Wp{

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Wp\Product_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

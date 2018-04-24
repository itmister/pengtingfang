<?php
namespace Dao\Winhome\Wp;
use \Dao;
class Rate_base extends WP {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Wp\Rate_base
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

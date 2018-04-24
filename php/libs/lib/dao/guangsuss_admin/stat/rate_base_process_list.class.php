<?php
namespace Dao\Guangsuss_admin\Stat;
use \Dao;
class Rate_base_process_list extends \Dao\Guangsuss_admin\Guangsuss_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Rate_base_process_list
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

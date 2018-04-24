<?php
namespace Dao\Winhome;
use \Dao;
class Rate_base_process_list extends Winhome {

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

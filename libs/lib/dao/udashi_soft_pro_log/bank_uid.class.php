<?php
namespace Dao\Udashi_soft_pro_log;
use \Dao;
class Bank_uid extends Udashi_soft_pro_log {

    protected static $_instance = null;
    /**
     * @return Dao\Udashi_soft_pro_log\Bank_uid
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }


}

<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Web_pv_ip extends \Dao\Udashi_admin\Udashi_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Udashi_admin\Stat\Web_pv_ip
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

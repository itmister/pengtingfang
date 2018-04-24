<?php
/**
 *单价修改记录模型
 */
namespace Dao\Revenue_7654;
use \Dao;
class Price_change_log extends Revenue_7654 {
    protected static $_instance = null;

    /**
     * @return Price_change_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}

<?php
/**
 * 单价记录表
 * @author caolei
 * 
 */
namespace Dao\Online_7654;

class Soft_price_log extends Online_7654 {

    protected static $_instance = null;

    /**
     * @return Soft_num
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}
<?php
namespace Dao\Union;
use Dao;

/**
 *  费渠道号
 * Class void_channel
 * @package Dao\Union
 */
class Void_channel extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Weixin_send_record
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}
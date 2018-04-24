<?php
namespace Dao\Union;
use Dao;

/**
 *  发送微信成功消息
 * Class Weixin_send_record
 * @package Dao\Union
 */
class Weixin_send_record extends Union {

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
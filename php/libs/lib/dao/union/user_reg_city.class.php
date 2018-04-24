<?php
namespace Dao\Union;
use Dao;

/**
 *  发送微信成功消息
 * Class User_reg_city
 * @package Dao\Union
 */
class User_reg_city extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\User_reg_city
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}
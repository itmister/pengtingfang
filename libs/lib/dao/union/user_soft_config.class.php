<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 14:51
 */
namespace Dao\Union;
use Dao;

/**
 * Class User_soft_config
 * @package Dao\User_soft_config
 */
class User_soft_config extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\User_soft_config
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     *
     */



}
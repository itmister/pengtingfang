<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/12
 * Time: 10:54
 */
namespace Dao\Union;
use Dao;

/**
 * 微信H5页面活动
 * Class Act_h5
 * @package Dao\Union
 */
class Act_h5 extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Act_h5
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
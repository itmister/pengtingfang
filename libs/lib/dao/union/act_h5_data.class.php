<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/12
 * Time: 14:28
 */
namespace Dao\Union;
use Dao;

/**
 * 微信H5页面数据
 * Class Act_h5_data
 * @package Dao\Union
 */
class Act_h5_data extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Act_h5_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
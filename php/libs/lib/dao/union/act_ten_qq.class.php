<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 11:18
 */
namespace Dao\Union;
class Act_ten_qq extends Union {

    protected static $_instance = null;

    /**
     * @return Act_ten_qq
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}
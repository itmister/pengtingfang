<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/19
 * Time: 16:06
 */
namespace Dao\Union;
class Free_soft extends Union {

    protected static $_instance = null;

    /**
     * @return Free_soft
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}
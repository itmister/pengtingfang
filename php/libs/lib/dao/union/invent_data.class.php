<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/11
 * Time: 9:45
 */

namespace Dao\Union;
use \Dao;
class Invent_data extends Union {

    protected static $_instance = null;

    /**
     * @return Invent_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}
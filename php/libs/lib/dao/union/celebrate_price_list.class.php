<?php
namespace Dao\Union;
use Dao;

/**
 *  周年庆活动
 * Class Celebrate_price_list
 * @package Dao\Union
 */
class Celebrate_price_list extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Celebrate_price_list
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}
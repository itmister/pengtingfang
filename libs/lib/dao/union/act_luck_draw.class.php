<?php
namespace Dao\Union;
use Dao;

/**
 *  微信抽奖
 * Class Act_luck_draw
 * @package Dao\Union
 */
class Act_luck_draw extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Act_luck_draw
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}
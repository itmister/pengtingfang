<?php
namespace Dao\Union;
use Dao;

/**
 * 转盘抽奖
 * Class Act_luck_dial
 * @package Dao\Union
 */
class Act_luck_dial extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Act_luck_dial
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    
    
    
    
    
    

}
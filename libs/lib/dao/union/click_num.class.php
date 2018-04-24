<?php
namespace Dao\Union;
use Dao;

/**
 * Class Click_num
 * @package Dao\Click_num
 */
class Click_num extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Click_num
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
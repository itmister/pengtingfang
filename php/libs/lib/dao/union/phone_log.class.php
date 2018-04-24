<?php
namespace Dao\Union;
use Dao;

/**
 * Class Phone_log
 * @package Dao\Phone_log
 */
class Phone_log extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Phone_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
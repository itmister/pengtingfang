<?php
namespace Dao\Union;
use \Dao;
class Log_soft_recover extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Log_soft_recover
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
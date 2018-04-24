<?php
namespace Dao\Union;
use Dao;

/**
 * 话费充值日志表
 */
class Huafei_cz_log extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Huafei_cz_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
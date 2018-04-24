<?php
namespace Dao\Union;
use \Dao;
class User_qqpcmgr_log extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\User_qqpcmgr_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

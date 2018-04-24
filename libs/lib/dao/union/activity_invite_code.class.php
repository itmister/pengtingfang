<?php
namespace Dao\Union;
use \Dao;
class Activity_invite_code extends Union {

    protected static $_instance = null;


    /**
     * @return Dao\Union\Activity_invite_code
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

<?php
namespace Mongo\Union;
use \Mongo;
class User_check extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\User
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }


}

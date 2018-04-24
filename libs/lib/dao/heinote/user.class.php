<?php
namespace Dao\Heinote;
use \Dao;
class User extends Heinote {

    protected static $_instance = null;

    /**
     * @return Dao\Heinote\User
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

<?php
namespace Dao\Clt_7654;
class System_log extends Clt_7654 {

    protected static $_instance = null;

    /**
     * @return User_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

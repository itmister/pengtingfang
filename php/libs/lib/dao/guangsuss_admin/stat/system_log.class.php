<?php
namespace Dao\Guangsuss_admin\Stat;
class System_log extends \Dao\Guangsuss_admin\Guangsuss_admin{

    protected static $_instance = null;

    /**
     * @return System_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

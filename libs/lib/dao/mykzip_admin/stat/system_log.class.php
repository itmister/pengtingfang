<?php
namespace Dao\Mykzip_admin\Stat;
class System_log extends \Dao\Mykzip_admin\Mykzip_admin{

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

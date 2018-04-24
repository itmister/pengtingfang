<?php
namespace Dao\Zj7654_log;
use \Dao;
class Install extends Zj7654_log {

    protected static $_instance = null;

    /**
     * @return Dao\Zj7654_log\install
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

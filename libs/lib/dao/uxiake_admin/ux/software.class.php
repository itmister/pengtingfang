<?php
namespace Dao\Uxiake_admin\Ux;
use \Dao\Uxiake_admin\Uxiake_admin;
class Software extends Uxiake_admin{
    protected static $_instance = null;

    /**
     * @return Software
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

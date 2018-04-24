<?php
namespace Dao\Uxiake_admin;
class Ad_admin_role extends Uxiake_admin{

    protected static $_instance = null;

    /**
     * @return Ad_admin_role
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

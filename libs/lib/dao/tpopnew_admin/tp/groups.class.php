<?php
namespace Dao\Tpopnew_admin\Tp;
use \Dao\Tpopnew_admin\Tpopnew_admin;

class Groups extends Tpopnew_admin{

    protected static $_instance = null;
    /**
     * @return Groups
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

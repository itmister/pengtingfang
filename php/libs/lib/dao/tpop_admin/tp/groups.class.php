<?php
namespace Dao\Tpop_admin\Tp;
use \Dao\Tpop_admin\Tpop_admin;

class Groups extends Tpop_admin{

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

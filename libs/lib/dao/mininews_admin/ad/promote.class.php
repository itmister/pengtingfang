<?php
namespace Dao\Mininews_admin\Ad;
use \Dao\Mininews_admin\Mininews_admin;

class Promote extends Mininews_admin{

    protected static $_instance = null;
    /**
     * @return Promote
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

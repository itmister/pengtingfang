<?php
namespace Dao\Mininews_admin\Mini;
use \Dao\Mininews_admin\Mininews_admin;

class Ad extends Mininews_admin{

    protected static $_instance = null;
    /**
     * @return Ad
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Rate_base extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Rate_base
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Kunbangzsw_ver_data extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Kunbangzsw_ver_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

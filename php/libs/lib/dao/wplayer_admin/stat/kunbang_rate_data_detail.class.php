<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Kunbang_rate_data_detail extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Kunbang_rate_data_detail
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

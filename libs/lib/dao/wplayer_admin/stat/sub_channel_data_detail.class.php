<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Sub_channel_data_detail extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Sub_channel_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

   
}

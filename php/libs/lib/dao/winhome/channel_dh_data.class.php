<?php
namespace Dao\Winhome;
use \Dao;
class Channel_dh_data extends Winhome {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Channel_dh_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

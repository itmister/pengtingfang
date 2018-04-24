<?php
namespace Dao\Wplayer_admin\Stat;
class Md5_data extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Md5_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

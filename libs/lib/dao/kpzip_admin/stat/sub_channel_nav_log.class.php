<?php
namespace Dao\Kpzip_admin\Stat;
class Sub_channel_nav_log extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Sub_channel_nav_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

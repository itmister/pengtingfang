<?php
namespace Dao\Kpzip_admin;
use \Dao;
class Kp_channel_email_config extends Kpzip_admin {
    protected static $_instance = null;

    /**
     * @return Kp_channel_email_config
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

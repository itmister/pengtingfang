<?php
namespace Dao\Union;
use \Dao;
class Sms_queue extends Union {
    protected static $_instance = null;
    /**
     * @return Dao\Union\Sms_queue
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

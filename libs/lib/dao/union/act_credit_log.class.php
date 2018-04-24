<?php
/**
 * 积分
 */
namespace Dao\Union;
use \Dao;
class Act_credit_log extends Union {

    protected static $_instance = null;

    /**
     * @return Act_credit_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}

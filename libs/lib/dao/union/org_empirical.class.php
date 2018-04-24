<?php
/**
 * 积分
 */
namespace Dao\Union;
use \Dao;
class Org_empirical extends Union {

    protected static $_instance = null;

    /**
     * @return Org_empirical
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

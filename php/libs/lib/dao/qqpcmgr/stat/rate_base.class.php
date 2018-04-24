<?php
namespace Dao\Qqpcmgr\Stat;
use \Dao;
class Rate_base extends \Dao\Qqpcmgr\Qqpcmgr {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Rate_base
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

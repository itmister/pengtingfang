<?php
namespace Dao\Qqpcmgr\Stat;
class System_log extends \Dao\Qqpcmgr\Qqpcmgr{

    protected static $_instance = null;

    /**
     * @return System_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

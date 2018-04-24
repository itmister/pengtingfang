<?php
namespace Dao\Qqpcmgr\Stat;

class Sub_channel_data_out extends \Dao\Qqpcmgr\Qqpcmgr {

    protected static $_instance = null;
    /**
     * @return Ver
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao;
class Active_uid_channel_qid_temp extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Kpzip_admin\Active_uid_channel_qid_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

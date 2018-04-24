<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao;
class Online_uid_qid_dh_temp extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Online_uid_qid_dh_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function sub_channel_dh_data($ymd){
        $time = time();
        $sql = "SELECT {$ymd} as ymd ,qid,count(*) as online,{$time} as dateline FROM `{$this->_realTableName}` GROUP BY qid;";
        return $this->query($sql);
    }
}

<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Online_uid_ver_temp extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Online_uid_ver_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_online_rate($ymd){
        $sql = "SELECT b.Ymd as ymd,a.ver as ver,count( case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=1 then a.ymd end) as online1,
count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=7 then a.ymd end) as online7,
count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=15 then a.ymd end) as online15,
count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=30 then a.ymd end) as online30
FROM `{$this->_realTableName}` as a LEFT JOIN stat_install_uid_ver_only as b on a.uid=b.uid and a.ver=b.ver where a.ymd={$ymd} and b.uid is not null GROUP BY a.ver,b.Ymd;";
        return $this->query($sql);
    }

    public function update_ver_ky($ymd){
        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN stat_jingpin_uid_ky_temp as b on a.uid=b.uid set a.`kuaiya_jingpin`=1 WHERE b.uid is not null;";
        return $this->query($up_sql);
    }

    public function get_all_kpzip_install($ymd){
        $sql = "SELECT ver,ymd,count(uid) AS kpzip_install FROM `{$this->_realTableName}` WHERE ymd = {$ymd} AND kuaiya_jingpin = 1 GROUP BY ver";
        return $this->query($sql);
    }
}

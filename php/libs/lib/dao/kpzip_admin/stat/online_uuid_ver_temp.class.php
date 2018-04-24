<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao;
class Online_uuid_ver_temp extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Online_uuid_ver_temp
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
FROM `{$this->_realTableName}` as a LEFT JOIN stat_install_uuid_ver_only as b on a.uid=b.uid and a.ver=b.ver where a.ymd={$ymd} and b.uid is not null GROUP BY a.ver,b.Ymd;";
        return $this->query($sql);
    }
}

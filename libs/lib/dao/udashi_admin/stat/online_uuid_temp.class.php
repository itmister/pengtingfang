<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Online_uuid_temp extends \Dao\Udashi_admin\Udashi_admin{

    protected static $_instance = null;
    /**
     * @return Online_uuid_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_online_rate($ymd){
        $sql = "SELECT b.Ymd as ymd,count( case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=1 then a.uid end) as online1,
        count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=7 then a.uid end) as online7,
        count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=15 then a.uid end) as online15,
        count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=30 then a.uid end) as online30
        FROM `{$this->_realTableName}` as a LEFT JOIN stat_install_uuid_only as b on a.uid=b.uid where a.ymd={$ymd} and b.uid is not null GROUP BY b.Ymd;";
        return $this->query($sql);
    }
}

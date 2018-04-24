<?php
namespace Dao\Winhome;
use \Dao;
class Online_uuid_temp extends Winhome {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Online_uuid_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_online_rate($ymd){
        $sql = "SELECT b.Ymd as ymd,count( case when TIMESTAMPDIFF(DAY,b.Ymd,a.Ymd)=1 then a.uid end) as online1,
        count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.Ymd)=7 then a.uid end) as online7,
        count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.Ymd)=15 then a.uid end) as online15,
        count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.Ymd)=30 then a.uid end) as online30
        FROM `{$this->_realTableName}` as a LEFT JOIN wh_install_uuid_only as b on a.uid=b.uid where a.Ymd={$ymd} and b.uid is not null GROUP BY b.Ymd;";
        return $this->query($sql);
    }
}

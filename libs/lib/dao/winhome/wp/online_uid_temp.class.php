<?php
namespace Dao\Winhome\Wp;
use \Dao;
class Online_uid_temp extends Wp {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Wp\Online_uid_temp
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
        FROM `{$this->_realTableName}` as a LEFT JOIN wp_install_uid_only as b on a.uid=b.uid where a.Ymd={$ymd} and b.uid is not null GROUP BY b.Ymd;";
        return $this->query($sql);
    }
}

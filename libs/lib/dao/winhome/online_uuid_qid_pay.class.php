<?php
namespace Dao\Winhome;
use \Dao;
class Online_uuid_qid_pay extends Winhome{

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Online_uuid_qid_pay
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

//    public function get_online_rate($ymd){
//        $sql = "SELECT b.Ymd as ymd,a.qid as qid,count( case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=1 then a.uid end) as online1,
//                count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=7 then a.uid end) as online7,
//                count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=15 then a.uid end) as online15,
//                count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=30 then a.uid end) as online30
//                FROM `{$this->_realTableName}` as a LEFT JOIN wh_install_uuid_qid_only as b on a.uid=b.uid and a.qid=b.QID
//                where a.ymd={$ymd} and b.uid is not null GROUP BY a.qid,b.Ymd;";
//        return $this->query($sql);
//    }
}

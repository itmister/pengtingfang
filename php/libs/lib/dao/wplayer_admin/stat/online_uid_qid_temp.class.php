<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Online_uid_qid_temp extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Online_uid_qid_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_online_rate($ymd){
        $sql = "SELECT b.Ymd as ymd,a.qid as qid,count( 
                case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=1 then a.ymd end) as online1,
                count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=7 then a.ymd end) as online7,
                count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=15 then a.ymd end) as online15,
                count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=30 then a.ymd end) as online30,
                count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.ymd)=30 then a.ymd end) as online30
            FROM `{$this->_realTableName}` as a LEFT JOIN stat_install_uid_qid_only as b on a.uid=b.uid and a.qid=b.QID where a.ymd={$ymd} and b.uid is not null GROUP BY a.qid,b.Ymd;";
        return $this->query($sql);
    }

    public function get_ver_list($ymd){
        $sql = "SELECT {$ymd} as ymd,qid,ver,count(*) as online FROM `{$this->_realTableName}`  where ymd={$ymd} GROUP BY qid,ver;";
        return $this->query($sql);
    }
    
    public function get_all_online_qid($limit,$ymd){
        if(!$limit) return false;
        $sql = "
            SELECT {$ymd} as ymd, b.uid,b.ip FROM
            (
                SELECT uid FROM `{$this->_realTableName}` ORDER BY uid ASC LIMIT {$limit}
            ) a
            LEFT JOIN `{$this->_realTableName}` b ON a.uid=b.uid
        ";
        return $this->query($sql);
    }
}
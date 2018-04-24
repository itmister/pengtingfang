<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao\Huayangnianhua_admin;
class Install_uuid_qid_pay_only extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    /**
     * @return Install_uuid_qid_pay_only
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_install_pay_ymd_count($ymd){
        $sql = "select count(*) as num from (
                    SELECT a.uid as uid,a.QID as QID,{$ymd} as Ymd_pay
                    FROM `{$this->_realTableName}` as a LEFT JOIN stat_online_uuid_qid_pay as b on a.uid=b.uid and a.QID=b.qid
                    WHERE a.Ymd_pay=0 AND b.ymd>=a.Ymd AND TIMESTAMPDIFF(DAY,a.Ymd,b.ymd)<=30 GROUP BY a.uid,a.QID HAVING count(*)>=2) as aa;";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    public function get_install_pay_ymd($ymd,$limit=''){
        $sql = "SELECT a.uid as uid,a.QID as QID,{$ymd} as Ymd_pay
                    FROM `{$this->_realTableName}` as a LEFT JOIN stat_online_uuid_qid_pay as b on a.uid=b.uid and a.QID=b.qid
                    WHERE a.Ymd_pay=0 AND b.ymd>=a.Ymd AND TIMESTAMPDIFF(DAY,a.Ymd,b.ymd)<=30 GROUP BY a.uid,a.QID HAVING count(*)>=2";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    public function get_ymd_install_num_count($ymd){
        $sql = "SELECT count(DISTINCT QID) as num FROM `{$this->_realTableName}` where Ymd_pay={$ymd};";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    public function get_ymd_install_num($ymd,$limit=''){
        $sql = "SELECT {$ymd} as ymd,QID as qid,substring_index(QID, '_', 1) as channel,substring_index(QID, '_', -1) as sub_channel,count(QID) as install,UNIX_TIMESTAMP() as dateline
                FROM `{$this->_realTableName}` where Ymd_pay={$ymd} GROUP BY QID";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    public function get_ymd_online_num_count($ymd){
        $sql = "select count(*) as num from (
        SELECT {$ymd} as ymd,a.QID as qid
        FROM `{$this->_realTableName}` as a left JOIN stat_online_uuid_qid_pay as b on a.uid=b.uid and a.QID=b.qid
        WHERE b.ymd={$ymd} and a.Ymd_pay>0 and a.Ymd_pay<{$ymd} GROUP BY a.QID) as aa;";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    public function get_ymd_online_num($ymd,$limit=''){
        $sql = "SELECT {$ymd} as ymd,a.QID as qid,substring_index(a.QID, '_', 1) as channel,
                substring_index(a.QID, '_', -1) as sub_channel,count(b.qid) as online,UNIX_TIMESTAMP() as dateline
                FROM `{$this->_realTableName}` as a left JOIN stat_online_uuid_qid_pay as b on a.uid=b.uid and a.QID=b.qid
                WHERE b.ymd={$ymd} and a.Ymd_pay>0 and a.Ymd_pay<{$ymd} GROUP BY a.QID";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
}

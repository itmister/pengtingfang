<?php
/**
 * 有效量记录表
 * @author huxiaowei1238
 * 
 */
namespace Dao\Online_7654;

class Soft_num extends Online_7654 {

    protected static $_instance = null;

    /**
     * @return Soft_num
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_soft_id_user_num($channel_id,$soft_id,$uid_str,$start,$end){
        if(!$channel_id||!$soft_id||!$uid_str||!$start||!$end) return false;
        $sql = "select uid,sum(num) as num from {$this->_realTableName}
        where channel_id={$channel_id} and ymd>={$start} and ymd<={$end} and soft_id='{$soft_id}' and uid in ({$uid_str}) group by uid";
        $ret = $this->query($sql);
        return $ret;
    }

    public function get_user_num_by_ymd($start,$end){
        $sql = "select uid,sum(num) as num,soft_id,org_id,soft_name from {$this->_realTableName}
        where ymd>={$start} and ymd<={$end} group by uid,soft_id,org_id";
        $ret = $this->query($sql);
        return $ret;
    }

    public function get_num_by_soft_id_ymd_uid($uid,$soft_id,$start_num,$end_num){
        $sql = "select sum(num) as num from {$this->_realTableName}
        where ymd>={$start_num} and ymd<={$end_num} and uid={$uid} and soft_id='{$soft_id}'";
        $ret = $this->query($sql);
        if($ret[0]['num']){
            return $ret[0]['num'];
        }
        return 0;
    }
}
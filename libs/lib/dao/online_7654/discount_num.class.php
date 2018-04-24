<?php
/**
 * 有效量扣除记录表
 * @author huxiaowei1238
 * 
 */
namespace Dao\Online_7654;

class Discount_num extends Online_7654 {

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

    public function get_soft_id_user_price($channel_id,$soft_id,$start,$end){
        if(!$channel_id||!$soft_id||!$start||!$end) return false;
        $sql = "select uid,discount,price from {$this->_realTableName}
        where channel_id={$channel_id} and start_time={$start} and end_time={$end} and soft_id='{$soft_id}'";
        $ret = $this->query($sql);
        return $ret;
    }

    public function get_soft_id_price($channel_id,$soft_id,$start,$end){
        if(!$channel_id||!$soft_id||!$start||!$end) return array();
        $sql = "select a.uid,b.name,a.discount,a.price,a.num from {$this->_realTableName} as a inner join user as b on a.uid=b.id
        where channel_id={$channel_id} and start_time={$start} and end_time={$end} and soft_id='{$soft_id}'";
        $ret = $this->query($sql);
        return $ret;
    }

    public function select_total($start,$end,$soft_id,$uid){
        if(!$uid||!$soft_id||!$start||!$end) return array();
        $sql = "select discount,price,start_time,end_time from {$this->_realTableName}
        where uid={$uid} and start_time>={$start} and end_time<={$end} and soft_id='{$soft_id}'";
        $ret = $this->query($sql);
        return $ret;
    }
}
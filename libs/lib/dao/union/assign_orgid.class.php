<?php
namespace Dao\Union;
use \Dao;

/**
 * @package Dao\Union
 */
class Assign_Orgid extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Assign_Orgid
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function user_tn_info($uid,$soft_id){
        $sql = "select * from {$this->_realTableName} where softID='{$soft_id}' and uid = {$uid} limit 1";
        $ret = $this->query($sql);
        return $ret[0] ? $ret[0]:[];
    }

    /**
     * 获取tn增加的记录
     * @param $soft_id
     * @param $ymd
     */
    public function tn_op_log($soft_ids,$ymd){
        $stime = strtotime($ymd);
        $soft_ids =  implode(',', array_map(function($str){return sprintf("'%s'", $str);}, $soft_ids ));
        $sql = "select * from {$this->_realTableName} where softID in ($soft_ids) and dateline >= {$stime}";
        $ret = $this->query($sql);
        return $ret;
    }

    public  function get_tn_info($soft_id,$tn){
        $sql = "select * from {$this->_realTableName} where softID='{$soft_id}' and org_id = '{$tn}' ORDER  BY  updateline desc limit 1";
        $ret = $this->query($sql);
        return isset($ret[0]) ? $ret[0]:[];
    }

    /**
     * 获取回收状态的tn
     * @param $soft_id
     * @return mixed
     */
    public function  get_recycle_codes($soft_ids){
        $soft_ids =  implode(',', array_map(function($str){return sprintf("'%s'", $str);}, $soft_ids ));
        $sql = "select org_id,softID from assign_orgid where softID in ({$soft_ids}) and status > 0 and
                  org_id not in (SELECT org_id from assign_orgid where status = 0 and softID in ({$soft_ids})) GROUP BY org_id";
        return $this->query($sql);
    }

    /**
     * 取开始推广时间戳，被回收或没推广返回0
     * @param $uid 用户uid
     * @param $soft_id 推广的软件short_name
     * @return integer
     */
    public function get_promote_time_begin( $uid, $soft_id) {
        $uid        = intval( $uid );
        $dateline   = intval( $this->get_one('dateline', "uid={$uid} AND softID='{$soft_id}' AND `status`=0"));
        return $dateline;
    }
}
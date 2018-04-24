<?php
/**
 * 有效量扣除记录表
 * @author huxiaowei1238
 * 
 */
namespace Dao\Online_7654;

class Channel_report_log extends Online_7654 {

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

    public function get_list($uid,$channel_id,$month,$page,$offset){
        $where = "channel_id=".$channel_id." and status=0 and month=".$month;
        if($uid>0){
            $where .= " and uid=".$uid;
        }
        $sql = "select * from {$this->_realTableName}  where {$where} order by soft_id desc limit {$offset},{$page}";
        return $this->query($sql);
    }

    public function get_one_uid($channel_id,$month){
        $where = "channel_id=".$channel_id." and status=0 and month=".$month;
        $sql = "select uid from {$this->_realTableName} where {$where} limit 1";
        return $this->query($sql);
    }
}
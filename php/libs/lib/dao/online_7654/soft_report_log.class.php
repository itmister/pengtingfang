<?php
/**
 * 有效量扣除记录表
 * @author huxiaowei1238
 * 
 */
namespace Dao\Online_7654;

class Soft_report_log extends Online_7654 {

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

    public function get_list($soft_id,$channel_id,$month,$page,$offset){
        $where = "soft_id='{$soft_id}' and status=0 and month=".$month;
        if($channel_id>0){
            $where .= " and channel_id=".$channel_id;
        }
        $sql = "select * from {$this->_realTableName}  where {$where} order by channel_id desc limit {$offset},{$page}";
        return $this->query($sql);
    }
}
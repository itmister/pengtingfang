<?php
/**
 * 有效量扣除记录表
 * @author huxiaowei1238
 * 
 */
namespace Dao\Online_7654;

class Report extends Online_7654 {

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

    public function get_list($month,$page,$offset){
        $where = 'status=0 and month='.$month;
        $sql = "select * from {$this->_realTableName}  where {$where} limit {$offset},{$page}";
        return $this->query($sql);
    }
}
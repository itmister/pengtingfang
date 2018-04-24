<?php
/**
 * 线下总收益
 */
namespace Dao\Revenue_7654;
use \Dao;
class Offline_Income extends Revenue_7654 {
    protected static $_instance = null;
    /**
     * @return Offline_Income
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_count_and_income($s_date,$e_date){
        $where  = sprintf("ymd >= %s and ymd <=%s",$s_date,$e_date);
        $sql = "select count(1) as num,sum(total_income) as total_income  from {$this->_realTableName} where {$where} ";
        $ret = $this->query($sql);
        return $ret[0] ? $ret[0]:[];
    }

    public function get_list($s_date,$e_date,$offset,$page_size){
        $where  = sprintf("ymd >= %s and ymd <= %s",$s_date,$e_date);
        $sql = "select * from {$this->_realTableName} where {$where} order BY ymd desc limit {$offset},{$page_size}";
        return $this->query($sql);
    }
}

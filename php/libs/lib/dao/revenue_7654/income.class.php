<?php
/**
 * 收益
 */
namespace Dao\Revenue_7654;
use \Dao;
class Income extends Revenue_7654 {
    protected static $_instance = null;
    /**
     * @return Income
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_info($ymd){
        $sql = "select * from {$this->_realTableName} where ymd = {$ymd}";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }

    /**
     * 拉取的总条数和总收入
     * @param $s_date
     * @param $e_date
     * @return array
     */
    public function get_count_and_income($s_date,$e_date){
        $where  = sprintf("ymd >= %s and ymd <=%s",$s_date,$e_date);
        $sql = "select count(1) as num,sum(total_income) as total_income from {$this->_realTableName} where {$where} ";
        $ret = $this->query($sql);
        return $ret[0] ? $ret[0]:[];
    }

    /**
     * 获取的列表
     * @param $s_date
     * @param $e_date
     * @param $offset
     * @param $page_size
     * @return mixed
     */
    public function get_list($s_date,$e_date,$offset,$page_size){
        $where  = sprintf("ymd >= %s and ymd <= %s",$s_date,$e_date);
        $sql = "select * from {$this->_realTableName} where {$where} order BY ymd desc limit {$offset},{$page_size}";
        return $this->query($sql);
    }

    /**
     * 更新
     * @param $ymd
     * @param $offline_incomde
     * @return int|mixed
     */
    public function add_offline_income($ymd,$offline_incomde){
        $sql = "INSERT INTO {$this->_realTableName} (ymd,total_income,offline_income) VALUES ({$ymd},{$offline_incomde},{$offline_incomde}) ON DUPLICATE KEY UPDATE offline_income = {$offline_incomde},
                  total_income = offline_income + online_income + other_income";
        return $this->exec($sql);
    }

    /**
     * 更新在线收入
     * @param $ymd
     * @param $online_income
     * @return int
     */
    public function update_online_income($ymd,$online_income){
        $sql = "update {$this->_realTableName} set online_income = {$online_income},total_income = offline_income + online_income + other_income where ymd={$ymd} limit 1";
        return $this->exec($sql);
    }

    /**
     * 更新其他收入
     * @param $ymd
     * @param $other_income
     * @param $other_desc
     * @return int
     */
    public function update_other_income($ymd,$other_income,$other_desc){
        $sql = "update {$this->_realTableName} set other_income = {$other_income},other_desc = '{$other_desc}', total_income = offline_income + online_income + other_income where ymd={$ymd} limit 1";
        return $this->exec($sql);
    }
}

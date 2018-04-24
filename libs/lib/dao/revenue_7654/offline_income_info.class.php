<?php
/**
 * 线下收益明显
 */
namespace Dao\Revenue_7654;
use \Dao;
class Offline_Income_Info extends Revenue_7654 {
    protected static $_instance = null;
    /**
     * @return Offline_Income_Info
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_list($s_date,$e_date){
        $where  = sprintf("ymd >= %s and ymd <= %s",$s_date,$e_date);
        $sql = "select * from {$this->_realTableName} where {$where} order BY ymd desc,soft_id desc";
        return $this->query($sql);
    }

    /**
     * 添加软件的数据
     * @param $ymd
     * @param $soft_id
     * @param $org_num
     * @param $price
     * @return bool|int|string
     */
    public function add_data($ymd,$soft_id,$org_num,$price){
        $sql = "INSERT INTO {$this->_realTableName} (ymd,soft_id,org_num,price) VALUES ({$ymd},'{$soft_id}',{$org_num},{$price}) ON DUPLICATE KEY UPDATE org_num = {$org_num},price = {$price}";
        return  $this->exec($sql);
    }

    /**
     * 计算一天内相应软件的总收益
     * @param $ymd
     * @param $soft_ids
     * @return mixed
     */
    public function get_sum($ymd,$soft_ids){
        $softs_str = $this->_field_to_str($soft_ids);
        $sql = "select sum(org_num * price) as num from {$this->_realTableName} where soft_id in($softs_str) and ymd = {$ymd}";
        $ret =  $this->query($sql);
        return $ret[0]['num'] ? $ret[0]['num'] : 0;
    }
}
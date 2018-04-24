<?php
namespace Dao\Log_7654;
use Dao\Dao;

class Biyibi extends Log_7654 {
   
    
    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_biyibi_count($ymd){
        $sql = "SELECT count(*) as num FROM `{$this->_realTableName}_{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    
    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_biyibi($ymd,$limit=''){
        $sql = "SELECT * FROM `{$this->_realTableName}_{$ymd}` ";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
    
    
}
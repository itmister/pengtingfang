<?php
namespace Dao\Huayangnianhua_log;
use \Dao;
class Jingpin extends Huayangnianhua_log {

    /**
     * @return Dao\Huayangnianhua_log\Jingpin
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    
    public function create_index($ymd){
        $sql = "show keys from `jingpin{$ymd}` WHERE key_name = 'is_install'";
        $index = $this->query($sql);
        if(!$index){
            $sql = "
                    ALTER TABLE `jingpin{$ymd}`
                    ADD INDEX `soft` USING BTREE (`software`, `is_install`, `UID`) ,
                    ADD INDEX `is_install` USING BTREE (`is_install`) ,
                    ADD INDEX `uid` USING BTREE (`UID`) ;
                ";
            $this->query($sql);

            
            //保存竟品唯一数据
            $this->_jingpin_temp_data($ymd);
        }
    }
    public function _jingpin_temp_data($ymd){
        //创建临时表
        $temp_table_name = "jingpin_temp{$ymd}";
        $sql = "
            CREATE TABLE `{$temp_table_name}` (
                `UID` varchar(32) NOT NULL,
                `software` varchar(32) NOT NULL,
                `version` varchar(32) NOT NULL,
                UNIQUE KEY `uid` (`UID`,`software`) USING BTREE
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ";
        $this->query($sql);
        
        $sql = "
            INSERT INTO `{$temp_table_name}` (UID,software,version)
                SELECT
                    UID,software,Version FROM `jingpin{$ymd}`
                WHERE is_install = 1
                GROUP BY
                    UID,software
        ";
        $this->query($sql);
    }
    
    public function drop_jingpin_temp($ymd){
        $temp_table_name = "jingpin_temp{$ymd}";
        $drop_sql = "DROP TABLE {$temp_table_name};";
        $this->query($drop_sql);
    }

    public function get_all_jingpin_security($ymd,$softId){
        $sql = "SELECT count(DISTINCT UID) as InstallCount FROM `jingpin_temp{$ymd}` WHERE LOWER(software) LIKE '%{$softId}%';";
        return $this->query($sql);
    }

    public function get_all_jingpin_competition($ymd,$softId){
        $sql = "SELECT count(DISTINCT UID) as InstallCount FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(software) LIKE '%{$softId}%';";
        return $this->query($sql);
    }
    
    public function get_jingpin_browser_num($ymd,$softId){
        $sql = "SELECT count(UID) as InstallCount FROM `jingpin_temp{$ymd}` WHERE LOWER(software) LIKE '%{$softId}%';";
        return $this->query($sql);
    }
    
    /**
     * 杀毒软件
     * @param unknown $ymd
     * @param unknown $softId
     * @return \Dao\mixed
     */
    public function get_jingpin_security_num($ymd){
        $sql = "SELECT count(DISTINCT UID) as security_num FROM `jingpin_temp{$ymd}` WHERE software IN ('360aqws','qqgj','jsdb','bdws','norton','rxsd')";
        $result = current($this->query($sql));
        return $result['security_num'] ? $result['security_num'] : 0;
    }
    
    public function get_jingpin_other_browser_num(){
        $browser_str = "'360aqllq','qqllq','2345llq','lbllq','360jsllq','sgllq','bdllq','ucllq','ggllq','hhllq','jzllq','ayllq','sjzcllq'";
        $sql = "SELECT count(DISTINCT UID) as other_browser FROM `jingpin_temp{$ymd}` WHERE software IN ({$browser_str})";
        $result = current($this->query($sql));
        return $result['other_browser'] ? $result['other_browser'] : 0;
    }
    
    public function get_all_jingpin_security_only($ymd,$softId){
        $sql = "
            SELECT count(1) only_num FROM (
                SELECT UID FROM `jingpin_temp{$ymd}` WHERE software = '{$softId}' AND
                UID NOT IN(SELECT UID FROM `jingpin_temp{$ymd}` WHERE software <> '{$softId}' GROUP BY UID)
                GROUP BY UID
            ) as a
            ";
        $result = current($this->query($sql));
        return $result['only_num'] ? $result['only_num'] : 0; 
    }
    
    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_num($ymd){
        $sql = "SELECT COUNT(DISTINCT UID) AS num FROM `{$this->_realTableName}{$ymd}`";
        $query_res = current($this->query($sql));
        return (int)$query_res['num'];
    }

    public function get_all_jingpin_soft_count($ymd,$soft){
        $sql = "SELECT count(DISTINCT UID) as num FROM `{$this->_realTableName}{$ymd}` WHERE software = '{$soft}'";
        $query_res = current($this->query($sql));
        return (int)$query_res['num'];
    }

    public function get_all_jingpin_soft($ymd,$soft,$limit=''){
        $sql = "SELECT DISTINCT UID AS uid,{$ymd} AS ymd FROM `{$this->_realTableName}{$ymd}` WHERE software = '{$soft}'";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    /**
      *是杀毒软件
     */
    public function get_all_jingpin_soft_count_is($ymd,$soft){
        $sql = "SELECT count(DISTINCT UID) as num FROM `{$this->_realTableName}{$ymd}` WHERE software = '{$soft}' and is_install = 1";
        $query_res = current($this->query($sql));
        return (int)$query_res['num'];
    }
    /**
     *是杀毒软件
     */
    public function get_all_jingpin_soft_is($ymd,$soft,$limit=''){
        $sql = "SELECT DISTINCT UID AS uid,{$ymd} AS ymd FROM `{$this->_realTableName}{$ymd}` WHERE software = '{$soft}' and is_install = 1";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
    
    public function get_rate_data($ymd,$jpname,$field){
        $sql = "SELECT UID AS uid FROM jingpin_temp{$ymd} WHERE software = '{$jpname}'";
        return $this->query($sql);
    }
}

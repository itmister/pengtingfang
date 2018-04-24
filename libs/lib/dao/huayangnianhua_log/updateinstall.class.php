<?php
namespace Dao\Huayangnianhua_log;
use \Dao;
class Updateinstall extends Huayangnianhua_log {

    /**
     * @return Dao\Huayangnianhua_log\Updateinstall
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    /**
     * 获取信息 渠道数据的升级信息记录数
     * @return array
     */
    public function get_all_updateinstall_ver_count($ymd){
        $time =time();
        $sql = "SELECT Version as ver,{$ymd} as Ymd,count(DISTINCT UID) as updateinstall ,{$time} as dateline FROM `{$this->_realTableName}{$ymd}` group by Version";
        return $this->query($sql);
    }


    /**
     * 获取信息 渠道数据的升级信息记录数
     * @return array
     */

    public function get_all_updateinstall_ver_uid_count($ymd){
        $time =time();
        $sql = "SELECT count(DISTINCT UID,Version) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    public function get_all_updateinstall_ver_uid($ymd,$limit=''){
        $time =time();
        $sql = "SELECT Version as ver,{$ymd} as Ymd,UID as uid FROM `{$this->_realTableName}{$ymd}` group by Version,UID";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_updateinstall_channel_qid_count($ymd){
        $sql = "SELECT count(DISTINCT UID,substring_index(trim(QID),'_',1)) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }


    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_updateinstall_channel_qid($ymd,$limit=''){
        $sql = "
            SELECT UID as uid,substring_index(trim(QID),'_',1) as qid,{$ymd} as ymd,Version as ver FROM
            (SELECT * FROM `{$this->_realTableName}{$ymd}` order by `TimeStamp` ASC)
            `{$this->_realTableName}{$ymd}` group by UID,substring_index(trim(QID),'_',1)
        ";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }


    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_updateinstall_qid_count($ymd){
        $sql = "SELECT count(DISTINCT UID,substring_index(trim(QID),'_',1)) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }


    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_updateinstall_qid($ymd,$limit=''){
        $sql = "
            SELECT UID as uid,QID as qid,{$ymd} as ymd,Version as ver FROM
            (SELECT * FROM `{$this->_realTableName}{$ymd}` order by `TimeStamp` ASC)
            `{$this->_realTableName}{$ymd}` group by UID,substring_index(trim(QID),'_',1)
        ";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
    
    /**
     * 当天升级量
     * @param integer $ymd
     * @return Ambigous <number, mixed>
     */
    public function get_updateinstall_ver_count($ymd){
        $sql = "SELECT count(DISTINCT UID,Version) as num FROM `{$this->_realTableName}{$ymd}`";
        $query_res = current($this->query($sql));
        return $query_res['num'] ? $query_res['num'] : 0;         
    }
    
    /**
     * 当天升级数据
     * @return \Dao\mixed
     */
    public function get_updateinstall_ver_data($ymd){
        $sql = "SELECT Version as ver,{$ymd} as ymd,UID AS uid FROM `{$this->_realTableName}{$ymd}` group by Version,UID";
        return $this->query($sql);
    }
    
    /**
     * 主渠升级数据
     * @param unknown $ymd
     * @return \Dao\mixed
     */
    public function get_updateinstall_qid_data($ymd){
        $sql = "SELECT {$ymd} AS ymd,substring_index(trim(QID),'_',1) as qid,count(distinct UID) as updateinstall FROM `{$this->_realTableName}{$ymd}` GROUP BY substring_index(trim(QID),'_',1)";
        return $this->query($sql);
    }
    /**
     * 子渠道升级数据
     * @param unknown $ymd
     * @return \Dao\mixed
     */
    public function get_updateinstall_subqid_data($ymd){
        $sql = "SELECT {$ymd} AS ymd,QID as qid,count(distinct UID) as updateinstall FROM `{$this->_realTableName}{$ymd}` GROUP BY qid";
        return $this->query($sql);
    }
    public function get_updateinstall_platform($ymd){
        $sql = "SELECT b.version,b.software,COUNT(distinct b.UID) as num FROM `{$this->_realTableName}{$ymd}` AS a LEFT JOIN `jingpin_temp{$ymd}` AS b ON a.UID=b.UID WHERE b.software IN('jsdb','qqgj','360sd') GROUP BY b.version,b.software ORDER BY version DESC";
        $data =  $this->query($sql);
        return $data;
    }

}

<?php
namespace Dao\Guangsuss_log;
use \Dao;
class Online extends Guangsuss_log {

    /**
     * @return Dao\Guangsuss_log\Online
     */
    public static function get_instance(){
        return parent::get_instance();
    }


    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_online_count($ymd){
    	$sql = " SELECT count(*) as `online` from (SELECT UID FROM `{$this->_realTableName}{$ymd}` group by UID) as aa";
    	return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_online_count($ymd){
        $sql = "SELECT count(DISTINCT UID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_online($ymd,$limit=''){
        $sql = "SELECT UID as uid,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}` group by UID";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_online_channel_qid_count($ymd){
        $sql = "SELECT count(DISTINCT UID,substring_index(trim(QID),'_',1)) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 渠道数据的启动信息
     * @return array
     */
    public function get_all_online_channel_qid($ymd,$limit=''){
        $sql = "SELECT UID as uid,substring_index(trim(QID),'_',1) as qid,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}` group by UID,substring_index(trim(QID),'_',1)";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    /**get_all_online_channel_qid_count
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_online_qid_count($ymd){
        $sql = "SELECT count(DISTINCT UID,QID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 渠道数据的启动信息
     * @return array
     */
    public function get_all_online_qid($ymd,$limit=''){
        $sql = "SELECT UID as uid,QID as qid,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}` group by UID,QID";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_online_ver_count($ymd){
        $sql = "SELECT count(DISTINCT UID,Version) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_online_ver($ymd,$limit=''){
        $sql = "SELECT UID as uid,Version as ver,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,Version";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
    /**
     * 获取信息 渠道数据的启动信息
     * @return array
     */
    public function get_all_online_qid_list($ymd){
        $sql = "SELECT substring_index(trim(QID),'_',1) as qid,{$ymd} as ymd,count(distinct UID) as online,UNIX_TIMESTAMP() as dateline
         FROM `{$this->_realTableName}{$ymd}` group by substring_index(trim(QID),'_',1)";
        return $this->query($sql);
    }
}

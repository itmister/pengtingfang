<?php
namespace Dao\Wplayer_log;
use \Dao;
class Install extends Wplayer_log {

    /**
     * @return Install
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_install_count($ymd){
        $sql = "SELECT count(DISTINCT UID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_all_install($ymd,$limit=''){
    	$sql = "SELECT UID as uid,{$ymd} as Ymd,repeat_install FROM `{$this->_realTableName}{$ymd}` group by UID";
        if($limit){
            $sql .= " limit {$limit}";
        }
    	return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_install_channel_qid_count($ymd){
        $sql = "SELECT count(DISTINCT UID,substring_index(trim(QID),'_',1)) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }


    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_channel_qid($ymd,$limit=''){
        $sql = "
            SELECT UID as uid,substring_index(trim(QID),'_',1) as QID,{$ymd} as Ymd,repeat_install,Version as ver,OS AS os FROM
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
    public function get_all_install_qid_count($ymd){
        $sql = "SELECT count(DISTINCT UID,substring_index(trim(QID),'_',1)) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }


    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_qid($ymd,$limit=''){
        $sql = "
            SELECT UID as uid,QID,{$ymd} as Ymd,repeat_install,Version as ver FROM
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
    public function get_all_install_ver_count($ymd){
        $sql = "SELECT count(DISTINCT UID,Version) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_install_ver($ymd,$limit=''){
        $sql = "
            SELECT UID as uid,Version as ver,{$ymd} as Ymd,repeat_install FROM 
            (SELECT * FROM `{$this->_realTableName}{$ymd}` ORDER BY TimeStamp ASC) `{$this->_realTableName}{$ymd}` group by UID,Version
        ";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
    public function get_original_install($ymd){
        $sql = "SELECT COUNT(1) AS original_install FROM `{$this->_realTableName}{$ymd}`";
        return current($this->query($sql));
    }
    
    public function get_original_install_by_main_qid($ymd){
        $sql = "SELECT {$ymd} AS ymd,substring_index(trim(QID),'_',1) as qid,COUNT(*) AS original_install FROM `{$this->_realTableName}{$ymd}` GROUP BY substring_index(trim(QID),'_',1)";
        return $this->query($sql);
    }
    
    public function get_original_install_by_qid($ymd){
        $sql = "SELECT {$ymd} AS ymd,QID as qid,COUNT(*) AS original_install FROM `{$this->_realTableName}{$ymd}` GROUP BY QID";
        return $this->query($sql);
    }
    
    public function md5_data_count($ymd){
        $sql = "SELECT COUNT(DISTINCT package_md5,UID) AS num FROM `install{$ymd}`";
        $query_res = current($this->query($sql));
        return $query_res['num'] ? $query_res['num'] : 0;
    }
    public function md5_data($ymd,$limit){
        $time = time();
        $sql = "SELECT {$ymd} AS ymd,UID AS uid,package_md5 AS md5,Version AS ver,{$time} AS dateline FROM `install{$ymd}` GROUP BY package_md5,UID LIMIT {$limit}";
        return $this->query($sql);
    }
    
}

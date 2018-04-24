<?php
namespace Dao\Huayangnianhua_log;
use \Dao;
class Uninstall extends Huayangnianhua_log {

    /**
     * @return Dao\Huayangnianhua_log\Uninstall
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_all_install($ymd){
    	$sql = "SELECT UID as uid,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID";
    	return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_channel_qid($ymd){
        $sql = "
            SELECT UID as uid,substring_index(trim(QID),'_',1) as QID,{$ymd} as Ymd 
            FROM (SELECT * FROM `{$this->_realTableName}{$ymd}` ORDER BY TimeStamp ASC) `{$this->_realTableName}{$ymd}`
            group by UID,substring_index(trim(QID),'_',1)
        ";
        return $this->query($sql);
    }


    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_qid($ymd){
        $sql = "
            SELECT UID as uid,QID,{$ymd} as Ymd
            FROM (SELECT * FROM `{$this->_realTableName}{$ymd}` ORDER BY TimeStamp ASC) `{$this->_realTableName}{$ymd}`
            GROUP BY UID, substring_index(trim(QID), '_', 1)
        ";
        return $this->query($sql);
    }

    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_install_ver($ymd){
        $sql = "
            SELECT UID as uid,Version as ver,{$ymd} as Ymd FROM 
            (SELECT * FROM `{$this->_realTableName}{$ymd}` ORDER BY TimeStamp ASC)
            `{$this->_realTableName}{$ymd}` group by UID,Version
        ";
        return $this->query($sql);
    }

    public function get_uninstall_by_hour($ymd,$hour){
        $start = strtotime(date("Y-m-d",strtotime($ymd))." ".$hour.":00:00");
        $end = strtotime(date("Y-m-d",strtotime($ymd))." ".$hour.":59:59");
        $sql = "SELECT $ymd as ymd, $hour as hour, count(*) as uninstall FROM `{$this->_realTableName}{$ymd}` where TimeStamp>='$start' and TimeStamp<='$end'";
        return $this->query($sql);
    }       



}

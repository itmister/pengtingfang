<?php
namespace Dao\Wh7654_log;
use \Dao;
class Active extends Wh7654_log {
    protected static $_instance = null;
    /**
     * @return Dao\Wh7654_log\Active
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }


    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_active_uuid_count($ymd){
        $sql = " SELECT count(*) as `active` from (SELECT UUID FROM `{$this->_realTableName}{$ymd}` group by UUID) as aa";
        return $this->query($sql);
    }


    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_active_count($ymd){
    	$sql = " SELECT count(*) as `active` from (SELECT UID FROM `{$this->_realTableName}{$ymd}` group by UID) as aa";
    	return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_active_channel_qid_uuid($ymd){
        $sql = "SELECT substring_index(QID, '_', 1) as qid,{$ymd} as ymd,count(DISTINCT UUID) as active
                FROM `{$this->_realTableName}{$ymd}` group by substring_index(QID, '_', 1)";
        return $this->query($sql);
    }


    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_active_channel_qid($ymd){
        $sql = "SELECT substring_index(QID, '_', 1) as qid,{$ymd} as ymd,count(DISTINCT UID) as active FROM `{$this->_realTableName}{$ymd}` group by substring_index(QID, '_', 1)";
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_active_qid_uuid($ymd){
        $sql = "SELECT QID as qid,{$ymd} as ymd,count(DISTINCT UUID) as active FROM `{$this->_realTableName}{$ymd}` group by QID";
        return $this->query($sql);
    }


    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_active_qid($ymd){
        $sql = "SELECT QID as qid,{$ymd} as ymd,count(DISTINCT UID) as active FROM `{$this->_realTableName}{$ymd}` group by QID";
        return $this->query($sql);
    }

    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_active_ver($ymd){
        $sql = "SELECT Version as ver,{$ymd} as Ymd,count(DISTINCT UID) as active FROM `{$this->_realTableName}{$ymd}` group by Version";
        return $this->query($sql);
    }

    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_active_ver_uuid($ymd){
        $sql = "SELECT Version as ver,{$ymd} as Ymd,count(DISTINCT UUID) as active FROM `{$this->_realTableName}{$ymd}` group by Version";
        return $this->query($sql);
    }
}

<?php
namespace Dao\Udashi_soft_uefi_log;
use \Dao;
class Manufacture extends Udashi_soft_uefi_log {
    protected static $_instance = null;
    /**
     * @return Dao\Udashi_soft_uefi_log\Manufacture
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
        $sql = " SELECT count(distinct DeviceId) as `active` from `{$this->_realTableName}{$ymd}` where result=1";
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
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_all_active($ymd,$limit=''){
        $sql = " SELECT DeviceId as `uid`,{$ymd} as Ymd from `{$this->_realTableName}{$ymd}` where result=1 group by DeviceId";
        if($limit){
            $sql .= " limit {$limit}";
        }
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
        $sql = "SELECT substring_index(QID, '_', 1) as QID,{$ymd} as Ymd,DeviceId as uid FROM `{$this->_realTableName}{$ymd}` where result=1 group by DeviceId";
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_active_qid_uuid($ymd){
        $sql = "SELECT QID,{$ymd} as Ymd,DeviceId as uid FROM `{$this->_realTableName}{$ymd}` where result=1 group by DeviceId";
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
        $sql = "SELECT  Version as ver,{$ymd} as Ymd,DeviceId as uid FROM `{$this->_realTableName}{$ymd}` where result=1 group by DeviceId";
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

    public function get_all_active_ver_fail($ymd){
        $sql = "SELECT  Version as ver,{$ymd} as ymd,count(distinct DeviceId) as active_fail FROM `{$this->_realTableName}{$ymd}` where result=0 group by Version";
        return $this->query($sql);
    }

    public function get_all_active_ver_fail_safe($ymd){
        $sql = "SELECT  Version as ver,{$ymd} as ymd,count(distinct case when LEFT(safe,1)=1 then DeviceId end) as active_fail_360,count(distinct case when right(LEFT(safe,2),1)=1 then DeviceId end) as active_fail_qg,count(distinct case when right(safe,1)=1 then DeviceId end) as active_fail_js FROM `{$this->_realTableName}{$ymd}` where result=0 group by Version";
        return $this->query($sql);
    }
}

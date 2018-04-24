<?php
namespace Dao\Udashi_soft_pro_log;
use \Dao;
class Uninstall extends Udashi_soft_pro_log {

    protected static $_instance = null;
    /**
     * @return Dao\Udashi_soft_pro_log\Uninstall
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
    public function get_all_install_uuid($ymd){
        $sql = "SELECT UUID as uid,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UUID";
        return $this->query($sql);
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
    public function get_all_install_channel_qid_uuid($ymd){
        $sql = "SELECT UUID as uid,substring_index(QID, '_', 1) as QID,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UUID,substring_index(QID, '_', 1)";
        return $this->query($sql);
    }


    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_channel_qid($ymd){
        $sql = "SELECT UID as uid,substring_index(QID, '_', 1) as QID,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,substring_index(QID, '_', 1)";
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_qid_uuid($ymd){
        $sql = "SELECT UUID as uid,QID,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UUID,QID";
        return $this->query($sql);
    }
    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_qid($ymd){
        $sql = "SELECT UID as uid,QID,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,QID";
        return $this->query($sql);
    }

    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_install_ver($ymd){
        $sql = "SELECT UID as uid,Version as ver,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,Version";
        return $this->query($sql);
    }

    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_install_ver_uuid($ymd){
        $sql = "SELECT UUID as uid,Version as ver,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UUID,Version";
        return $this->query($sql);
    }
}

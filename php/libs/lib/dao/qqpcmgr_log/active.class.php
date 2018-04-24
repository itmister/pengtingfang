<?php
namespace Dao\Qqpcmgr_log;
use \Dao;
class Active extends Qqpcmgr_log {
    /**
     * @return Dao\Qqpcmgr_log\Active
     */
    public static function get_instance(){
        return parent::get_instance();
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
    public function get_all_active_channel_qid($ymd){
        $sql = "SELECT substring_index(trim(QID),'_',1) as qid,{$ymd} as ymd,count(DISTINCT UID) as active FROM `{$this->_realTableName}{$ymd}` group by substring_index(trim(QID),'_',1)";
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
}

<?php
namespace Dao\Wallpaper_log;
use \Dao;
class Uninstall extends Wallpaper_log {

    protected static $_instance = null;
    /**
     * @return Dao\Wallpaper_log\Uninstall
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
    public function get_all_install($ymd){
    	$sql = "SELECT UID as uid,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID";
    	return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_channel_qid($ymd){
        $sql = "SELECT UID as uid,substring_index(trim(QID),'_',1) as QID,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,substring_index(trim(QID),'_',1)";
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
}

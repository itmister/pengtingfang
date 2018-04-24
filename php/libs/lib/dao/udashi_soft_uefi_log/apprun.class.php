<?php
namespace Dao\Udashi_soft_uefi_log;
use \Dao;
class Apprun extends Udashi_soft_uefi_log {

    protected static $_instance = null;
    /**
     * @return Dao\Udashi_soft_uefi_log\Apprun
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
    public function get_online_count($ymd){
    	$sql = " SELECT count(*) as `online` from (SELECT UID FROM `{$this->_realTableName}{$ymd}` group by UID) as aa";
    	return $this->query($sql);
    }

    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_online_uuid_count($ymd){
        $sql = " SELECT count(*) as `online` from (SELECT UID FROM `{$this->_realTableName}{$ymd}` group by UID) as aa";
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_online_uuid_count($ymd){
        $sql = "SELECT count(DISTINCT UID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_online_uuid($ymd,$limit=''){
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
    public function get_all_online_channel_qid_uuid_count($ymd){
        $sql = "SELECT count(DISTINCT substring_index(QID, '_', 1)) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 渠道数据的启动信息
     * @return array
     */
    public function get_all_online_channel_qid_uuid($ymd,$limit=''){
        $sql = "SELECT count(DISTINCT UID) as online,substring_index(QID, '_', 1) as qid,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}` group by substring_index(QID, '_', 1)";
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
        $sql = "SELECT count(DISTINCT UID,substring_index(QID, '_', 1)) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    public function get_all_online_channel_qid_count_new($ymd){
        $sql = "SELECT count(DISTINCT UID,substring_index(QID, '_', 1)) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 渠道数据的启动信息
     * @return array
     */
    public function get_all_online_channel_qid($ymd,$limit=''){
        $sql = "SELECT UID as uid,substring_index(QID, '_', 1) as qid,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}` group by UID,substring_index(QID, '_', 1)";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    public function get_all_online_channel_qid_new($ymd,$limit=''){
        $sql = "SELECT UID as uid,substring_index(QID, '_', 1) as qid,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}` group by UID,substring_index(QID, '_', 1)";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_online_qid_uuid_count($ymd){
        $sql = "SELECT count(DISTINCT QID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 渠道数据的启动信息
     * @return array
     */
    public function get_all_online_qid_uuid($ymd,$limit=''){
        $sql = "SELECT count(DISTINCT UID) as online,QID as qid,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}` group by QID";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }


    /**
     * 获取信息 付费渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_online_qid_uuid_pay_count($ymd,$qid){
        $sql = "SELECT count(DISTINCT UUID,QID) as num FROM `{$this->_realTableName}{$ymd}` where QID like '{$qid}%'";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 付费渠道数据的启动信息
     * @return array
     */
    public function get_all_online_qid_uuid_pay($ymd,$qid,$limit=''){
        $sql = "SELECT UUID as uid,QID as qid,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}` where QID like '{$qid}%' group by UUID,QID";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }


    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_online_qid_count($ymd){
        $sql = "SELECT count(DISTINCT UID,QID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    public function get_all_online_qid_count_new($ymd){
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

    public function get_all_online_qid_new($ymd,$limit=''){
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
        $sql = "SELECT count(DISTINCT UID) as online,Version as ver,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by Version";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    public function get_all_online_security($ymd,$softId){
        $sql = "SELECT count(DISTINCT UID) as InstallCount FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(SecuritySoftware) LIKE '%{$softId}%';";
        return $this->query($sql);
    }

    public function get_all_online_competition($ymd,$softId){
        $sql = "SELECT count(DISTINCT UID) as InstallCount FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(CompetitionSoftware) LIKE '%{$softId}%';";
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

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_online_ver_uuid_count($ymd){
        $sql = "SELECT count(DISTINCT UUID,Version) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    public function get_all_online_ver_uuid_count_new($ymd){
        $sql = "SELECT count(DISTINCT UID,Version) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_online_ver_uuid($ymd,$limit=''){
        $sql = "SELECT UUID as uid,Version as ver,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UUID,Version";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
    public function get_all_online_ver_uuid_new($ymd,$limit=''){
        $sql = "SELECT UID as uid,Version as ver,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}` group by UID,Version";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
}

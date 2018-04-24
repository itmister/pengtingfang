<?php
namespace Dao\Udashi_soft_uefi_log;
use \Dao;
class Install extends Udashi_soft_uefi_log {

    /**
     * @return Dao\Udashi_soft_uefi_log\Install
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_install_uuid_count($ymd){
        $sql = "SELECT count(DISTINCT UID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_all_install_uuid($ymd,$limit=''){
        $sql = "SELECT UID as uid,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
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
    	$sql = "SELECT UID as uid,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID";
        if($limit){
            $sql .= " limit {$limit}";
        }
    	return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_install_channel_qid_uuid_count($ymd){
        $sql = "SELECT count(DISTINCT UID,substring_index(QID, '_', 1)) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_channel_qid_uuid($ymd,$limit=''){
        $sql = "SELECT UID as uid,substring_index(QID, '_', 1) as QID,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,substring_index(QID, '_', 1)";
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
        $sql = "SELECT count(DISTINCT UID,substring_index(QID, '_', 1)) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_channel_qid($ymd,$limit=''){
        $sql = "SELECT UID as uid,substring_index(QID, '_', 1) as QID,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,substring_index(QID, '_', 1)";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }


    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_install_qid_uuid_count($ymd){
        $sql = "SELECT count(DISTINCT UID,QID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }


    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_qid_uuid($ymd,$limit=''){
        $sql = "SELECT UID as uid,QID,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,QID";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    /*付费渠道统计*/
    public function get_all_install_qid_uuid_pay_count($ymd,$qid){
        $sql = "SELECT count(DISTINCT UUID,QID) as num FROM `{$this->_realTableName}{$ymd}` where QID like '{$qid}%'";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 付费渠道数据的安装信息
     * @return array
     */
    public function get_all_install_qid_uuid_pay($ymd,$qid,$limit=''){
        $sql = "SELECT UUID as uid,QID,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` where QID like '{$qid}%' group by UUID,QID";
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
        $sql = "SELECT count(DISTINCT UID,QID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }


    /**
     * 获取信息 渠道数据的安装信息
     * @return array
     */
    public function get_all_install_qid($ymd,$limit=''){
        $sql = "SELECT UID as uid,QID,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,QID";
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
        $sql = "SELECT UID as uid,Version as ver,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,Version";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_install_ver_uuid_count($ymd){
        $sql = "SELECT count(DISTINCT UID,Version) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_install_ver_uuid($ymd,$limit=''){
        $sql = "SELECT UID as uid,Version as ver,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}` group by UID,Version";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

    public function get_original_install($ymd){
        $sql = "SELECT COUNT(*) AS original_install FROM `{$this->_realTableName}{$ymd}`";
        return current($this->query($sql));
    }

    public function get_original_install_by_qid($ymd){
        $sql = "SELECT {$ymd} AS ymd,substring_index(trim(QID),'_',1) as qid,COUNT(*) AS original_install FROM `{$this->_realTableName}{$ymd}` GROUP BY substring_index(trim(QID),'_',1)";
        return $this->query($sql);
    }

    public function get_original_install_by_qid_sub($ymd){
        $sql = "SELECT {$ymd} AS ymd,QID as qid,COUNT(*) AS original_install FROM `{$this->_realTableName}{$ymd}` GROUP BY QID";
        return $this->query($sql);
    }

    public function get_original_install_by_ver($ymd){
        $sql = "SELECT {$ymd} AS ymd,Version as ver,COUNT(*) AS original_install FROM `{$this->_realTableName}{$ymd}` GROUP BY Version";
        return $this->query($sql);
    }
}

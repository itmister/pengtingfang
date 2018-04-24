<?php
namespace Dao\Wplayer_log;
use \Dao;
class Online extends Wplayer_log {

    /**
     * @return Online
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    
    public function create_index($ymd){
        $sql = "show keys from `online{$ymd}` WHERE key_name = 'qid'";
        $index = $this->query($sql);
        if(!$index){
            $sql = "
                ALTER TABLE `online{$ymd}`
                ADD INDEX `qid` USING BTREE (`UID`, `QID`,`id`) ,
                ADD INDEX `ver` USING BTREE (`UID`, `Version`,`id`);
            ";
            $this->query($sql);
        }
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
        $sql = "SELECT UID as uid,substring_index(trim(QID),'_',1) as qid,{$ymd} as ymd,Version as ver,OS AS os FROM `{$this->_realTableName}{$ymd}` group by UID,substring_index(trim(QID),'_',1)";
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
        if(!$limit) return false;
        $sql = "
                SELECT b.UID AS uid,b.QID AS qid,{$ymd} AS ymd,b.Version AS ver,b.IP AS ip FROM 
                (
                    SELECT id FROM `{$this->_realTableName}{$ymd}` GROUP BY UID,QID ORDER BY id ASC LIMIT {$limit}
                ) a
                LEFT JOIN `{$this->_realTableName}{$ymd}` b ON a.id=b.id
         ";
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
        if(!$limit) return false;
        $sql = "
            SELECT b.UID AS uid,b.Version AS ver,{$ymd} as Ymd FROM
            (
                SELECT id FROM `{$this->_realTableName}{$ymd}` GROUP BY UID,Version ORDER BY id ASC LIMIT {$limit}
            ) a
            LEFT JOIN `{$this->_realTableName}{$ymd}` b ON a.id=b.id
        ";
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
    public function get_start_data($ymd){
        $time = time();
        $sql = "SELECT {$ymd} AS ymd,package_md5 AS md5,COUNT(distinct UID) AS start_num,Version AS ver,{$time} AS dateline FROM `{$this->_realTableName}{$ymd}` GROUP BY package_md5";
        return $this->query($sql);
    }
}

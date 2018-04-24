<?php
namespace Dao\Udashi_soft_pro_log;
use \Dao;
class Avaliable extends Udashi_soft_pro_log {

    protected static $_instance = null;
    /**
     * @return Dao\Udashi_soft_pro_log\Avaliable
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
    public function get_count($ymd){
        $sql = " SELECT count(DISTINCT UID) as `num` FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
       
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 版本还原量
     * @return array
     */
    public function get_count_distinct_uid_version($ymd){
        $sql = " SELECT count(DISTINCT uid) `huanyuanliang` , {$ymd} ymd , version ver FROM `{$this->_realTableName}{$ymd}`  where version<>'' GROUP by version";
        return $this->query($sql);
    }

    /**
     * 获取信息 快压的记录数
     * @return array
     */
    public function get_all_kuaiya_ver_count($ymd){
        $sql = "SELECT count(DISTINCT UID,Version) as num FROM `{$this->_realTableName}{$ymd}` where kuaizip = 1";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    /**
     * 获取信息 还原的记录数
     * @return array
     */
    public function get_all_huanyuan_ver_count($ymd){
        $sql = "SELECT count(DISTINCT UID,Version) as num FROM `{$this->_realTableName}{$ymd}`  where version<>'' ";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 获取信息 快压的 数据
     * @return array
     */
    public function get_all_kuaiya_ver($ymd,$limit=''){
        $sql = "SELECT UID as uid,Version as ver,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}`  where kuaizip = 1 group by UID,Version";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
    /**
     * 获取信息 还原的 数据
     * @return array
     */
    public function get_all_huanyuan_ver($ymd,$limit=''){
        $sql = "SELECT UID as uid,Version as ver,{$ymd} as Ymd FROM `{$this->_realTableName}{$ymd}`  where version<>'' group by UID,Version";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
    
    /**
     * 获取信息 产品数据的安装信息,去除guid
     * @return array
     */
    public function get_count_distinct_uid($ymd){
        $sql = " SELECT count(DISTINCT UID) as `num` FROM `{$this->_realTableName}{$ymd}` where guid<>''";
        $data = $this->query($sql);
         
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    
    /**
     * 获取信息 产品数据的安装信息  新
     * @return array
     */
    public function get_count_distinct_uid_n($ymd){
        $sql =" select count(a.uid) num from ( select DISTINCT uid from `{$this->_realTableName}{$ymd}` where guid<>'' ) a where not EXISTS(SELECT uid FROM bank_uid where UID=a.uid); ";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    /**
     * 机器留存率
     * @param unknown $ymd
     * @param unknown $day
     */
    public function get_count_uid_cll($ymd,$uymd){
        $sql = "select count(a.uid) num from ( SELECT DISTINCT uid from `{$this->_realTableName}{$ymd}` where guid<>'' ) a where  EXISTS( select uid from bank_uid where  ymd={$uymd} and  UID=a.uid  )";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    
    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_haiyuan_uid($ymd,$limit=''){
        $sql = "SELECT UID as uid,substring_index(QID, '_', 1) as qid,QID as sub_qid,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}` group by UID";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }


    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_count_guid($ymd){
        $sql = " SELECT count(DISTINCT guid) as `num` FROM `{$this->_realTableName}{$ymd}` where guid<>''";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    
    /**
     * 新增U盘库
     * @return array
     */
    public function insert_bank_guid($ymd){
        $sql = "INSERT INTO bank_guid  SELECT {$ymd} ymd ,guid from  (select {$ymd} ymd ,guid FROM {$this->_realTableName}{$ymd} where guid<>'' GROUP BY guid) a where not EXISTS (SELECT guid FROM bank_guid where guid=a.guid);";
        $this->query($sql);
    }
    
    /**
     * 新增电脑库
     * @return array
     */
    public function insert_bank_uid($ymd){
        $sql = "INSERT INTO bank_uid  SELECT {$ymd} ymd ,uid from  (select {$ymd} ymd ,uid FROM {$this->_realTableName}{$ymd} where uid<>'' GROUP BY uid) a where not EXISTS (SELECT uid FROM bank_uid where uid=a.uid);";
        $this->query($sql);
    }

    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_count_guid_n($ymd){
        $sql =" select count(a.guid) num from ( select DISTINCT guid from `{$this->_realTableName}{$ymd}` where guid<>'' ) a where not EXISTS(SELECT guid FROM bank_guid where guid=a.guid);";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    
    
    /**
     * 获取信息 版本数据的安装信息
     * @return array
     */
    public function get_all_haiyuan_uid_guid($ymd,$limit=''){
        $sql = "SELECT guid,{$ymd} as ymd,count(DISTINCT UID) as num,UNIX_TIMESTAMP() as dateline
 from `{$this->_realTableName}{$ymd}` where guid<>'' GROUP BY guid";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
    
    
    /**
     * 所有数据
     * @return array
     */
    public function get_count_all($ymd){
        $sql = " SELECT count(*) as `num` FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    
}

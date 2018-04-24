<?php
namespace Dao\Mykzip_log;
use \Dao;
class Data_category extends Mykzip_log {

    protected static $_instance = null;
    /**
     * @return Data_category
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }


    /**
     * 获取版本号数据
     * @param string $tablename
     * @param string $ymd
     * @param string $version
     * @return boolean|\Dao\mixed
     */
    public function get_ver_data($tablename,$ymd,$version){
        if(!$tablename || !$ymd || !$version){
            return false;
        }
        $tablename = $this->_prefix.$tablename.$ymd;
        $sql ="SHOW TABLES LIKE '%{$tablename}%'";
        if(!$this->query($sql)){
            return false;
        }
        
    	$sql  = "SELECT COUNT(*) AS total_num,FROM_UNIXTIME(`TimeStamp`,'%H') AS hour,Version FROM `{$tablename}`";
    	$sql .= " WHERE Version = '{$version}'";
    	$sql .= " GROUP BY hour";
    	return $this->query($sql);
    }
    
    /**
     * 获取渠道数据
     * @param string $tablename
     * @param string $ymd
     * @param string $qid
     * @return boolean|\Dao\mixed
     */
    public function get_qid_data($tablename,$ymd,$qid){
        if(!$tablename || !$ymd || !$qid){
            return false;
        }
        $tablename = $this->_prefix.$tablename.$ymd;
        $sql ="SHOW TABLES LIKE '%{$tablename}%'";
        if(!$this->query($sql)){
            return false;
        }
        
        $sql  = "SELECT COUNT(*) AS total_num,FROM_UNIXTIME(`TimeStamp`,'%H') AS hour,QID FROM `{$tablename}`";
        $sql .= " WHERE QID = '{$qid}'";
        $sql .= " GROUP BY hour";
        return $this->query($sql);
    }
}

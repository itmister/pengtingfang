<?php
namespace Dao\Union;
use \Dao;
class Store_validate_record extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Store_validate_record
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;

    }

	/**
     * 获取信息
     * @return array
     */
	public function select($where=true,$field="*"){
        $sql = "SELECT {$field} FROM `{$this->_realTableName}` WHERE {$where}";
        return $this->query($sql);
    }
    
    /**
     * 获取总数
     * @param string $where
     * @return array
     */
    public function count($where){
    	$sql = "SELECT count(*) as count FROM `{$this->_realTableName}` WHERE {$where}";
    	$result = $this->query($sql);
    	return $result[0]['count'];
    }

    public function get_status(){
        $sql = "SELECT a.* FROM (SELECT uid,status FROM `{$this->_realTableName}` ORDER BY id DESC) as a GROUP BY a.uid";
        $result = $this->query($sql);
        foreach($result as $v){
            $data[$v['uid']] = $v['status'];
        }
        return $data;
    }


    /** 判断用户认证状态
     * @param $uid
     * @return int
     */
    public function get_status_by_uid($uid){
        if($uid<=0) return 0;
        $sql = "SELECT status FROM `{$this->_realTableName}` where uid={$uid} ORDER BY id DESC limit 1";
        $result = $this->query($sql);
        if($result&&$result[0]['status']==2){
            return 1;
        }
        return 0;
    }
}

<?php
namespace Dao\Online_7654;
use \Dao;
class User extends Online_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Online_7654\User
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_user_channel_by_uid($uid){
        $sql = "select a.name,b.id,b.username from {$this->_realTableName} as a inner join channel as b on a.channel_id=b.id
        where a.id={$uid}";
        $ret = $this->query($sql);
        return $ret[0];
    }

    /**
     * 获取信息
     * @return array
     */
    public function get_all($where){
    	$sql = "SELECT * FROM `{$this->_realTableName}` WHERE {$where}";
    	return $this->query($sql);
    }
    
    /**
     * 获取总数
     * @param string $where
     * @return array
     */
    public function get_count($where){
    	$sql = "SELECT count(*) as count FROM `{$this->_realTableName}` WHERE {$where}";
    	$result = $this->query($sql);
    	return $result[0]['count'];
    }
    
    
	/**
     * 取用户列表关联渠道
     * @return array
     */
    public function get_list($where){
    	$sql = "SELECT a.*,b.contact,b.username,b.phone,b.email FROM `{$this->_realTableName}` a left join online_7654.channel b on a.channel_id = b.id WHERE {$where}";
    	return $this->query($sql);
    }
    
    /**
     * 取用户列表
     * @return array
     */
    public function get_user_list($where){
    	$sql = "SELECT a.*,b.reg_dateline FROM `{$this->_realTableName}` a left join union.user b on a.id = b.id WHERE {$where}";
    	return $this->query($sql);
    }
    
}

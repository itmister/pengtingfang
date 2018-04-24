<?php
namespace Dao\Online_7654;
use \Dao;
class Channel extends Online_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Online_7654\Channel
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
    public function get_all($where=true,$field='*'){
    	$sql = "SELECT {$field} FROM `{$this->_realTableName}` WHERE {$where}";
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
    
}

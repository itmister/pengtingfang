<?php
namespace Dao\Union;
use \Dao;
class User_ext extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\User
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }


    /**
     * 设置用户礼花
     * @param $uid
     * @param $lihua = 0
     */
    public function set_lihua($uid) {
        $uid = intval($uid);
        $this->update("uid={$uid}", array('lihua' => 0));
    }
    
	/**
     * 获取信息
     * @return array
     */
	public function select($where=true,$field="*"){
        $sql = "SELECT {$field} FROM `{$this->_realTableName}` WHERE {$where}";
        return $this->query($sql);
    }
}

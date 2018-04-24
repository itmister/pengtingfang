<?php
namespace Dao\Union;
use \Dao;
class User_vip extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\User_vip
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }


    /**
     * 去用户vip信息
     * @param $uid
     * @param $lihua = 0
     */
    public function get_user_vip($uid) {
        $uid = intval($uid);
        $sql = "select * from {$this->_realTableName} where userid={$uid} and isdel=0 limit 1";
        return $this->query($sql);
    }

    public function get_all(){
        $sql = "select userid from {$this->_realTableName}  where isdel=0";
        $res = $this->query($sql);
        foreach($res as $v){
            $data[] = $v['userid'];
        }
        return $data;
    }
}

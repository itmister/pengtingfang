<?php
namespace Dao\Union;
use \Dao;

/**
 * 双节活动软件的
 * @package Dao\Union
 */
class Act_Badge_Soft extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Act_Mod_Package
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_user_hist($uid,$type){
        $sql = "select ymd from {$this->_realTableName} where `uid`={$uid} and `type` ={$type}";
        $ret = $this->query($sql);
        return $ret;
    }

    public function add_user_hist($uid,$ymd,$type){
        $data = [
            'uid'=>$uid,
            'ymd'=>$ymd,
            'type'=>$type
        ];
        return $this->add($data);
    }
}

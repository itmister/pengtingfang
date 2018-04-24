<?php
namespace Dao\Union;
use \Dao;
class Act_Coupon extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Credit_Stat
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function get_coupon_uid($uid){
        $sql = "select * from {$this->_realTableName} where uid ={$uid} limit 1";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }
    
    public function get_coupon(){
        $sql = "select * from {$this->_realTableName} where uid=0 limit 1";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }

    public function update_info($id,$data){
        return $this->update('id='.$id,$data);
    }
}

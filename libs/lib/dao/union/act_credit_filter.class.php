<?php
/**
 * 积分
 */
namespace Dao\Union;
use \Dao;
class Act_Credit_Filter extends Union {

    protected static $_instance = null;

    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_info($apply_id) {
        $sql = "select count(1) as num from {$this->_realTableName} where apply_id = {$apply_id}";
        $ret = $this->query($sql);
        return $ret[0]['num'];
    }

    public function add_data($data){
        return $this->add_all($data);
    }

    public function get_data($apply_id){
        $sql = "select uid from {$this->_realTableName} where apply_id = {$apply_id}";
        $ret = $this->query($sql);
        return $ret;
    }
}

<?php
namespace Dao\Union;
use \Dao;

/**
 * 活动礼包配置表
 * @package Dao\Union
 */
class Act_Mod_Package extends Union {
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

    /**
     * 获取一个规则下所有礼包
     * @param $rid
     * @param int $status
     * @return mixed
     */
    public function getRulePackage($rid,$status = 1){
        $sql = "select * from {$this->_realTableName} where rid={$rid} and status ={$status}";
        $ret = $this->query($sql);
        return $ret;
    }

    public function getPackage($pid){
        $sql = "select * from {$this->_realTableName} where pid={$pid}";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }
}

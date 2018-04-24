<?php
namespace Dao\Union;
use Dao;

/**
 *  活动中用户获得的虚拟有价货币
 * Class Act_Mod_Coupon
 * @package Dao\Union
 */
class Act_Mod_Coupon extends Union {
    protected static $_instance = null;
    /**
     * @return Dao\Union\Act_Mod_Coupon
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 获得经验值
     * @param $rid
     * @param $uid
     * @param string $num
     * @return bool|int|string
     */
    public function add_coupon($uid,$rid,$num){
        $sql = "insert into {$this->_realTableName}(uid,rid,total,remain) VALUES ({$uid},{$rid},{$num},{$num}) ON DUPLICATE KEY UPDATE total=total+{$num},remain=remain+{$num}";
        return  $this->exec($sql);
    }

    /**
     * 消费经验值
     * @param $uid
     * @param $rid
     * @param $num
     * @return bool|int|string
     */
    public function consume($uid,$rid,$num){
        $sql = "update {$this->_realTableName} set remain = remain - {$num} WHERE  uid = {$uid} and rid = {$rid} and remain >= $num";
        return  $this->exec($sql);
    }

    /**
     * @param $uid
     * @param $rid
     * @return array
     */
    public function info($uid,$rid){
        $sql = "select * from {$this->_realTableName} WHERE  uid = {$uid} and rid = {$rid} ";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0] : [];
    }
}
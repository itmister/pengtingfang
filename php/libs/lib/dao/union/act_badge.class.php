<?php
namespace Dao\Union;
use Dao;

/**
 *  双节点亮徽章活动
 * Class Act_Badge
 * @package Dao\Act_Badge
 */
class Act_Badge extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Act_Badge
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * @param $uid
     * @return array
     */
    public function info($uid){
        $sql = "select * from {$this->_realTableName} WHERE uid = {$uid}";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0] : [];
    }

    /**
     * @param $uid
     * @param $key
     * @param $val
     * @return bool|int|string
     */
    public function change_status($uid,$key,$val){
        $sql = "update {$this->_realTableName} set {$key}={$val} where uid ={$uid}";
        return  $this->exec($sql);
    }

    /**
     *
     */
    public function add_badge($uid){
       return  $this->add(['uid'=>$uid]);
    }
}
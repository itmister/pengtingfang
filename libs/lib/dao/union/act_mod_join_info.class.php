<?php
namespace Dao\Union;
use Dao;

/**
 *  活动模型奖励发放记录
 * Class Act_Mod_Join_Info
 * @package Dao\Union
 */
class Act_Mod_Join_Info extends Union {
    protected static $_instance = null;
    /**
     * @return Dao\Union\Act_Mod_Join_Info
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function add_join_info($data){
        return $this->add($data);
    }

    public function get_join_info($uid,$rid){
        $sql = "select * from {$this->_realTableName} where uid ={$uid} and rid={$rid} limit 1";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }

    public function update_join_info($uid,$rid,$data){
       return  $this->update("uid = {$uid} AND rid ={$rid}",$data);
    }
}
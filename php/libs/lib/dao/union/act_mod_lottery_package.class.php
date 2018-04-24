<?php
namespace Dao\Union;
use Dao;

/**
 *  抽奖活动奖池
 * Class Act_Mod_Lottery_Package
 * @package Dao\Union
 */
class Act_Mod_Lottery_Package extends Union {
    protected static $_instance = null;
    /**
     * @return Dao\Union\Act_Mod_Lottery_Package
     */
    public static function get_instance(){
            if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function getLotteryPackage($rid,$status = 1){
        $sql = "select * from {$this->_realTableName} where rid={$rid} and status ={$status}";
        $ret = $this->query($sql);
        return $ret;
    }
}
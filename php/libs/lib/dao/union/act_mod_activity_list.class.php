<?php
namespace Dao\Union;
use Dao;

/**
 *  活动列表
 * Class Act_Mod_Activity_List
 * @package Dao\Union
 */
class Act_Mod_Activity_List extends Union {
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

    public function add_activity($data){
        return $this->add($data);
    }

    public function get_activity_info($aid){
        $sql = "select * from {$this->_realTableName} where aid ={$aid} limit 1";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }

}
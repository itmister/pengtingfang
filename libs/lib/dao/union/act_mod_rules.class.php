<?php
namespace Dao\Union;
use Dao;

/**
 *  活动列表
 * Class Act_Mod_Rules
 * @package Dao\Union
 */
class Act_Mod_Rules extends Union {
    protected static $_instance = null;
    /**
     * @return Dao\Union\Act_Mod_Rules
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function add_rule($data){
        return $this->add($data);
    }

    public function get_rule_info($rid){
        $sql = "select * from {$this->_realTableName} where rid ={$rid} limit 1";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }

}
<?php
namespace Dao\Union;
use \Dao;
class Credit_Stat extends Union {

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

    public function add_stat($data){
        return $this->add($data);
    }

    public function get_day_info($uid,$type,$ymd){
        $sql = "select * from {$this->_realTableName} where uid ={$uid} and type = {$type} and ymd = {$ymd} limit 1";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }

    public function update_info($id,$data){
        return $this->update('id='.$id,$data);
    }
}

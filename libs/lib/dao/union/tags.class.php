<?php
namespace Dao\Union;
use \Dao;

/**
 * @package Dao\Union
 */
class Tags extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Tags
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function tags($status = null){
        if ($status !== null ) $status = ' AND status ='.$status;
        $sql = "select * from {$this->_realTableName} where 1 $status ORDER BY `order` desc,mutex_priority desc ";
        $ret = $this->query($sql);
        return $ret;
    }

    public function tag_info($t_id){
        $sql = "select * from {$this->_realTableName} where t_id = {$t_id} limit 1";
        $ret = $this->query($sql);
        return $ret[0] ? $ret[0]:[];
    }

    public function modify_tag($t_id,$data){
        $ret = $this->update("t_id = {$t_id}",$data);
       // echo $this->get_last_sql();
        return $ret;
    }

    public function add_tag($data){
        $ret =  $this->add($data);
        //echo $this->get_last_sql();
        return $ret;
    }

}

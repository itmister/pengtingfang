<?php

namespace Dao\Union;
use \Dao;
class Act_Credit_Apply extends Union {

    protected static $_instance = null;

    /**
     * @return Act_credit
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_list($admin,$page,$offset){
        $where = 'status > -1 ';
        if ($admin){
            $where .= 'and manger = '.$admin;
        }
        $sql = "select * from {$this->_realTableName}  where {$where}  order by ctime desc limit {$offset},{$page}";
        return $this->query($sql);
    }

    public function get_count($admin){
        $where = 'status > -1 ';
        if ($admin){
            $where .= 'and manger = '.$admin;
        }
        $sql = "select count(1) as num from {$this->_realTableName} where {$where}";
        $ret = $this->query($sql);
        return $ret[0]['num'];
    }

    public function get_apply_info( $apply_id){
        $sql = "select * from {$this->_realTableName} where id = {$apply_id} limit 1";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }

    public function add_apply($data){
        return $this->add($data);
    }

    public function update_apply($apply_id,$data){
        return $this->update('id='.$apply_id,$data);
    }
}

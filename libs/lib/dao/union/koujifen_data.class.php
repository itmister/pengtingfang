<?php
/**
 * 站内信
 */
namespace Dao\Union;
use \Dao;

class Koujifen_data extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Koujifen_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_list($koujifen_batch_id,$page,$offset){
        $where = 'koujifen_batch_id='.$koujifen_batch_id;
        $sql = "select * from {$this->_realTableName}  where {$where} order by `ip_count` desc limit {$offset},{$page}";
        return $this->query($sql);
    }

    public function get_list_all($koujifen_batch_id){
        $where = 'koujifen_batch_id='.$koujifen_batch_id;
        $sql = "select * from {$this->_realTableName}  where {$where} order by `ip_count` desc";
        return $this->query($sql);
    }

    public function get_count($koujifen_batch_id){
        $where = 'koujifen_batch_id='.$koujifen_batch_id;
        $sql = "select count(1) as num from {$this->_realTableName} where {$where}";
        $ret = $this->query($sql);
        return $ret[0]['num'];
    }
}

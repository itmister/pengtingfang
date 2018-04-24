<?php
/**
 *
 */
namespace Dao\Union;
use \Dao;

class Kouipcount_data extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Kouipcount_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_list($kouipcount_batch_id,$page,$offset){
        $where = 'kouipcount_batch_id='.$kouipcount_batch_id;
        $sql = "select * from {$this->_realTableName}  where {$where} order by `ip_count` desc limit {$offset},{$page}";
        return $this->query($sql);
    }

    public function get_count($kouipcount_batch_id){
        $where = 'kouipcount_batch_id='.$kouipcount_batch_id;
        $sql = "select count(1) as num from {$this->_realTableName} where {$where}";
        $ret = $this->query($sql);
        return $ret[0]['num'];
    }

    public function get_list_by_uid($kouipcount_batch_id){
        $where = 'kouipcount_batch_id='.$kouipcount_batch_id;
        $sql = "select `no`,`point_total`,`point_dai`,point,point_exchange,sum(`ip_count`) as ip_count ,sum(`y_count`) as y_count,sum(`s_count`) as s_count
         from {$this->_realTableName}  where {$where} group by `no` order by `no` asc";
        $ret = $this->query($sql);
        return $ret;
    }
}

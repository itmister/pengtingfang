<?php
/**
 * 扣积分
 */
namespace Dao\Union;
use \Dao;

class Kouipcount_batch extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Kouipcount_batch
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_info($koujifen_batch_id){
        $where = 'kouipcount_batch_id='.$koujifen_batch_id;
        $sql = "select * from {$this->_realTableName}  where {$where}";
        return $this->query($sql);
    }
    public function get_list($start,$end,$page,$offset){
        $where = 'status=1';
        if ($start){
            $where .= ' and inputtime >= '.$start;
        }
        if ($end){
            $where .= ' and inputtime <= '.$end;
        }
        $sql = "select * from {$this->_realTableName}  where {$where} order by inputtime desc limit {$offset},{$page}";
        return $this->query($sql);
    }

    public function get_count($start,$end){
        $where = 'status=1';
        if ($start){
            $where .= ' and inputtime >= '.$start;
        }
        if ($end){
            $where .= ' and inputtime <= '.$end;
        }
        $sql = "select count(1) as num from {$this->_realTableName} where {$where}";
        $ret = $this->query($sql);
        return $ret[0]['num'];
    }
}

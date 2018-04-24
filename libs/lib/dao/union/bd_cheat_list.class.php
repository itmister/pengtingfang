<?php

namespace Dao\Union;
use \Dao;
class Bd_Cheat_List extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Bd_Cheat_List
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function get_last_recode($uid){
        $sql = "select * from {$this->_realTableName} where uid = {$uid} and status = 0 order by ctime desc limit 1";
        return $this->query($sql)[0];
    }

    /**
     * @param $conditions
     * @param $offset
     * @param int $prepage
     * @return mixed
     */
    public function get_list($conditions,$offset,$prepage = 20){
        $where = "1";
        foreach ($conditions as $k => $v) {
            if ($k == 'c_start' ){
                $v && $where .=" AND ctime >= '{$v}'";
            }elseif($k ==  'g_start'){
                $v && $where .=" AND gtime >= '{$v}'";
            }elseif($k ==  'c_end'){
                $v && $where .=" AND ctime <= '{$v}'";
            }elseif($k ==  'g_end'){
                $v && $where .=" AND gtime <= '{$v}'";
            }elseif($k ==  'status'){
                if ($v != -1 && !is_null($v)) $where .=" AND status = $v";
            }else{
                $v &&  $where .=" AND {$k} = '{$v}'";
            }
        }
        $sql  = "select * from  {$this->_realTableName} where {$where} ORDER  by id desc  limit {$offset},{$prepage}";
        return $this->query($sql);
    }

    /**
     * @param $conditions
     * @return int
     */
    public function get_count($conditions){
        $where = "1";
        foreach ($conditions as $k => $v) {
            if ($k == 'c_start' ){
                $v && $where .=" AND ctime >= '{$v}'";
            }elseif($k ==  'g_start'){
                $v && $where .=" AND gtime >= '{$v}'";
            }elseif($k ==  'c_end'){
                $v && $where .=" AND ctime <= '{$v}'";
            }elseif($k ==  'g_end'){
                $v && $where .=" AND gtime <= '{$v}'";
            }elseif($k ==  'status'){
                is_null($v);
                if ($v != -1 && !is_null($v))  $where .=" AND status = $v";
            }else{
                $v &&  $where .=" AND {$k} = '{$v}'";
            }
        }
        $sql  = "select count(1) as num from {$this->_realTableName}  where {$where} ";
        $ret = $this->query($sql);
        return $ret[0]['num']?$ret[0]['num']:0;
    }

    public function change_status($id){
       return  $this->update('id='.$id,['status'=>1,'gtime'=>date("Y-m-d H:i:s")]);
    }
}
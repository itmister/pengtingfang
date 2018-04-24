<?php
namespace Dao\Union;
use \Dao;
class User_change_log extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\User_change_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }


    /**
     * 设置用户礼花
     * @param $uid
     * @param $lihua = 0
     */
    public function add_log($array) {
        return $this->add($array);
    }

    /*查询用户收支记录明细
     * $start 开始时间
     * $end 结束时间
     * $type 收支类型 1，收入 2，支出 空全部
     * */
    public function get_user_change_log_list($start,$end,$type,$uid){
        $where = "dateline>={$start} and dateline<={$end} and uid={$uid}";
        if($type){
            $where .=" and type={$type}";
        }
        $sql = "select dateline,credit,type,ip_count,name,rule,user_type,uid,ymd
                from {$this->_realTableName} where {$where} order by dateline desc";
        $list = $this->query($sql);
        return $list;
    }
}

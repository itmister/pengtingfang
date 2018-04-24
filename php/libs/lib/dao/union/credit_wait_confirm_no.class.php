<?php
/**
 * 预计积分
 */
namespace Dao\Union;
use \Dao;
class Credit_wait_confirm_no extends Union {


    /**
     * @return Dao\Union\Credit_wait_confirm_no
     */
    public static function get_instance(){ return parent::get_instance(); }

    /**
     *个人中心 推广业绩 每天的详细列表
     */
    public function get_user_credit_day($uid,$ymd){
        $sql = "select name, sum(credit) as credit
                from {$this->_realTableName} where uid={$uid} and ymd={$ymd} and delete_flag=0 and is_get<>2 and sub_type <> 10 and name not in ('hao123','sgdh','360dh','jsdh') group by name";
        $list = $this->query($sql);
        return $list;
    }


    /**
     *个人中心 推广业绩 每天的详细列表
     */
    public function get_user_dh_credit_day($uid,$ymd){
        $sql = "select dateline,is_get,name, type, sub_type, sum(credit) as credit,sum(ip_count) as ip_count
                from {$this->_realTableName} where uid={$uid} and ymd={$ymd} and name in ('hao123','sgdh','360dh','jsdh') and delete_flag=0 and is_get<>2 group by name";
        $list = $this->query($sql);
        return $list;
    }

    /**
     * 未发收入
     * @param $uid 用户uid
     * @return integer
     */
    public function not_get($uid) {
        $uid = intval($uid);
        $sql = "
        select
            sum(credit) as credit
        from
          {$this->_get_table_name()}
        WHERE
          uid={$uid}
          and is_get=0
        ";
        $rows = $this->query($sql );
        return intval( reset($rows)['credit'] );
    }
}

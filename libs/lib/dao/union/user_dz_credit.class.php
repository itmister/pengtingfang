<?php
/**
 * 积分
 */
namespace Dao\Union;
use \Dao;
class User_dz_credit extends Union {

    protected static $_instance = null;

    /**
     * @return User_dz_credit
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 取用户活动积分
     * @param $act_id
     * @param $uid
     * @return integer
     */
    public function get_credit_count($ymd) {
        $sql = "
                    SELECT count(a.uid) as num
                    FROM discuz.dx_common_member as a LEFT JOIN discuz.dx_dsu_paulsign as b on a.uid=b.uid
                    LEFT join `union`.user_dz_credit as c on a.uid=c.dz_uid and c.ymd={$ymd} LEFT JOIN `union`.`user` as d on a.username=d.`name`
                    where a.uid>1 and d.id is not NULL;
                ";
        $info = $this->query($sql);
        return $info[0]['num']?$info[0]['num']:0;
    }

    public function get_credit($ymd,$limit){
        $date = date("Ymd");
        $sql = "
            SELECT a.uid as dz_uid,{$date} as ymd,a.username,a.credits as credit,b.reward,
            (a.credits-(case when c.credit>0 then c.credit else 0 end)) as day_credit,
            (b.reward-(case when c.reward>0 then c.reward else 0 end)) as day_reward,
            UNIX_TIMESTAMP() as dateline,d.id as uid FROM discuz.dx_common_member as a
            LEFT JOIN discuz.dx_dsu_paulsign as b on a.uid=b.uid
            LEFT join `union`.user_dz_credit as c on a.uid=c.dz_uid and c.ymd={$ymd}
            LEFT JOIN `union`.`user` as d on a.username=d.`name`
            where a.uid>1 and d.id is not NULL
        ";
        if($limit){
            $sql .= " limit ".$limit;
        }
        return $this->query($sql);
    }

    public function get_user_credit_emp(){
        $data = date("Ymd");
        $sql = "SELECT uid,(case when day_credit>60 then 60 else day_credit end) as day_credit FROM {$this->_realTableName} WHERE ymd={$data} and day_credit>0";
        return $this->query($sql);
    }

    public function get_user_sign_emp(){
        $data = date("Ymd");
        $sql = "SELECT uid,day_reward FROM {$this->_realTableName} WHERE ymd={$data} and day_reward>0";
        return $this->query($sql);
    }

    public function delete_before_7($ymd){
        $sql = " delete from {$this->_realTableName} where ymd={$ymd};";
        return $this->query($sql);
    }
}

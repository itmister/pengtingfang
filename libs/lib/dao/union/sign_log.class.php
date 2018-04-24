<?php
/**
 * 签到日志
 */
namespace Dao\Union;
use \Dao;
class Sign_Log extends Union {

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

    /**
     * 统计一段时间用户的签到次数
     * @param $uid
     * @param $start_date
     * @param $end_date
     * @return int
     */
    public function get_sign_count($uid,$start_date,$end_date){
        $sql  ="SELECT count(1) AS num from {$this->_realTableName} where uid={$uid} and dateline>={$start_date} and dateline<={$end_date}";
        $ret = $this->query($sql);
        return $ret[0]['num']?$ret[0]['num']:0;
    }
}

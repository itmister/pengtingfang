<?php
/**
 * 积分
 */
namespace Dao\Union;
use \Dao;
class Act_credit extends Union {

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
     * 取用户活动积分
     * @param $act_id
     * @param $uid
     * @return integer
     */
    public function get_credit($act_id, $uid) {
        return intval( $this->get_one('act_credit', "act_id='{$act_id}' AND uid='{$uid}'"));
    }
}

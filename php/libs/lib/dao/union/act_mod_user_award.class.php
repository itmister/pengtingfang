<?php
namespace Dao\Union;
use Dao;

/**
 *  活动模型奖励发放记录
 * Class Act_Mod_User_Award
 * @package Dao\Union
 */
class Act_Mod_User_Award extends Union {
    protected static $_instance = null;
    /**
     * @return Dao\Union\Act_Mod_User_Award
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 添加发奖记录
     * @param $uid  用户id
     * @param $rid  规则id
     * @param $pname 奖励礼包名
     * @param int $pid 礼包id
     * @param int $send_status 发送状态  1：已经发送，0为发送
     * @return int|string
     */
    public function sendAward($uid,$rid,$pname,$pid=0,$send_status = 1,$num =1){
        $arr =[
            'uid'=>$uid,
            'rid'=>$rid,
            'pname'=>$pname,
            'pid'=>$pid,
            'send_status'=>$send_status,
            'num'=>$num,
            'ctime'=>date('Y-m-d H:i:s')
        ];
        return $this->add($arr);
    }

    /**
     * 用户发奖记录
     * @param $uid
     * @param $rid
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getUserInfo($uid,$rid,$offset=0,$limit=2000){
        $sql = "select * from {$this->_realTableName} where uid ={$uid} and rid={$rid} order by ctime desc limit {$offset},{$limit}";
        return $this->query($sql);
    }

    public function getList($limit,$where = ''){
        $sql = "select * from {$this->_realTableName} {$where} ORDER by ctime desc limit {$limit}";
        return $this->query($sql);
    }
}
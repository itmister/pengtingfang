<?php
namespace Dao\Union;
use Dao;

/**
 * Class Phone_code
 * @package Dao\Phone_code
 */
class Phone_code extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Phone_code
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 获取上一次发送验证码的时间戳
     * @param integer $uid 用户id
     * @return integer
     */
    public function last_send_dateline($uid) {
        $time  = (time() - 1800);
        $sql   = "SELECT dateline FROM {$this->_get_table_name()} WHERE uid = {$uid} AND dateline > {$time} ORDER BY id DESC";
        $query = current($this->query($sql));
        return $query['dateline'];
    }
}
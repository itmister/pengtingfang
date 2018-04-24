<?php
namespace Dao\Guangsuss_admin\Stat;
use \Dao;
class Sub_channel_info extends \Dao\Guangsuss_admin\Guangsuss_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Sub_channel_info
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_sub_channel_list($ymd,$soft_id){
        $sql = "SELECT {$ymd} as ymd,a.sub_channel as qid,a.tn as to_{$soft_id}_tn,UNIX_TIMESTAMP() as dateline,1 as is_me
        FROM `{$this->_realTableName}` as a
        where a.`status`=0 and a.soft_id='{$soft_id}' GROUP BY a.sub_channel";
        return $this->query($sql);
    }

}
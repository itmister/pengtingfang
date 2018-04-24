<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao;
class Sub_channel_dh_data extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Sub_channel_dh_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_channel($ymd){
        $time = time();
        $sql = "SELECT ymd,substring_index(qid, '_', 1) as qid,
        sum(`all_num`)as `all_num`,
        sum(`online`)as `online`,
        sum(to_hao123_num)as to_hao123_num,
        sum(`to_hao123_ip_num`)as `to_hao123_ip_num`,
        sum(to_360dh_num)as to_360dh_num,
        sum(to_360dh_ip_num) as to_360dh_ip_num,
        sum(`to_other_num`)as to_other_num,
        {$time} as dateline
         FROM `{$this->_realTableName}` WHERE ymd={$ymd} GROUP BY substring_index(qid, '_', 1),ymd";
        return $this->query($sql);
    }

    public function update_other_num_qid($ymd){
        $sql = "UPDATE `{$this->_realTableName}` set to_other_num=all_num-(to_360dh_num+to_hao123_num) WHERE ymd={$ymd};";
        return $this->query($sql);
    }
}

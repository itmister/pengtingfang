<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao\Kpzip_admin;
class Channel_list extends \Dao\Kpzip_admin\Kpzip_admin {

    /**
     * @return Channel_list
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    #取我们自己的渠道
    public function get_channel_list($ymd){
        $sql = "select {$ymd} as ymd,channel_name as qid,1 as is_me,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}` ;";
        return $this->query($sql);
    }
}
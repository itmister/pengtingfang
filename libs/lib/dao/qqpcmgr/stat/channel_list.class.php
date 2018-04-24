<?php
namespace Dao\Qqpcmgr\Stat;
use \Dao\Qqpcmgr;
class Channel_list extends \Dao\Qqpcmgr\Qqpcmgr {

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
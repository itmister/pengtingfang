<?php
namespace Dao\Winhome;
use \Dao;
class Channel_list extends Winhome {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Channel_list
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    #取我们自己的渠道
    public function get_channel_list($ymd){
        $sql = "select {$ymd} as ymd,channel_name as qid,1 as is_me,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}` ;";
        return $this->query($sql);
    }
}
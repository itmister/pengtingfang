<?php
namespace Dao\Qqpcmgr\Stat;
use \Dao;
class Tn_channel_data extends \Dao\Qqpcmgr\Qqpcmgr{

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Tn_channel_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    #记录我们tn每天的ip数
    public function in_tn_ip_me(){
        $sql = "insert into `{$this->_realTableName}` (ymd,qid,soft_id,tn,to_ip_num,dateline)(SELECT ymd,qid,soft_id,tn,to_ip_num,UNIX_TIMESTAMP() as dateline FROM wh_tn_channel_temp WHERE is_me=1);";
        return $this->query($sql);
    }



}

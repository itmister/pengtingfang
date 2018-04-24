<?php
namespace Dao\Mykzip_admin\Stat;
use \Dao;
class Tn_channel_temp extends \Dao\Mykzip_admin\Mykzip_admin{

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Tn_channel_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    #标记出那些使我们的tn
    public function tn_update_is_me(){
        $sql="UPDATE `{$this->_realTableName}` as t LEFT JOIN
        (SELECT channel,soft_id,tn FROM stat_sub_channel_info GROUP BY channel,tn,soft_id) as tp on t.qid=tp.channel and t.soft_id=tp.soft_id and t.tn=tp.tn
        set t.is_me=1 where tp.channel is not NULL;";
        return $this->query($sql);
    }

    public function get_all_qid_data(){
        $sql = "SELECT ymd, qid,
                sum(case WHEN is_me=1 and soft_id='hao123' THEN to_num else 0 end) as 'to_hao123_num',
                sum(case WHEN is_me=1 and soft_id='hao123' THEN to_ip_num else 0 end) as 'to_hao123_ip_num',
                sum(case WHEN is_me=1 and soft_id='360dh' THEN to_num else 0 end) as 'to_360dh_num',
                sum(case WHEN is_me=1 and soft_id='360dh' THEN to_ip_num else 0 end) as 'to_360dh_ip_num',
                sum(case WHEN is_me=0 THEN to_num else 0 end) as 'to_other_num', UNIX_TIMESTAMP() as dateline
                 FROM `{$this->_realTableName}` GROUP BY qid;";
        return $this->query($sql);
    }
}

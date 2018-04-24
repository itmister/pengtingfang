<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao\Wplayer_admin;
class Updateinstall_uid_channel_qid_only extends \Dao\Wplayer_admin\Wplayer_admin {

    /**
     * @return Updateinstall_uid_channel_qid_only
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function update_channel_360($ymd){
        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN stat_jingpin_uid_360_temp as b on a.uid=b.uid set a.`is_360`=1 WHERE a.ymd={$ymd} and b.ymd={$ymd} and b.uid is not null;";
        return $this->query($up_sql);
    }

    public function get_ver_list($ymd){
        $sql = "SELECT {$ymd} as ymd,qid,ver,count(*) as updateinstall,count(case when is_360=1 then ymd end) as updateinstall_360,count(case when is_360=0 then ymd end) as updateinstall_no360 FROM `{$this->_realTableName}`  where ymd={$ymd} GROUP BY qid,ver;";
        return $this->query($sql);
    }

    public function get_qid_list($ymd){
        $sql = "SELECT {$ymd} as ymd,qid,count(*) as updateinstall FROM `{$this->_realTableName}`  where ymd={$ymd} GROUP BY qid;";
        return $this->query($sql);
    }
    
}

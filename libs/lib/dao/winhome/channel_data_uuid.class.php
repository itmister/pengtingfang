<?php
namespace Dao\Winhome;
use \Dao;
class Channel_data_uuid extends Winhome {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Channel_data_uuid
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function in_sub_channel_data($ymd){
        $time =time();
        $sql = " insert into `{$this->_realTableName}` (ymd,qid,install,install_total,dateline)(SELECT $ymd,QID as qid,count(case when Ymd = {$ymd} then uid end) as `install`,count(*) as `install_total`,$time FROM wh_install_uuid_channel_qid_only GROUP BY QID) on duplicate key update install=values(install),install_total=values(install_total)";
        return $this->query($sql);
    }

    public function get_uninstall_count($ymd){
        $sql = "create table temp_channel_qid_uuid_uninstall as
SELECT QID as qid,count(*) as `uninstall_total`,count(case when Ymd = {$ymd} then uid end) as `uninstall` FROM wh_uninstall_uuid_channel_qid_only GROUP BY QID;";
        $this->query($sql);

        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN temp_channel_qid_uuid_uninstall as b on a.qid=b.qid set a.`uninstall`=b.`uninstall`,a.uninstall_total=b.uninstall_total WHERE a.ymd={$ymd};";
        $this->query($up_sql);
        $drop_sql = "DROP TABLE temp_channel_qid_uuid_uninstall;";
        return $this->query($drop_sql);
    }

    public function get_online_count($ymd){
        $sql = "create table temp_channel_qid_uuid_online as
                SELECT b.num,b.qid FROM `{$this->_realTableName}` as a LEFT JOIN (
                 SELECT count(*) as num , qid FROM wh_online_uuid_channel_qid_temp GROUP BY qid
                ) as b on a.qid=b.qid WHERE a.ymd={$ymd} and b.num is not NULL
        ";
        $this->query($sql);

        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN temp_channel_qid_uuid_online as b on a.qid=b.qid set a.`online`=b.`num` WHERE a.ymd={$ymd};";
        $this->query($up_sql);

        $drop_sql = "DROP TABLE temp_channel_qid_uuid_online;";
        return $this->query($drop_sql);
    }

}

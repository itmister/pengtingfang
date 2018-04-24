<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao\Kpzip_admin;
class Channel_data extends \Dao\Kpzip_admin\Kpzip_admin {

    /**
     * @return Channel_data
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function in_sub_channel_data($ymd){
        $time =time();
        $sql = " insert into `{$this->_realTableName}` (ymd,qid,install,install_total,dateline)(SELECT $ymd,QID as qid,count(case when Ymd = {$ymd} then Ymd end) as `install`,count(*) as `install_total`,$time FROM stat_install_uid_channel_qid_only GROUP BY QID) on duplicate key update install=values(install),install_total=values(install_total)";
        return $this->query($sql);
    }

    public function get_uninstall_count($ymd){
        $sql = "create table temp_channel_qid_uninstall as
SELECT QID as qid,count(*) as `uninstall_total`,count(case when Ymd = {$ymd} then Ymd end) as `uninstall` FROM stat_uninstall_uid_channel_qid_only GROUP BY QID;";
        $this->query($sql);

        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN temp_channel_qid_uninstall as b on a.qid=b.qid set a.`uninstall`=b.`uninstall`,a.uninstall_total=b.uninstall_total WHERE a.ymd={$ymd};";
        $this->query($up_sql);
        $drop_sql = "DROP TABLE temp_channel_qid_uninstall;";
        return $this->query($drop_sql);
    }

    public function get_online_count($ymd){
        $sql = "create table temp_channel_qid_online as
                SELECT b.num,b.qid FROM `{$this->_realTableName}` as a LEFT JOIN (
                 SELECT count(*) as num , qid FROM stat_online_uid_channel_qid_temp GROUP BY qid
                ) as b on a.qid=b.qid WHERE a.ymd={$ymd} and b.num is not NULL
        ";
        $this->query($sql);

        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN temp_channel_qid_online as b on a.qid=b.qid set a.`online`=b.`num` WHERE a.ymd={$ymd};";
        $this->query($up_sql);

        $drop_sql = "DROP TABLE temp_channel_qid_online;";
        return $this->query($drop_sql);
    }

}

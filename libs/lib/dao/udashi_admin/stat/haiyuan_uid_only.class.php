<?php
namespace Dao\Udashi_admin\Stat;
use \Dao\Udashi_admin;
class Haiyuan_uid_only extends \Dao\Udashi_admin\Udashi_admin {

    /**
     * @return Haiyuan_uid_only
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_ymd_data($ymd){
        $sql = "SELECT {$ymd} as ymd,count(case when ymd <= {$ymd} then ymd end) as `haiyuan_total`,count(case when ymd = {$ymd} then ymd end) as `haiyuan` FROM `{$this->_realTableName}`";
        return $this->query($sql);
    }

    public function get_qid_data($ymd){
//        $sql = "SELECT {$ymd} as ymd,qid,count(case when ymd <= {$ymd} then ymd end) as `haiyuan_total`,count(case when ymd = {$ymd} then ymd end) as `haiyuan` FROM `{$this->_realTableName}` group by qid";
//        return $this->query($sql);

        $sql = "create table temp_qid_haiyuan as
SELECT {$ymd} as ymd,qid,count(case when ymd <= {$ymd} then ymd end) as `haiyuan_total`,count(case when ymd = {$ymd} then ymd end) as `haiyuan` FROM `{$this->_realTableName}` group by qid;";
        $this->query($sql);

        $up_sql = "UPDATE `stat_channel_data` as a LEFT JOIN temp_qid_haiyuan as b on a.qid=b.qid and a.ymd=b.ymd set a.`haiyuan`=b.`haiyuan`,a.haiyuan_total=b.haiyuan_total WHERE a.ymd={$ymd};";
        $this->query($up_sql);

        $up_sql_ue = "UPDATE `stat_channel_data_uefi` as a LEFT JOIN temp_qid_haiyuan as b on a.qid=b.qid and a.ymd=b.ymd set a.`haiyuan`=b.`haiyuan`,a.haiyuan_total=b.haiyuan_total WHERE a.ymd={$ymd};";
        $this->query($up_sql_ue);

        $drop_sql = "DROP TABLE temp_qid_haiyuan;";
        return $this->query($drop_sql);
    }

    public function get_sub_qid_data($ymd){
//        $sql = "SELECT {$ymd} as ymd,sub_qid as qid,count(case when ymd <= {$ymd} then ymd end) as `haiyuan_total`,count(case when ymd = {$ymd} then ymd end) as `haiyuan` FROM `{$this->_realTableName}` group by sub_qid";
//        return $this->query($sql);

        $sql = "create table temp_sub_qid_haiyuan as
SELECT {$ymd} as ymd,sub_qid as qid,count(case when ymd <= {$ymd} then ymd end) as `haiyuan_total`,count(case when ymd = {$ymd} then ymd end) as `haiyuan` FROM `{$this->_realTableName}` group by sub_qid;";
        $this->query($sql);

        $up_sql = "UPDATE `stat_sub_channel_data` as a LEFT JOIN temp_sub_qid_haiyuan as b on a.qid=b.qid and a.ymd=b.ymd set a.`haiyuan`=b.`haiyuan`,a.haiyuan_total=b.haiyuan_total WHERE a.ymd={$ymd};";
        $this->query($up_sql);

        $up_sql_ue = "UPDATE `stat_sub_channel_data_uefi` as a LEFT JOIN temp_sub_qid_haiyuan as b on a.qid=b.qid and a.ymd=b.ymd set a.`haiyuan`=b.`haiyuan`,a.haiyuan_total=b.haiyuan_total WHERE a.ymd={$ymd};";
        $this->query($up_sql_ue);

        $drop_sql = "DROP TABLE temp_sub_qid_haiyuan;";
        return $this->query($drop_sql);
    }
}

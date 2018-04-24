<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Sub_channel_data_uuid extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Sub_channel_data_uuid
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function in_sub_channel_data($ymd){
        $time =time();
        $sql = " insert into `{$this->_realTableName}` (ymd,qid,install,install_total,dateline)(SELECT $ymd,QID as qid,count(case when Ymd = {$ymd} then Ymd end) as `install`,count(*) as `install_total`,$time FROM stat_install_uuid_qid_only GROUP BY QID) on duplicate key update install=values(install),install_total=values(install_total)";
        return $this->query($sql);
    }

    public function get_uninstall_count($ymd){
        $sql = "create table temp_qid_uuid_uninstall as
SELECT QID as qid,count(*) as `uninstall_total`,count(case when Ymd = {$ymd} then Ymd end) as `uninstall` FROM stat_uninstall_uuid_qid_only GROUP BY QID;";
        $this->query($sql);

        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN temp_qid_uuid_uninstall as b on a.qid=b.qid set a.`uninstall`=b.`uninstall`,a.uninstall_total=b.uninstall_total WHERE a.ymd={$ymd};";
        $this->query($up_sql);
        $drop_sql = "DROP TABLE temp_qid_uuid_uninstall;";
        return $this->query($drop_sql);
    }

    public function get_online_count($ymd){
        $sql = "create table temp_qid_uuid_online as
                SELECT b.num,b.qid FROM `{$this->_realTableName}` as a LEFT JOIN (
                 SELECT count(*) as num , qid FROM stat_online_uuid_qid_temp GROUP BY qid
                ) as b on a.qid=b.qid WHERE a.ymd={$ymd} and b.num is not NULL
        ";
        $this->query($sql);

        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN temp_qid_uuid_online as b on a.qid=b.qid set a.`online`=b.`num` WHERE a.ymd={$ymd};";
        $this->query($up_sql);

        $drop_sql = "DROP TABLE temp_qid_uuid_online;";
        return $this->query($drop_sql);
    }

    public function get_channel($ymd){
        $time = time();
        $day = strtotime($ymd);
        $_ymd = date("Ymd",strtotime("-30 days ",$day));
        $sql = "SELECT ymd,substring_index(qid, '_', 1) as qid,sum(`install`)as `install`,
        sum(install_total)as install_total,sum(`online`)as `online`,sum(active)as active,
        sum(`uninstall`)as `uninstall`,sum(uninstall_total)as uninstall_total,
        sum(install_uninstall) as install_uninstall,sum(`uninstall1`)as uninstall1,
        sum(uninstall7)as uninstall7,sum(online1)as online1,sum(online7)as online7,
        sum(online15)as online15,sum(online30)as online30,{$time} as dateline
         FROM `{$this->_realTableName}` WHERE ymd>={$_ymd} and ymd<={$ymd} GROUP BY substring_index(qid, '_', 1),ymd";
        return $this->query($sql);
    }
}

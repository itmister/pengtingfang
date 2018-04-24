<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao\Huayangnianhua_admin;
class Install_uid_qid_only extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    /**
     * @return Install_uid_qid_only
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_qid_uninstall_count($ymd){
        $sql = "SELECT a.QID as qid,a.Ymd as ymd,count(*) as install_uninstall
        FROM `{$this->_realTableName}` as a LEFT JOIN stat_uninstall_uid_qid_only as b on a.uid=b.uid and a.QID=b.QID and a.Ymd=b.Ymd WHERE a.Ymd={$ymd} and b.Ymd={$ymd} GROUP BY a.QID";
        return $this->query($sql);
    }
    public function get_all_kpzip_install($ymd){
        $sql = "SELECT QID AS qid,Ymd AS ymd,count(DISTINCT uid) AS kpzip_install FROM `{$this->_realTableName}` WHERE Ymd = {$ymd} AND repeat_install = 1 GROUP BY QID";
        return $this->query($sql);
    }

    public function update_qid_360($ymd){
        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN stat_jingpin_uid_360_temp as b on a.uid=b.uid set a.`is_360`=1 WHERE a.Ymd={$ymd} and b.ymd={$ymd} and b.uid is not null;";
        return $this->query($up_sql);
    }
    public function get_ver_list($ymd){
        $sql = "SELECT {$ymd} as ymd,QID as qid,ver,count(*) as install FROM `{$this->_realTableName}`  where Ymd={$ymd} GROUP BY QID,ver;";
        return $this->query($sql);
    }
}

<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao\Kpzip_admin;
class Install_uid_qid_only extends \Dao\Kpzip_admin\Kpzip_admin {

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
}

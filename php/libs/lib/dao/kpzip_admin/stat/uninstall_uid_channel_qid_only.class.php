<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao;
class Uninstall_uid_channel_qid_only extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Uninstall_uid_channel_qid_only
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    #次日卸载量 和 7日内卸载量
    public function get_uninstall_rate($ymd){
        $sql = "SELECT b.Ymd as ymd,a.QID as qid,count( case when TIMESTAMPDIFF(DAY,b.Ymd,a.Ymd)=1 then a.Ymd end) as uninstall1,
                count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.Ymd)<=7 then a.Ymd end) as uninstall7
                FROM `{$this->_realTableName}` as a LEFT JOIN stat_install_uid_channel_qid_only as b on a.uid=b.uid and a.QID=b.QID
                where a.Ymd={$ymd} and b.uid is not null GROUP BY a.QID,b.Ymd;";
        return $this->query($sql);
    }
}

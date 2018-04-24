<?php
namespace Dao\Winhome\Wp;
use \Dao;
class Install_uid_channel_qid_only extends WP {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Wp\Install_uid_channel_qid_only
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function get_qid_count(){
        $sql ="SELECT QID id,count(*) as `install_total`,count(case when Ymd = 20151208 then uid end) as `install`
        FROM `{$this->_realTableName}` GROUP BY QID;";
        return $this->query($sql);
    }

    public function get_qid_uninstall_count($ymd){
        $sql = "SELECT a.QID as qid,a.Ymd as ymd,count(*) as install_uninstall
        FROM `{$this->_realTableName}` as a LEFT JOIN wp_uninstall_uid_channel_qid_only as b on a.uid=b.uid and a.QID=b.QID and a.Ymd=b.Ymd
        WHERE a.Ymd={$ymd} and b.Ymd={$ymd} GROUP BY a.QID";
        return $this->query($sql);
    }
}

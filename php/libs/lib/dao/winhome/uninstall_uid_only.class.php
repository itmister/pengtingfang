<?php
namespace Dao\Winhome;
use \Dao;
class Uninstall_uid_only extends Winhome {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Uninstall_uid_only
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_uninstall_count($ymd){
        $sql = "SELECT count(*) as `uninstall_total`,count(case when Ymd = {$ymd} then uid end) as `uninstall` FROM `{$this->_realTableName}`";
        return $this->query($sql);
    }

    #次日卸载量 和 7日内卸载量
    public function get_uninstall_rate($ymd){
        $sql = "SELECT b.Ymd as ymd,
        count( case when TIMESTAMPDIFF(DAY,b.Ymd,a.Ymd)=1 then a.uid end) as uninstall1,
        count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.Ymd)<=7 then a.uid end) as uninstall7
        FROM `{$this->_realTableName}` as a LEFT JOIN wh_install_uid_only as b on a.uid=b.uid where a.Ymd={$ymd} and b.uid is not null group by b.Ymd;";
        return $this->query($sql);
    }
}

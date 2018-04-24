<?php
namespace Dao\Winhome;
use \Dao;
class Uninstall_uuid_ver_only extends Winhome {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Uninstall_uuid_ver_only
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    #次日卸载量 和 7日内卸载量
    public function get_uninstall_rate($ymd){
        $sql = "SELECT b.Ymd as ymd,a.ver as ver,count( case when TIMESTAMPDIFF(DAY,b.Ymd,a.Ymd)=1 then a.uid end) as uninstall1,
count(case when TIMESTAMPDIFF(DAY,b.Ymd,a.Ymd)<=7 then a.uid end) as uninstall7
FROM `{$this->_realTableName}` as a LEFT JOIN wh_install_uuid_ver_only as b on a.uid=b.uid and a.ver=b.ver where a.Ymd={$ymd} and b.uid is not null GROUP BY a.ver,b.Ymd;";
        return $this->query($sql);
    }
}

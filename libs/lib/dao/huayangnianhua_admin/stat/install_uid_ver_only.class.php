<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao\Huayangnianhua_admin;
class Install_uid_ver_only extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    /**
     * @return Install_uid_ver_only
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_ver_uninstall_count($ymd){
        $sql = "SELECT a.ver as ver,a.Ymd as ymd,count(*) as install_uninstall
        FROM `{$this->_realTableName}` as a LEFT JOIN stat_uninstall_uid_ver_only as b on a.uid=b.uid and a.ver=b.ver and a.Ymd=b.Ymd
         WHERE a.Ymd={$ymd} and b.Ymd={$ymd} GROUP BY a.ver";
        return $this->query($sql);
    }
    
    public function get_all_kpzip_install($ymd){
        $sql = "SELECT ver,Ymd AS ymd,count(DISTINCT uid) AS kpzip_install FROM `{$this->_realTableName}` WHERE Ymd = {$ymd} AND repeat_install = 1 GROUP BY ver";
        return $this->query($sql);
    }
}

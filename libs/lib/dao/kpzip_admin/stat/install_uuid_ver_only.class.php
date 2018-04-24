<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao\Kpzip_admin;
class Install_uuid_ver_only extends \Dao\Kpzip_admin\Kpzip_admin {

    /**
     * @return Install_uuid_ver_only
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_ver_uninstall_count($ymd){
        $sql = "SELECT a.ver as ver,a.Ymd as ymd,count(*) as install_uninstall
        FROM `{$this->_realTableName}` as a LEFT JOIN stat_uninstall_uuid_ver_only as b on a.uid=b.uid and a.ver=b.ver and a.Ymd=b.Ymd
         WHERE a.Ymd={$ymd} and b.Ymd={$ymd} GROUP BY a.ver";
        return $this->query($sql);
    }
}

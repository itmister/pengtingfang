<?php
namespace Dao\Mykzip_admin\Stat;
use \Dao\Mykzip_admin;
class Install_uid_only extends \Dao\Mykzip_admin\Mykzip_admin {

    /**
     * @return Install_uid_only
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_install_count($ymd){
        $sql = "SELECT count(*) as `install_total`,count(case when Ymd = {$ymd} then Ymd end) as `install` FROM `{$this->_realTableName}`";
    	return $this->query($sql);
    }

    public function get_install_uninstall_count($ymd){
        $sql = "SELECT count(*) as install_uninstall FROM `{$this->_realTableName}` as a left JOIN stat_uninstall_uid_only as b on a.uid=b.uid and a.Ymd=b.Ymd
        WHERE a.Ymd={$ymd} and b.Ymd={$ymd}";
        return $this->query($sql);
    }
}

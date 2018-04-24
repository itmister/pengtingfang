<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao\Wplayer_admin;
class Install_uid_only extends \Dao\Wplayer_admin\Wplayer_admin {

    /**
     * @return Install_uid_only
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_install_count($ymd){
        $sql = "SELECT count(*) as `install_total`,count(case when Ymd = {$ymd} then Ymd end) as `install`,count(case when Ymd = {$ymd} AND repeat_install = 1 then Ymd end) as kpzip_install FROM `{$this->_realTableName}`";
    	return $this->query($sql);
    }

    public function get_install_uninstall_count($ymd){
        $sql = "SELECT count(*) as install_uninstall FROM `{$this->_realTableName}` as a left JOIN stat_uninstall_uid_only as b on a.uid=b.uid and a.Ymd=b.Ymd
        WHERE a.Ymd={$ymd} and b.Ymd={$ymd}";
        return $this->query($sql);
    }

    public function update_pro_360($ymd){
        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN stat_jingpin_uid_360_temp as b on a.uid=b.uid set a.`is_360`=1 WHERE a.Ymd={$ymd} and b.ymd={$ymd} and b.uid is not null;";
        return $this->query($up_sql);
    }

    public function get_install_360_count($ymd){
        $sql = "SELECT count(case when is_360 = 1 and Ymd>=20161118 and Ymd<={$ymd} then Ymd end) as `install360all`,count(case when is_360 = 1 and Ymd = {$ymd} then Ymd end) as `install360`,count(case when is_360 = 0 and Ymd>=20161118 and Ymd<={$ymd} then Ymd end) as `installno360all`,count(case when is_360 = 0 and Ymd = {$ymd} then Ymd end) as `installno360` FROM `{$this->_realTableName}`";
        return $this->query($sql);
    }
}

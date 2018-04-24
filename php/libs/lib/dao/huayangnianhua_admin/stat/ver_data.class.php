<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao;
class Ver_data extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Huayangnianhua_admin\Stat\Ver_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function in_ver_data($ymd){
        $time =time();
        
        //累计用户量、新增用户量
        $sql = "
            SELECT
                {$ymd} AS ymd,
            	ver,
            	COUNT(uid) AS install_total,
            	COUNT(case when Ymd = {$ymd} then uid end) AS `install`,
            	{$time} AS dateline
            FROM stat_install_uid_ver_only 
            GROUP BY ver
         ";
          $install_data = $this->query($sql);
          $this->addAll($install_data,['install_total','install']);
        
        
          //版本累计用户量
        $sql = "
            SELECT 
                {$ymd} AS ymd,
                ver,
                COUNT(uid) AS install_ver_total,
                {$time} AS dateline 
            FROM stat_install_ver_uid_only
            GROUP BY ver
        ";
        $install_ver_data = $this->query($sql);
        $this->addAll($install_ver_data,['install_ver_total']);
        
    }

    public function get_ver_uninstall_count($ymd){
        //DROP TABLE IF EXISTS `temp_ver_uninstall`;
        $sql = "
create table temp_ver_uninstall as
SELECT ver,count(*) as `uninstall_total`,count(case when Ymd = {$ymd} then Ymd end) as `uninstall` FROM stat_uninstall_uid_ver_only GROUP BY ver;";
        $this->query($sql);

        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN temp_ver_uninstall as b on a.ver=b.ver set a.`uninstall`=b.`uninstall`,a.uninstall_total=b.uninstall_total WHERE a.ymd={$ymd};";
        $this->query($up_sql);
        $drop_sql = "DROP TABLE temp_ver_uninstall;";
        return $this->query($drop_sql);
    }

    public function get_ver_online_count($ymd){
        //DROP TABLE IF EXISTS `temp_ver_online`;
        $sql = "
create table temp_ver_online as
                SELECT b.num,b.ver FROM `{$this->_realTableName}` as a LEFT JOIN (
                 SELECT count(*) as num , ver FROM stat_online_uid_ver_temp GROUP BY ver
                ) as b on a.ver=b.ver WHERE a.ymd={$ymd} and b.num is not NULL;
        ";
        $this->query($sql);

        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN temp_ver_online as b on a.ver=b.ver set a.`online`=b.`num` WHERE a.ymd={$ymd} and b.`num`>0;";
        $this->query($up_sql);

        $drop_sql = "DROP TABLE temp_ver_online;";
        return $this->query($drop_sql);
    }
}

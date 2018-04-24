<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao;
class Rate_process_list extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Rate_process_list
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_count_ymd($where){
        $sql = "SELECT count(DISTINCT Ymd) as num FROM `{$this->_realTableName}` where {$where}";
        return $this->query($sql);
    }
    /*
     * $sql = "SELECT a.Ymd ,sum(case when a.SaveSoftId='zhudongfangyu.exe' then a.InstallCount end) as '360安全卫士',
                sum(case when a.SaveSoftId='qqpcrtp.exe' then a.InstallCount end) as 'QQ管家',
                sum(case when a.SaveSoftId='kxescore.exe' then a.InstallCount end) as '金山毒霸',
                sum(case when a.SaveSoftId='baiduansvc.exe' then a.InstallCount end) as '百度卫士',
                sum(case when a.SaveSoftId='ravmond.exe' then a.InstallCount end) as '瑞星杀毒',
                sum(case when a.SaveSoftId='ns.exe' then a.InstallCount end) as '诺顿',
                sum(case when a.SaveSoftId='2345service.exe' then a.InstallCount end) as '2345安全卫士',
                sum(case when a.SaveSoftId='homesafe.exe' then a.InstallCount end) as 'homesafe.exe',
                sum(case when a.SaveSoftId='homelock.exe' then a.InstallCount end) as 'homelock.exe',
                a.OnlineCount FROM (
                SELECT * FROM `{$this->_realTableName}` WHERE {$where}
                ) as a GROUP BY a.Ymd order by {$orderby} limit {$limit};";
     */
    public function select_data($where,$orderby,$limit){
        $sql = "SELECT a.Ymd ,sum(case when a.SaveSoftId='zhudongfangyu.exe' then a.InstallCount end) as 'zhudongfangyu.exe',
                sum(case when a.SaveSoftId='qqpcrtp.exe' then a.InstallCount end) as 'qqpcrtp.exe',
                sum(case when a.SaveSoftId='kxescore.exe' then a.InstallCount end) as 'kxescore.exe',
                sum(case when a.SaveSoftId='baiduansvc.exe' then a.InstallCount end) as 'baiduansvc.exe',
                sum(case when a.SaveSoftId='ravmond.exe' then a.InstallCount end) as 'ravmond.exe',
                sum(case when a.SaveSoftId='ns.exe' then a.InstallCount end) as 'ns.exe',
                sum(case when a.SaveSoftId='2345service.exe' then a.InstallCount end) as '2345service.exe',
                sum(case when a.SaveSoftId='homesafe.exe' then a.InstallCount end) as 'homesafe.exe',
                sum(case when a.SaveSoftId='homelock.exe' then a.InstallCount end) as 'homelock.exe',
                sum(case when a.SaveSoftId='hlsys.exe' then a.InstallCount end) as 'hlsys.exe',
                a.OnlineCount FROM (
                SELECT * FROM `{$this->_realTableName}` WHERE {$where}
                ) as a GROUP BY a.Ymd order by {$orderby} limit {$limit};";
        return $this->query($sql);
    }
}

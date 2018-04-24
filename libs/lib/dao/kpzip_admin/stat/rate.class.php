<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao;
class Rate extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Rate
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_count_ymd($where){
        $sql = "SELECT count(DISTINCT Ymd) as num FROM wh_rate where {$where}";
        return $this->query($sql);
    }
    public function select_data($where,$orderby,$limit){
        $sql = "SELECT a.Ymd ,sum(case when a.SaveSoftId='360aqws' then a.InstallCount end) as '360aqws',
                sum(case when a.SaveSoftId='2345ws' then a.InstallCount end) as '2345ws',
                sum(case when a.SaveSoftId='qqgj' then a.InstallCount end) as 'qqgj',
                sum(case when a.SaveSoftId='jsdb' then a.InstallCount end) as 'jsdb',
                sum(case when a.SaveSoftId='bdws' then a.InstallCount end) as 'bdws',
                sum(case when a.SaveSoftId='norton' then a.InstallCount end) as 'norton',
                sum(case when a.SaveSoftId='rxsd' then a.InstallCount end) as 'rxsd',
                a.OnlineCount FROM (
                SELECT * FROM stat_rate WHERE {$where}
                ) as a GROUP BY a.Ymd order by {$orderby} limit {$limit};";
        return $this->query($sql);
    }
}

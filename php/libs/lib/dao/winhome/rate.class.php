<?php
namespace Dao\Winhome;
use \Dao;
class Rate extends Winhome {

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
        $sql = "SELECT a.Ymd ,sum(case when a.SaveSoftId='360ws' then a.InstallCount end) as '360ws',
                sum(case when a.SaveSoftId='2345ws' then a.InstallCount end) as '2345ws',
                sum(case when a.SaveSoftId='qqgj' then a.InstallCount end) as 'qqgj',
                sum(case when a.SaveSoftId='jsdb' then a.InstallCount end) as 'jsdb',
                sum(case when a.SaveSoftId='bdws' then a.InstallCount end) as 'bdws',
                sum(case when a.SaveSoftId='ndsd' then a.InstallCount end) as 'ndsd',
                sum(case when a.SaveSoftId='rxsd' then a.InstallCount end) as 'rxsd',
                sum(case when a.SaveSoftId='lgzyws' then a.InstallCount end) as 'lgzyws',
                sum(case when a.SaveSoftId='xbzyws' then a.InstallCount end) as 'xbzyws',
                sum(case when a.SaveSoftId='yssy' then a.InstallCount end) as 'yssy',
                sum(case when a.SaveSoftId='bjszygj' then a.InstallCount end) as 'bjszygj',
                a.OnlineCount FROM (
                SELECT * FROM wh_rate WHERE {$where}
                ) as a GROUP BY a.Ymd order by {$orderby} limit {$limit};";
        return $this->query($sql);
    }
}

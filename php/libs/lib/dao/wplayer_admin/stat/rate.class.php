<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Rate extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Rate
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_count_ymd($where){
        $sql = "SELECT count(DISTINCT Ymd) as num FROM stat_rate where {$where}";
        return $this->query($sql);
    }
    public function select_data($where,$orderby,$limit){
        $sql = "SELECT a.Ymd ,sum(case when a.SaveSoftId='360aqws' then a.InstallCount end) as '360aqws',
                sum(case when a.SaveSoftId='360aqws' then a.OnlyInstallCount end) as 'only_360aqws',
                sum(case when a.SaveSoftId='2345ws' then a.InstallCount end) as '2345ws',
                sum(case when a.SaveSoftId='qqgj' then a.InstallCount end) as 'qqgj',
                sum(case when a.SaveSoftId='qqgj' then a.OnlyInstallCount end) as 'only_qqgj',
                sum(case when a.SaveSoftId='jsdb' then a.InstallCount end) as 'jsdb',
                sum(case when a.SaveSoftId='jsdb' then a.OnlyInstallCount end) as 'only_jsdb',
                sum(case when a.SaveSoftId='bdws' then a.InstallCount end) as 'bdws',
                sum(case when a.SaveSoftId='norton' then a.InstallCount end) as 'norton',
                sum(case when a.SaveSoftId='rxsd' then a.InstallCount end) as 'rxsd',
                sum(case when a.SaveSoftId='kuaizip' then a.InstallCount end) as 'kuaizip',
                sum(case when a.SaveSoftId='youdao' then a.InstallCount end) as 'youdao',
                sum(case when a.SaveSoftId='yinxiang' then a.InstallCount end) as 'yinxiang',
                sum(case when a.SaveSoftId='htkk' then a.InstallCount end) as 'htkk',
                sum(case when a.SaveSoftId='nosd' then a.InstallCount end) as 'nosd',
                a.OnlineCount FROM (
                SELECT * FROM stat_rate WHERE {$where}
                ) as a GROUP BY a.Ymd order by {$orderby} limit {$limit};";
        return $this->query($sql);
    }
    
    //浏览器竞品数据
    public function select_browser_data($where,$orderby,$limit){
        $sql = "
            SELECT a.Ymd ,
                sum(case when a.SaveSoftId='360aqllq' then a.InstallCount end) as '360aqllq',
                sum(case when a.SaveSoftId='360aqllq' then a.OnlyInstallCount end) as 'only_360aqllq',
                sum(case when a.SaveSoftId='qqllq' then a.InstallCount end) as 'qqllq',
                sum(case when a.SaveSoftId='qqllq' then a.OnlyInstallCount end) as 'only_qqllq',
                sum(case when a.SaveSoftId='2345llq' then a.InstallCount end) as '2345llq',
                sum(case when a.SaveSoftId='2345llq' then a.OnlyInstallCount end) as 'only_2345llq',
                sum(case when a.SaveSoftId='lbllq' then a.InstallCount end) as 'lbllq',
                sum(case when a.SaveSoftId='lbllq' then a.OnlyInstallCount end) as 'only_lbllq',
                sum(case when a.SaveSoftId='360jsllq' then a.InstallCount end) as '360jsllq',
                sum(case when a.SaveSoftId='360jsllq' then a.OnlyInstallCount end) as 'only_360jsllq',
                sum(case when a.SaveSoftId='sgllq' then a.InstallCount end) as 'sgllq',
                sum(case when a.SaveSoftId='sgllq' then a.OnlyInstallCount end) as 'only_sgllq',
                sum(case when a.SaveSoftId='bdllq' then a.InstallCount end) as 'bdllq',
                sum(case when a.SaveSoftId='bdllq' then a.OnlyInstallCount end) as 'only_bdllq',
                sum(case when a.SaveSoftId='ucllq' then a.InstallCount end) as 'ucllq',
                sum(case when a.SaveSoftId='ucllq' then a.OnlyInstallCount end) as 'only_ucllq',
                sum(case when a.SaveSoftId='ggllq' then a.InstallCount end) as 'ggllq',
                sum(case when a.SaveSoftId='ggllq' then a.OnlyInstallCount end) as 'only_ggllq',
                sum(case when a.SaveSoftId='hhllq' then a.InstallCount end) as 'hhllq',
                sum(case when a.SaveSoftId='hhllq' then a.OnlyInstallCount end) as 'only_hhllq',
                sum(case when a.SaveSoftId='jzllq' then a.InstallCount end) as 'jzllq',
                sum(case when a.SaveSoftId='jzllq' then a.OnlyInstallCount end) as 'only_jzllq',
                sum(case when a.SaveSoftId='ayllq' then a.InstallCount end) as 'ayllq',
                sum(case when a.SaveSoftId='ayllq' then a.OnlyInstallCount end) as 'only_ayllq',
                sum(case when a.SaveSoftId='sjzcllq' then a.InstallCount end) as 'sjzcllq',
                sum(case when a.SaveSoftId='sjzcllq' then a.OnlyInstallCount end) as 'only_sjzcllq',
                sum(case when a.SaveSoftId='otherllq' then a.InstallCount end) as 'otherllq',
                a.OnlineCount FROM (
                    SELECT * FROM stat_rate WHERE {$where}
            ) as a GROUP BY a.Ymd order by {$orderby} limit {$limit};";
        return $this->query($sql);
    }
}

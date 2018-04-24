<?php
namespace Dao\Udashidown;
use \Dao;
class Soft_ip_ extends Udashidown{

    /**
     * @return Dao\Udashidown\Soft_ip_
     */
    public static function get_instance(){
        return parent::get_instance();
    }


    public function get_web_ip($ymd){
        #删除七天以前的表
        $delYmd = date("Ymd",strtotime("-7 days",strtotime($ymd)));
        $this->query("DROP TABLE IF EXISTS `temp_soft_ip_{$delYmd}`;");
        $this->query("DROP TABLE IF EXISTS `{$this->_realTableName}{$delYmd}`;");
        #end
        $this->query("DROP TABLE IF EXISTS `temp_soft_ip_{$ymd}`;");
        $sql_temp = "CREATE TABLE temp_soft_ip_{$ymd} as
SELECT * FROM `{$this->_realTableName}{$ymd}` GROUP BY ip";
        $this->query($sql_temp);

        $sql = "SELECT {$ymd} as ymd,username,count(DISTINCT ip) as ip,UNIX_TIMESTAMP() as dateline FROM `temp_soft_ip_{$ymd}` where type=1 and username is not null and username<>'' GROUP BY username";
        return $this->query($sql);
    }


    public function get_web_pv($ymd){
        $sql = "SELECT {$ymd} as ymd,username,count(*) as pv,UNIX_TIMESTAMP() as dateline FROM `{$this->_realTableName}{$ymd}` where type=1 and username is not null and username<>'' GROUP BY username";
        return $this->query($sql);
    }


    public function update_web_pv_ip_user($ymd){
        $sql = "UPDATE `{$this->_realTableName}{$ymd}` as a LEFT JOIN pc_members as b on a.uid=b.uid set a.username=b.username;";
        return $this->query($sql);
    }

}

<?php
namespace Dao\Udashi_admin\Stat;
use \Dao\Udashi_admin;
class Editorial extends \Dao\Udashi_admin\Udashi_admin {

    /**
     * @return Editorial
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function selectByAll($select_params){
        $sql = "select SUM(pv) pvnum , SUM(ip) ipnum , SUM(article_num) articlenum , b.addressid from  (SELECT * from stat_web_pv_ip where {$select_params['where']}  ) a INNER JOIN stat_editorial b on a.username = b.username
GROUP BY addressid order BY {$select_params['orderby']}";
        return $this->query($sql);
    }

    public function selectByAllDetail($select_params){
        $sql = "select a.ymd,SUM(pv) pvnum , SUM(ip) ipnum , SUM(article_num) articlenum , b.addressid from  stat_web_pv_ip a INNER JOIN stat_editorial b on a.username = b.username
where {$select_params['where']}  GROUP BY addressid,a.ymd";
        return $this->query($sql);
    }


    public function selectByUser($select_params){
        $sql = "select b.username , SUM(pv) pvnum , SUM(ip) ipnum , SUM(article_num) articlenum , b.addressid from  stat_web_pv_ip a INNER JOIN stat_editorial b on a.username = b.username
where {$select_params['where']}  GROUP BY b.username order by {$select_params['orderby']}";
        return $this->query($sql);
    }


    public function selectByUserDetail($select_params){
        $sql = "select * from stat_web_pv_ip  where {$select_params['where']} ORDER by {$select_params['orderby']}";
        return $this->query($sql);
    }

}

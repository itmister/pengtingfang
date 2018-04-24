<?php
namespace Dao\Udashi_admin\Stat;
use \Dao\Udashi_admin;
class Haiyuan_data extends \Dao\Udashi_admin\Udashi_admin {
    /**
     * @return Channel_data
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    
    public function getlist($select_params){
        $sql = "SELECT 
sum(num1) as num1,
sum(num2) as num2,
sum(num3) as num3,
sum(num4) as num4,
sum(num5) as num5,
sum(num6) as num6,
sum(num7) as num7,
sum(num8) as num8,
sum(num9) as num9,
sum(num10) as num10
FROM (
SELECT 
(case when a.num=1 then count(a.guid) else 0 END) as num1,
(case when a.num=2 then count(a.guid) else 0 END) as num2,
(case when a.num=3 then count(a.guid) else 0 END) as num3,
(case when a.num=4 then count(a.guid) else 0 END) as num4,
(case when a.num=5 then count(a.guid) else 0 END) as num5,
(case when a.num=6 then count(a.guid) else 0 END) as num6,
(case when a.num=7 then count(a.guid) else 0 END) as num7,
(case when a.num=8 then count(a.guid) else 0 END) as num8,
(case when a.num=9 then count(a.guid) else 0 END) as num9,
(case when a.num>=10 then count(a.guid) else 0 END) as num10 FROM (
SELECT guid,sum(num) as num FROM stat_haiyuan_data where 1 {$select_params['where']} GROUP BY guid) as a GROUP BY a.guid ) as aa";
        return $this->query($sql)[0];
    }
    
    public function getdetaillist($select_params){
        $sql = "SELECT guid,sum(num) as num FROM stat_haiyuan_data where 1 {$select_params['where']}  GROUP BY guid HAVING {$select_params['having']} limit {$select_params['limit']}";
    
        return $this->query($sql);
    }
    public function getdetailcount($select_params){
        $sql = "SELECT COUNT(a.guid) count from (SELECT guid,sum(num) as num FROM stat_haiyuan_data where 1 {$select_params['where']}  GROUP BY guid HAVING {$select_params['having']}) a";
    
        return $this->query($sql)[0]['count'];
    }
}


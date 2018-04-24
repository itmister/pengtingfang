<?php
namespace Dao\Udashi_admin\Stat;
use \Dao\Udashi_admin;
class Haiyuanpc_data extends \Dao\Udashi_admin\Udashi_admin {
    /**
     * @return Haiyuanpc_data
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    
    public function getlist($select_params){
        $sql = "SELECT *  FROM stat_haiyuanpc_data where 1 {$select_params['where']} ";
        return $this->query($sql);
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


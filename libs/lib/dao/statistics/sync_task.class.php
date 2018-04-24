<?php
/**
 * 7654统计后台同步数据
 */
namespace Dao\Statistics;

class Sync_task extends Statistics{

    /**
     * @return Sync_task
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function query_call($ymd){
        $ret = $this->query("call software_centre_technician({$ymd})");
        $ret = $this->query("call software_centre_index({$ymd})");
        $ret = $this->query("call promotion_union_software({$ymd})");
        $ret = $this->query("call promotion_all_union({$ymd})");
        return $ret;
    }
}
<?php
/**
 * 统计-市场经理-
 */
namespace Dao\Stat;
use \Dao;

class Manager_info_detail extends Stat {

    /**
     * @return Manager_info_detail
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function sync_ymd($ymd_start, $ymd_end) {
    }

    public function sync_all() {
    }
}

<?php
namespace Union\Stat\Sync;
use Union\Stat\Sync\Performance_ymd;

/**
 * 统计- 同步市场经理下属技术员月业绩
 * Class Performance_ym
 * @package Union\Stat\Sync
 */

class Performance_ym extends Performance_ymd {

    public function ymd($ymd_start, $ymd_end) {

        parent::ymd( $ymd_start, $ymd_end );
        \Dao\Stat\Manager\Performance_ym_software::get_instance()->sync_ymd( $ymd_start, $ymd_end );
        \Dao\Stat\Manager\Performance_ym_pt::get_instance()->sync_ymd( $ymd_start, $ymd_end );
        \Dao\Stat\Manager\Director_performance_ym_software::get_instance()->sync_ymd( $ymd_start, $ymd_end );
        \Dao\Stat\Manager\Director_performance_ym_pt::get_instance()->sync_ymd( $ymd_start, $ymd_end );
        \Dao\Stat\Manager\Technician_performance_registerym::get_instance()->sync_all($ymd_start, $ymd_end );
        \Dao\Stat\Manager\Technician_ym_credit30::get_instance()->sync_ymd( $ymd_start, $ymd_end );
    }

    public function ym($ym) {
        parent::ym($ym);
    }

    public function uid($uid) {
        parent::uid( $uid );
    }

    public function all() {
        parent::all();
    }
}
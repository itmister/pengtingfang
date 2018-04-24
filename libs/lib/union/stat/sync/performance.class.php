<?php
namespace Union\Stat\Sync;
use Union\Stat\Sync\Performance_ym;
/**
 * 统计- 同步市场经理下属技术员月业绩
 * Class Performance
 * @package Union\Stat\Sync
 */

class Performance extends Performance_ym {

    public function ymd($ymd_start, $ymd_end) {
        parent::ymd( $ymd_start, $ymd_end );
        \Dao\Stat\Manager\Technician_performance::get_instance()->sync_ymd( $ymd_start, $ymd_end );
        \Dao\Stat\Manager\Technician_performance_info_complete::get_instance()->sync_ymd( $ymd_start, $ymd_end );
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
<?php
namespace Union\Stat\Sync;
use Union\Stat\Sync\Performance_ymd_software;

/**
 * 统计- 同步市场经理下属技术员日业绩
 * Class Performance_ymd
 * @package Union\Stat\Sync
 */

class Performance_ymd extends Performance_ymd_software {

    public function ymd($ymd_start, $ymd_end) {
        parent::ymd( $ymd_start, $ymd_end );
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
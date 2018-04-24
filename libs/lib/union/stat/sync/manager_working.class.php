<?php
namespace Union\Stat\Sync;
use Union\Stat\Sync\Manager;
/**
 * 统计- 同步市场经理作业
 * Class Manager_working
 * @package Union\Stat\Sync
 */

class Manager_working extends Manager {

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
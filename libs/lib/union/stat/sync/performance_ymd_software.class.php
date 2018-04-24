<?php
namespace Union\Stat\Sync;
use Union\Stat\Sync\Manager_technician;
/**
 * 统计- 同步市场经理下属技术员业绩日流水
 * Class Performance_ymd_software
 * @package Union\Stat\Sync
 */

class Performance_ymd_software extends Manager_technician {

    public function ymd($ymd_start, $ymd_end) {
        parent::ymd( $ymd_start, $ymd_end );
        \Dao\Stat\Manager\Performance_ymd_software::get_instance()->sync_ymd( $ymd_start, $ymd_end );
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
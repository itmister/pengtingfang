<?php
namespace Union\Stat\Sync;
use Union\Stat\Sync\Base;
/**
 * 统计- 同步市场经理
 * Class Manager
 * @package Union\Stat\Sync
 */

class Manager extends Base {

    public function ymd($ymd_start, $ymd_end) {
        parent::ymd( $ymd_start, $ymd_end );
        \Dao\Stat\Manager\Manager::get_instance()->sync_ymd( $ymd_start, $ymd_end );
    }

    public function ym($ym) {
        parent::ym($ym);
    }

    public function uid($uid) {
        parent::uid( $uid );
    }

    public function all() {
        parent::all();
        \Dao\Stat\Manager\Manager::get_instance()->sync_all();
    }
}
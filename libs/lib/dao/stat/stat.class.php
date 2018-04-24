<?php
namespace Dao\Stat;
use Dao\Dao;

class Stat extends Dao {
    protected $_connection_key = 'DB_STATNEW';
    protected $_prefix = '';

    /**
     * 全同步
     */
    public function sync_all() {}

    /**
     * 同步指定时间段
     * @param $ymd_start
     * @param $ymd_end
     */
    public function sync_ymd( $ymd_start, $ymd_end ) {}

    /**
     * 同步月
     * @param $ym
     */
    public function sync_ym( $ym ) {}

    /**
     * @param $ymd_start
     * @param $ymd_end
     */
    public function sync_ym_by_ymd( $ymd_start = 0, $ymd_end = 0 ) {
        $ym_start  = !empty($ymd_start) ? intval( date('Ym', strtotime( $ymd_start))) : 0;
        if (!empty($ym_start)) $this->sync_ym( $ym_start);
        $ym_end = !empty( $ymd_end ) ? intval ( date('Ym', strtotime( $ymd_end))) : 0;
        if (!empty( $ym_end) && $ym_end != $ym_start )  $this->sync_ym( $ym_end);
    }

    /**
     * 同步用户
     * @param $uid
     */
    public function sync_user( $uid ) {}
}
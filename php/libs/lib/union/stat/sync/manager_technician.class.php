<?php
namespace Union\Stat\Sync;
use Union\Stat\Sync\Manager_working;
/**
 * 统计- 同步市场经理下属技术员
 * Class Manager_technician
 * @package Union\Stat\Sync
 */

class Manager_technician extends Manager_working {

    public function ymd($ymd_start, $ymd_end) {
        parent::ymd( $ymd_start, $ymd_end );
        $dao_manager_technician = \Dao\Stat\Manager\Technician::get_instance();
        $dao_manager_technician->sync_ymd( $ymd_start, $ymd_end );
        $this->_sync_manager();
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

    /**
     * 同步市场经理下属技术员汇总信息
     */
    private function _sync_manager() {
        //下属技术员数量
        $sql = "
        INSERT into `manager_manager`(technician,manager_uid)
        select
            count(*) as technician,
            manager_uid
        from
          manager_technician
        GROUP BY
          manager_uid
        on DUPLICATE key update technician=values(technician);
        ";
        $this->query( $sql );

        //下属月业绩技术员数量
        $sql = "
        INSERT into `manager_manager`(technician_performance,manager_uid)
        select
            count(*) as technician_performance,
            manager_uid
        from
          manager_technician_performance
        GROUP BY
          manager_uid
        on DUPLICATE key update technician_performance=values(technician_performance);
        ";
        $this->query( $sql );

        //下属业绩且资料完整技术员数量
        $sql = "
        INSERT into `manager_manager`(technician_performance_info_complete,manager_uid)
        select
            count(*) as technician_performance_info_complete,
            manager_uid
        from
          manager_technician_performance_info_complete
        GROUP BY
          manager_uid
        on DUPLICATE key update technician_performance_info_complete=values(technician_performance_info_complete);
        ";
        $this->query( $sql );

    }
}
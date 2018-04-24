<?php
/**
 * 统计-市场经理-注册技术员当月推广大于等30元数量
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Technician_ym_credit30 extends Stat {
    /**
     * @return Technician_ym_credit30
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $table_name = $this->_get_table_name();
        $this->query("drop table if exists {$table_name}");
        $sql = "
            create table {$table_name} (
                `manager_uid` int(11) not null DEFAULT 0 comment '市场经理uid',
                `director_uid` int(11) not null default 0 comment '渠道主管uid',
                `credit30_count` int(11) not null default 0 comment '当月推广大于等30元数量',
                `ym` int(11) not null default 0 comment '年月',
                primary key(`manager_uid`, `ym`),
                key `director_uid` (`director_uid`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '市场经理-注册技术员月推广大于等30元数量';
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_ymd( $ymd_start, $ymd_end ) {
        $this->sync_all();
    }

    public function sync_all() {
        $table_name = $this->_get_table_name();
        $table_technician_performance_registerym = $this->_get_table_name('manager_technician_performance_registerym');
        $this->delete_all();
        $sql = "
            insert into {$table_name} (manager_uid, director_uid, credit30_count, ym)
            select
                manager_uid,
                director_uid,
                count(*) as credit30_count,
                ym
            from
                {$table_technician_performance_registerym}
            where
                credit >= 30000
            GROUP BY
              manager_uid,ym
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    /**
     * 取指定月业绩
     * @param $ym
     * @return mixed
     */
    public function get_list( $ym ) {
        $table_name = $this->_get_table_name();
        $sql = "
            select
                manager_uid,
                credit30_count
            from
                {$table_name}
            where ym='{$ym}'
        ";
        return $this->query( $sql );
    }
}
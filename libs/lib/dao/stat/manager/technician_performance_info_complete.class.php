<?php
/**
 * 统计-市场经理-有业绩技术员
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Technician_performance_info_complete extends Stat {
    /**
     * @return Technician_performance
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table( $drop_if_exists = false ) {
        if ( $drop_if_exists ) $this->query("drop table if exists `manager_technician_performance_info_complete`");
        $sql = "
            create table manager_technician_performance_info_complete(
                `director_uid` int(11) default 0 comment '渠道主管uid',
                `manager_uid` int(11) default 0 comment '市场主管uid',
                `uid` int(11) default 0 comment '技术员uid',
                PRIMARY key (`uid`),
                key `director_uid`(`director_uid`),
                key `manager_uid`(`manager_uid`)
            ) engine INNODB default charset utf8  comment '市场经理-有业绩技术员且资料完整技术员';
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_all() {
        $this->query( "truncate TABLE `manager_technician_performance_info_complete`;");
        $sql = "
            insert into `manager_technician_performance_info_complete`(director_uid,manager_uid,uid)
            select
                mtp.director_uid,
                mtp.manager_uid,
                mtp.uid
            FROM
                manager_technician_performance mtp
                INNER JOIN manager_technician mt on mtp.uid=mt.uid and mt.info_is_complete = 1;
        ";
        $this->exec( $sql );
        $this->sync_manager_technician_performance_ic_count();
    }

    public function sync_manager_technician_performance_ic_count() {
        $sql = "
            insert into `manager_manager` (manager_uid, technician_performance_info_complete)
            select
                manager_uid,
                count(*) as technician_count
            from
                manager_technician_performance_info_complete
            GROUP BY
                manager_uid
            on duplicate key update technician_performance_info_complete = values( technician_performance_info_complete )
        ";
        return $this->exec( $sql );
    }


    /**
     * 取市场经理下属有业绩且资料完整技术员数量
     * @return
    [
        [
            manager_uid : 市场经理uid,
            technician_performance_ic_count : 下属有业绩技术员数量
        ]
    ]
     */
    public function get_technician_performance_ic_count_list() {
        $sql = "
            select
                manager_uid,
                count(*) as technician_performance_ic_count
            from
                manager_technician_performance_info_complete
            GROUP BY
                manager_uid;
        ";
        return $this->query( $sql );
    }
}
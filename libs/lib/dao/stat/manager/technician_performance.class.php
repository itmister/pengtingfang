<?php
/**
 * 统计-市场经理-有业绩技术员
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Technician_performance extends Stat {
    /**
     * @return Technician_performance
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table( $drop_if_exists = false ) {
        if ( $drop_if_exists ) $this->query("drop table if exists `manager_technician_performance`");
        $sql = "
            create table manager_technician_performance(
                `director_uid` int(11) default 0 comment '渠道主管uid',
                `manager_uid` int(11) default 0 comment '市场主管uid',
                `uid` int(11) default 0 comment '技术员uid',
                `credit` int(11) default 0 comment '积分',
                `ip_count` int(11) default 0 comment '有效量',
                `ip_count_org` int(11) default 0 comment '厂商返回量',
                PRIMARY key (`uid`),
                key `director_uid`(`director_uid`),
                key `manager_uid`(`manager_uid`)
            ) engine INNODB default charset utf8  comment '市场经理-有业绩技术员';
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_all() {
        $this->delete_all();
        $sql = "
            insert into `manager_technician_performance`(director_uid,manager_uid,uid,credit,ip_count,ip_count_org)
            select
                director_uid,
                manager_uid,
                uid,
                sum(credit) as credit,
                sum(ip_count) as ip_count,
                sum(ip_count_org) as ip_count_org
            FROM
                manager_performance
            GROUP BY
                uid
        ";
        $this->exec($sql);
        $this->sync_manager_technician_performance_count();
    }

    public function sync_manager_technician_performance_count() {
        $sql = "
            insert into `manager_manager` (manager_uid, technician_performance)
            select
                manager_uid,
                count(*) as technician_count
            from
                manager_technician_performance
            GROUP BY
                manager_uid
            on duplicate key update technician_performance = values( technician_performance )
        ";
        return $this->exec( $sql );
    }

    /**
     * 取市场经理下属有业绩技术员数量
     * @return
    [
        [
            manager_uid : 市场经理uid,
            technician_performance_count : 下属有业绩技术员数量
        ]
    ]
     */
    public function get_technician_performance_count_list() {
        $sql = "
            select
                manager_uid,
                count(*) as technician_performance_count
            from
                manager_technician_performance
            GROUP BY
                manager_uid;
        ";
        return $this->query( $sql );
    }
}
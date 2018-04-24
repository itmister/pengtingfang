<?php
/**
 * 统计-市场经理-业绩
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Performance extends Stat {

    protected static $_instance = null;

    /**
     * @return Performance
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query( "drop table if exists `manager_performance`");
        $sql = "
            create table `manager_performance` (
                `c_id` int(11) not null DEFAULT  0 comment 'credit_wait_confirm id',
                `uid` int(11) not null default 0 comment '技术员uid',
                `manager_uid` int(11) not null DEFAULT 0 comment '市场经理uid',
                `director_uid` int(11) not null default 0 comment '渠道主管uid',
                `ymd` int(11) not null default 0 comment '业绩年月日',
                `ym` int(11) not null default 0 comment '业绩年月',
                `credit` int(11) not null DEFAULT 0 comment '积分',
                `ip_count` int(11) not null DEFAULT 0 comment '有效量',
                `ip_count_org` int(11) not null DEFAULT 0 comment '厂商返量',
                `promotion_type` int(11) not null DEFAULT 0 comment '推广类型，2：软件，3：导航',
                `soft_id` VARCHAR(40) not null DEFAULT '' comment '软件标识',
                PRIMARY key `c_id`(`c_id`),
                key `uid_ymd_soft_id` (`uid`,`ymd`,`soft_id`),
                key `uid_soft_ym` (`uid`,`soft_id`, `ym`),
                key `manager_soft_ym` (`manager_uid`, `soft_id`, `ym`),
                key `ymd` (`ymd`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '市场经理-业绩';
        ";
        $this->query( $sql );
        return $this->affected_rows();
//        return $this->affected_rows();
    }

    public function sync_ymd( $ymd_start, $ymd_end ) {
        $this->sync_all();
    }


    public function sync_all() {
        $this->query("truncate table `manager_performance`");
        return $this->_sync();
    }

    protected function _sync( $where = '') {
        $sql = "
            insert into `manager_performance` (c_id,uid, manager_uid, director_uid, ymd, ym, credit, ip_count, ip_count_org, promotion_type, soft_id)
            #explain
            select
                pb.c_id as c_id,
                pb.uid as uid,
                m.manager_uid as manager_uid,
                m.director_uid as director_uid,
                pb.ymd as ymd,
                pb.ym as ym,
                pb.credit,
                pb.ip_count,
                pb.ip_count_org as ip_count_org,
                pb.sub_type as promotion_type,
                pb.soft_id as soft_id
            FROM
                `stat`.`manager_manager` m
                INNER JOIN `stat`.`base_performance` pb on pb.puid=m.manager_uid and pb.ymd >= m.ymd_start and pb.ymd <= m.ymd_end and pb.type=2
        ";
        return $this->exec( $sql );
    }
}
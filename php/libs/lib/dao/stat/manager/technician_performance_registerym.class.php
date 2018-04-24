<?php
/**
 * 统计-市场经理平台-技术员注册当月-推广业绩汇总
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Technician_performance_registerym extends Stat {
    /**
     * @return Technician_performance_registerym
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->drop();
        $sql = "
            create table `manager_technician_performance_registerym` (
                `uid` int(11) not null default 0 comment '技术员uid',
                `manager_uid` int(11) not null DEFAULT 0 comment '市场经理uid',
                `director_uid` int(11) not null default 0 comment '渠道主管uid',
                `credit` int(11) not null DEFAULT 0 comment '积分',
                `ip_count` int(11) not null DEFAULT 0 comment '有效量',
                `ip_count_org` int(11) not null DEFAULT 0 comment '厂商返量',
                `reg_ymd` int(11) not null DEFAULT 0 comment '注册年月日',
                `ym` int(11) not null default 0 comment '注册年月',
                PRIMARY key (`uid`),
                key `manager_ym` (`manager_uid`, `ym`),
                key `manager_ymd` (`manager_uid`, `reg_ymd`),
                key (`credit`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '市场经理平台-技术员注册当月-推广业绩汇总';
        ";
        return $this->exec( $sql );
    }

    public function sync_ymd( $ymd_start, $ymd_end ) {
        $this->sync_all();
    }

    public function sync_all() {
       // $this->query("truncate table `manager_technician_performance_registerym`");
        $this->delete_all();
        $this->_sync();
    }

    protected function _sync( $arr_where = [] ) {
        $sql = "
            insert into manager_technician_performance_registerym(uid,manager_uid,director_uid,ym, reg_ymd, credit,ip_count,ip_count_org)
            select
                mt.uid,
                mt.manager_uid,
                mt.director_uid,
                mt.reg_ym,
                mt.reg_ymd,
                sum(mp.credit) as credit,
                sum(mp.ip_count) as ip_count,
                sum(mp.ip_count_org) as ip_count_org
            from
                manager_technician mt
                LEFT JOIN manager_performance mp on mt.uid=mp.uid and mt.reg_ym = mp.ym
            GROUP BY
                mt.uid
        ";
        $this->exec( $sql );
    }
}
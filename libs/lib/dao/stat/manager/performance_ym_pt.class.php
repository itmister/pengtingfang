<?php
/**
 * 统计-市场经理-月-推广类型业绩汇总
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Performance_ym_pt extends Stat {
    /**
     * @return Performance_ym_pt
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query( "drop table if exists `manager_performance_ym_pt`;");
        $sql = "
            create table `manager_performance_ym_pt` (
                `manager_uid` int(11) not null DEFAULT 0 comment '市场经理uid',
                `director_uid` int(11) not null default 0 comment '渠道主管uid',
                `ym` int(11) not null default 0 comment '业绩年月',
                `promotion_type` int(11) not null DEFAULT 0 comment '推广类型，2：软件，3：导航',
                `credit` int(11) not null DEFAULT 0 comment '积分',
                `ip_count` int(11) not null DEFAULT 0 comment '有效量',
                `ip_count_org` int(11) not null DEFAULT 0 comment '厂商返量',
                PRIMARY key (`manager_uid`,`ym`,`promotion_type`),
                key `director_uid` (`director_uid`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '市场经理-月-推广类型业绩汇总';
        ";
        $this->query( $sql );
        return $this->affected_rows();
//        return $this->affected_rows();
    }


    public function sync_ymd( $ymd_start, $ymd_end ) {
        $this->sync_all();//@todo
    }

    public function sync_all() {
        $this->query("truncate table `manager_performance_ym_pt`");
        $this->_sync();
    }

    protected function _sync( $arr_where = [] ) {
        $sql = "
            insert into `manager_performance_ym_pt` (manager_uid,director_uid, ym, credit, ip_count, ip_count_org, promotion_type)
            #explain
            select
                manager_uid,
                director_uid,
                ym,
                sum(credit) as credit,
                sum(ip_count) as ip_count,
                sum(ip_count_org) as ip_count_org,
                promotion_type
            FROM
                `manager_performance_ymd_software`
            GROUP BY
               manager_uid,ym,promotion_type
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
                *
            from
                {$table_name}
            where ym='{$ym}'
        ";
        return $this->query( $sql );
    }
}
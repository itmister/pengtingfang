<?php
/**
 * 统计-市场经理-日-推广类型业绩汇总
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Performance_ymd_pt extends Stat {
    /**
     * @return Performance_ymd_pt
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query( "drop TABLE if EXISTS `manager_performance_ymd_pt`;");
        $sql = "
            create table manager_performance_ymd_pt(
                `director_uid` int(11) not null default 0 comment '渠道主管uid',
                `manager_uid` int(11) not null default 0 comment '市场经理uid',
                `ymd` int(11) not null default 0 comment '年月日',
                `promotion_type` int(11) not null default 0 comment '年月日',
                `credit` int(11) not null default 0 comment '积分',
                `ip_count` int(11) not null default 0 comment 'ip_count',
                `ip_count_org` int(11) not null default 0 comment 'ip_count_org',
                PRIMARY key (`manager_uid`,`ymd`, `promotion_type`),
                key director_uid (`director_uid`)
            ) ENGINE=INNODB default charset utf8 comment '市场经理-日-推广类型业绩汇总';
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_all() {
        $this->delete_all('manager_performance_ymd_pt');
        $sql = "
            replace into `manager_performance_ymd_pt` (director_uid,manager_uid,ymd,promotion_type, credit, ip_count, ip_count_org)
            select
                director_uid,
                manager_uid,
                ymd,
                promotion_type,
                sum(credit),
                sum(ip_count),
                sum(ip_count_org)
            FROM
                `manager_performance`
            GROUP BY
                manager_uid,ymd,promotion_type
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    /**
     * 取指定月业绩
     * @param $ym
     * @return mixed
     */
    public function get_list( $ymd ) {
        $table_name = $this->_get_table_name();
        $sql = "
            select
                *
            from
                {$table_name}
            where ymd='{$ymd}'
        ";
        return $this->query( $sql );
    }
}
<?php
/**
 * 统计-市场经理-注册技术员当月推广大于等30元数量
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Director_ym_credit30 extends Stat {
    /**
     * @return Director_ym_credit30
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $table_name = $this->_get_table_name();
        $this->query("drop table if exists {$table_name}");
        $sql = "
            create table `manager_director_ym_credit30` (
                `director_uid` int(11) not null default 0 comment '渠道主管uid',
                `credit30_count` int(11) not null default 0 comment '当月推广大于等30元数量',
                `ym` int(11) not null default 0 comment '年月',
                primary key(`director_uid`, `ym`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '市场经理-渠道主管-注册技术员月推广大于等30元数量';
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_ymd( $ymd_start, $ymd_end ) {
        $this->sync_all();
    }

    public function sync_all() {
        $this->delete_all();
        $sql = "
            replace into `manager_director_ym_credit30` (director_uid, credit30_count, ym)
            select
                director_uid,
                sum(credit30_count) as credit30_count,
                ym
            from
                `manager_technician_ym_credit30`
            GROUP BY
              director_uid,ym
        ";
        $this->exec( $sql );

        //admin
        $sql_admin = "
            replace into `manager_director_ym_credit30` (director_uid, credit30_count, ym)
            select
                1,
                sum(credit30_count) as credit30_count,
                ym
            from
                `manager_technician_ym_credit30`
            GROUP BY
              ym
        ";
        return $this->exec( $sql_admin );
    }

    /**
     * 取指定月技术员当月推广大于等30元数量
     * @param $ym
     * @return mixed
     */
    public function get_credit30_count_by_director_uid($director_uid, $ym ) {
        $table_name = $this->_get_table_name();
        $sql = "
            select
                credit30_count
            from
                {$table_name}
            where ym='{$ym}' and director_uid = {$director_uid}
        ";
        $result = $this->query( $sql );
        return $result[0]['credit30_count'];
    }
}
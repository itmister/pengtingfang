<?php
/**
 * 统计-市场经理-渠道主管月业绩
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Director_performance_ym_software extends Stat {

    /**
     * @return Director_performance_ym_software
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query( "drop table if exists `manager_director_performance_ymd_software`;");
        $sql = "
            create table `manager_director_performance_ymd_software` (
                `director_uid` int(11) not null default 0 comment '渠道主管uid',
                `soft_id` VARCHAR(40) not null DEFAULT '' comment '软件标识',
                `ymd` int(11) not null default 0 comment '业绩年月',
                `credit` int(11) not null DEFAULT 0 comment '积分',
                `ip_count` int(11) not null DEFAULT 0 comment '有效量',
                `ip_count_org` int(11) not null DEFAULT 0 comment '厂商返量',
                `promotion_type` int(11) not null DEFAULT 0 comment '推广类型，2：软件，3：导航',
                PRIMARY key (`director_uid`,`soft_id`,`ymd`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '市场经理-渠道主管-日-软件业绩汇总';
        ";
        $this->exec( $sql );
//        return $this->affected_rows();
    }

    public function sync_ymd( $ymd_start, $ymd_end ) {
        $this->delete( "ymd between {$ymd_start} and {$ymd_end}");
        $this->_sync(['ymd_start' => $ymd_start, 'ymd_end' => $ymd_end ]);
    }

    public function sync_ym( $ym_start, $ym_end ) {
        $this->delete( "ym between {$ym_start} and {$ym_end}" );
        $this->_sync( ['ym_start' => $ym_start, 'ymd_end' => $ym_end ] );
    }

    public function sync_all() {
        $this->delete_all();
        $this->_sync();
    }

    protected function _sync( $arr_where = [] ) {

        $where = '';
        if ( isset( $arr_where['ym_start'] ) and isset( $arr_where['ym_end']) ) {
            $where = " WHERE ym between {$arr_where['ym_start']} and {$arr_where['ym_end']} ";
        }
        else if ( isset( $arr_where['ymd_start'] ) and isset( $arr_where['ymd_end']) ) {
            $where = " WHERE ymd between {$arr_where['ymd_start']} and {$arr_where['ymd_end']} ";
        }

        //渠道经理
        $sql = "
            insert into `manager_director_performance_ymd_software` (director_uid, ymd, credit, ip_count, ip_count_org, promotion_type, soft_id)
            #explain
            select
                director_uid,
                ymd,
                sum(credit) as credit,
                sum(ip_count) as ip_count,
                sum(ip_count_org) as ip_count_org,
                promotion_type,
                soft_id
            FROM
                `manager_performance`
            GROUP BY
               director_uid,soft_id,ymd
          {$where}
        ";
        $this->exec( $sql );

        //管理员admin
        $sql = "
            insert into `manager_director_performance_ymd_software` (director_uid, ymd, credit, ip_count, ip_count_org, promotion_type, soft_id)
            #explain
            select
                1,
                ymd,
                sum(credit) as credit,
                sum(ip_count) as ip_count,
                sum(ip_count_org) as ip_count_org,
                promotion_type,
                soft_id
            FROM
                `manager_performance`
            GROUP BY
               soft_id,ymd
          {$where}
        ";
        $this->exec( $sql );
    }
}
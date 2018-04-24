<?php
/**
 * 统计-市场经理-业绩-日流水
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Performance_ymd_software extends Stat {

    /**
     * @return Performance_ymd_software
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query( "drop table if exists `manager_performance_ymd_software`;");
        $sql = "
            create table `manager_performance_ymd_software` (
                `manager_uid` int(11) not null DEFAULT 0 comment '市场经理uid',
                `director_uid` int(11) not null default 0 comment '渠道主管uid',
                `ymd` int(11) not null default 0 comment '业绩年月日',
                `ym` int(11) not null default 0 comment '业绩年月',
                `credit` int(11) not null DEFAULT 0 comment '积分',
                `ip_count` int(11) not null DEFAULT 0 comment '有效量',
                `ip_count_org` int(11) not null DEFAULT 0 comment '厂商返量',
                `promotion_type` int(11) not null DEFAULT 0 comment '推广类型，2：软件，3：导航',
                `soft_id` VARCHAR(40) not null DEFAULT '' comment '软件标识',
                PRIMARY key (`manager_uid`,`ymd`,`soft_id`),
                key `manager_soft_ym` (`manager_uid`, `soft_id`, `ym`),
                key `ymd` (`ymd`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '市场经理-日业绩';
        ";
        $this->query( $sql );
        return $this->affected_rows();
//        return $this->affected_rows();
    }

    public function sync_all() {

        $this->delete_all();
        $this->_sync();

    }

    public function sync_ymd( $ymd_start, $ymd_end ) {
        $this->delete( "ymd between {$ymd_start} and {$ymd_end}" );
        $this->_sync( ['ymd_start' => $ymd_start, 'ymd_end' => $ymd_end ] );
    }


    public function get_list( $ymd_start, $ymd_end, $software_id_list ) {
        $soft_list = ( !empty($software_id_list) && is_array( $software_id_list) ) ? $soft_list = $this->_field_to_str($software_id_list) : '';
        $where_soft_list  = !empty($soft_list) ? "and mp.soft_id in({$soft_list})" : '';
        $sql = "
select
	mp.manager_uid,
	bu.user_name,
	bu.real_name,
	bu.phone,
	sum(mp.ip_count) as ip_count,
	soft_id
FROM
	manager_performance_ymd_software mp
	INNER JOIN base_user bu on mp.manager_uid = bu.uid and mp.ymd BETWEEN {$ymd_start} and {$ymd_end} {$where_soft_list}
GROUP BY
	mp.manager_uid,mp.soft_id
        ";
        $data_list = [];
        \Io::fb($sql);
        foreach ( $this->yield_result( $sql ) as $row ) {
            if (!isset($data_list[$row['manager_uid']])) $data_list[$row['manager_uid']] = [
                'manager_uid' => $row['manager_uid'],
                'user_name' => $row['user_name'],
                'real_name' => $row['real_name'],
                'phone' => $row['phone'],
                'total' => 0,
            ];
            $ip_count = intval( $row['ip_count']);
            $data_list[$row['manager_uid']][$row['soft_id']] = $ip_count;
            $data_list[$row['manager_uid']]['total'] += $ip_count;
        }

        return $data_list;
    }

    protected function _sync( $arr_where = [] ) {
        $where = '';
        if ( isset( $arr_where['ymd_start'] ) and isset( $arr_where['ymd_end']) ) {
            $where = " WHERE mp.ymd between {$arr_where['ymd_start']} and {$arr_where['ymd_end']} ";
        }
        $sql = "
            insert into `manager_performance_ymd_software` (manager_uid, director_uid, ymd, ym, credit, ip_count, ip_count_org, promotion_type, soft_id)
            #explain
            select
                m.manager_uid as manager_uid,
                m.director_uid as director_uid,
                mp.ymd as ymd,
                mp.ym as ym,
                sum(mp.credit) as credit,
                sum(mp.ip_count) as ip_count,
                sum(mp.ip_count_org) as ip_count_org,
                mp.promotion_type as promotion_type,
                mp.soft_id as soft_id
            FROM
                `stat`.`manager_manager` m
                INNER JOIN `stat`.`manager_performance` mp on mp.manager_uid=m.manager_uid
            {$where}
            GROUP  BY
                manager_uid, ymd, soft_id
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }
}
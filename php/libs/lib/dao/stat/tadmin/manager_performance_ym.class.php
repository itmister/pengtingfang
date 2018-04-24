<?php
/**
 * 统计-市场经理-月-业绩
 * 使用mongo存储
 * 按日
 */
namespace Dao\Stat\Tadmin;
use \Dao\Stat\Stat;
use \Dao\Stat\Base\Promotion;

class Manager_performance_ym extends Stat {

    /**
     * @return Manager_performance_ym
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        //@todo mongo
    }

    /**
     * 市场经理uid
     * @param $uid
     */
    public function sync_uid($uid) {
        $this->sync_all( $uid );
    }

    public function sync_ymd($ymd_start, $ymd_end ) {
        $ymd_start = intval( $ymd_start );
        $ymd_end = intval( $ymd_end );
        $dateline_start = strtotime( $ymd_start);
        $dateline_end = strtotime( $ymd_end);
        $ym_start = date('Ym', $dateline_start );
        $ym_end = date('Ym', $dateline_end);
        $this->sync_ym( $ym_start );
        if ($ym_start != $ym_end ) $this->sync_ym($ym_end );
    }

    public function sync_ym( $ym ) {
        $ym = intval( $ym );
        $mg_manager_performance_ym = \Mongo\Stat\Tadmin_manager_performance_ym::i();
        $mg_manager_performance_ym->delete(['ym' => $ym ]);
        $promotion_list   = [];
        foreach ( Promotion::get_instance()->get_list( Promotion::status_online ) as $row ) $promotion_list[$row['short_name']] = 0;

        //基本信息
        $sql_manager_list = "
            select
                    manager_uid,
                    mm.director_uid,
                    mc.province_name,
                    mc.city_name,
                    bu.user_name,
                    bu.real_name,
                    bu.remark,
                    bu.reg_ymd
            FROM
                    manager_manager mm
                    LEFT JOIN manager_city mc on mm.city_id=mc.city_id
                    LEFT JOIN base_user bu on mm.manager_uid = bu.uid
            WHERE
                    mm.type=4
        ";

        $manager_list = [];
        foreach ( $this->yield_result( $sql_manager_list ) as $row  ) {
            $row['ym']= $ym;
            $row['manager_uid'] = intval( $row['manager_uid']);
            $row['director_uid'] = intval( $row['director_uid']);
            $row['reg_ymd'] = intval( $row['reg_ymd']);
            $manager_list[ $row['manager_uid'] ]  = $row;
        }
        $sql = "
select
	manager_uid as manager_uid,
	soft_id,
	sum(ip_count) as ip_count
from
	manager_performance_ym_software
WHERE
	ym={$ym}
GROUP BY
	manager_uid,soft_id
        ";
        foreach ( $this->yield_result($sql) as $row ) if (isset($manager_list[$row['manager_uid']]))
            $manager_list[$row['manager_uid']][$row['soft_id']] = intval( $row['ip_count'] );

        $mg_manager_performance_ym->add_all( $manager_list );
    }

    public function sync_all() {
    }

    public function get_list( $ym,  $director_uid = 0, $manager_uid = 0) {
        return \Mongo\Stat\Tadmin_manager_performance_ym::i()->get_list( $ym, $director_uid, $manager_uid );
    }
}
<?php
/**
 * 统计-市场经理-日-业绩
 * 使用mongo存储
 * 按日
 */
namespace Dao\Stat\Tadmin;
use \Dao\Stat\Stat;
use \Dao\Stat\Base\Promotion;

class Manager_performance_ymd extends Stat {

    /**
     * @return Manager_performance_ymd
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
        $mg_manager_performance_ymd = \Mongo\Stat\Tadmin_manager_performance_ymd::i();


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
        ";
        $manager_list = [];
        foreach ( $this->yield_result( $sql_manager_list ) as $row  ) $manager_list[ $row['manager_uid'] ]  = $row;

        //业绩
        $field_init = array_merge( ['ip_count' => 0 , 'technician_credit30' => 0 ], $promotion_list );
        while ($dateline_start <= $dateline_end ) {
            $data = [];
            $ymd_now = intval( date('Ymd', $dateline_start));
            $ym_now = intval( date('Ym', $dateline_start));
            foreach ( $manager_list as $manager_info ) {
                $row = array_merge( $manager_info, $field_init );
                $row['manager_uid']        = intval( $row['manager_uid']);
                $row['director_uid']       = intval( $row['director_uid']);
                $row['ymd']                 = intval( $ymd_now);
                $row['ym']                  = intval( $ym_now );
                $data[ $manager_info['manager_uid'] . '_' . $ymd_now ] = $row;
            }

            $sql_performance = "
                select
                    manager_uid,
                    soft_id,
                    ip_count
                from
                    manager_performance_ymd_software
                WHERE
                    ymd = {$ymd_now}
            ";
            foreach ( $this->yield_result( $sql_performance ) as $row )
                $data[ $row['manager_uid'] . '_' . $ymd_now ][$row['soft_id']] = intval( $row['ip_count'] );

            //软件安装总量
            $sql_manager_promotion_type = "
                select
                    manager_uid,
                    ip_count
                from
                    manager_performance_ymd_pt
                WHERE
                    manager_uid > 0
                    and promotion_type=2
                    and ymd ={$ymd_now}
            ";
            foreach ( $this->yield_result( $sql_manager_promotion_type) as $row )
                $data[ $row['manager_uid'] . '_' .$ymd_now ]['ip_count'] = intval($row['ip_count']);

            //本月新增技术员≥30元的技术员人数
            $sql_credit30 = "
                select
                    manager_uid,
                    credit30_count
                from
                    technician_ymd_registerym_credit30
                where ymd={$ymd_now}
            ";
            foreach ( $this->yield_result( $sql_credit30) as $row ) $data[ $row['manager_uid'] . '_' .$ymd_now]['credit30_count'] = intval( $row['credit30_count']);
//            \Io\File::output('/app/www/cron/runtime/t.txt', var_export($data, true), false, FILE_APPEND);
            $mg_manager_performance_ymd->delete(['ymd' => $ymd_now ]);
            $mg_manager_performance_ymd->add_all( $data );
            $dateline_start += 86400;
        }
    }

    public function sync_ym( $ym ) {

    }

    public function sync_all() {
    }

    public function get_list( $director_uid = 0, $ym, $manager_uid ) {

        return \Mongo\Stat\Tadmin_manager_performance_ymd::i()->get_list( $director_uid, $ym, $manager_uid);
    }
}
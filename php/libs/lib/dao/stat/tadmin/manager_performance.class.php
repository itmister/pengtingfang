<?php
/**
 * 统计-市场经理-业绩排名
 * 使用mongo存储
 * 换月
 */
namespace Dao\Stat\Tadmin;
use \Dao\Stat\Stat;
use \Dao\Stat\Base\Promotion;

class Manager_performance extends Stat {

    /**
     * @return Manager_performance
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
        $this->sync_ym_by_ymd( $ymd_start, $ymd_end );
    }

    public function sync_ym( $ym ) {

        $ym               = intval( $ym );
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
//        \io::dead( $sql_manager_list );
        foreach ( $this->yield_result( $sql_manager_list ) as $row ){
            $row['ym'] = intval($ym);
            $row['manager_uid'] = intval($row['manager_uid']);
            $row['director_uid']  = intval( $row['director_uid'] );
            $row = array_merge( $row, $promotion_list);
            $manager_list[ $row['manager_uid'] ]  = $row;
        }

        //软件安装总量
        $sql_manager_promotion_type = "
            select
                manager_uid,
                ip_count
            from
                manager_performance_ym_pt
            WHERE
                manager_uid > 0
                and ym={$ym}
                and promotion_type=2
        ";
        foreach ( $this->yield_result( $sql_manager_promotion_type) as $row ) $manager_list[ $row['manager_uid']]['ip_count'] = intval($row['ip_count']);

        //本月新增技术员≥30元的技术员人数
        $sql_credit30 = "
            select
                manager_uid,
                credit30_count
            from
                manager_technician_ym_credit30
            where ym={$ym}
        ";
        foreach ( $this->yield_result( $sql_credit30) as $row ) $manager_list[ $row['manager_uid']]['credit30_count'] = intval( $row['credit30_count']);


        //软件安装
        $sql_software = "
            select
                manager_uid,
                soft_id,
                ip_count
            FROM
                manager_performance_ym_software
            WHERE
                ym={$ym}
        ";
        foreach ( $this->yield_result( $sql_software) as $row ) $manager_list[ $row['manager_uid']][$row['soft_id']] = intval( $row['ip_count']);

        $mg_manager_technician = \Mongo\Stat\Tadmin_manager_performance::i();
        $mg_manager_technician->delete(['ym' => $ym]);
        $mg_manager_technician->add_all( $manager_list );
    }

    public function sync_all() {
    }

    public function get_list( $director_uid = 0, $ym ) {
        return \Mongo\Stat\Tadmin_manager_performance::i()->get_list( $director_uid, $ym );
    }
    
    public function get_info($director_uid = 0, $ym, $manager_uid = -1, $sort = 'technician_credit30'){
    	return \Mongo\Stat\Tadmin_manager_performance::i()->get_list( $director_uid, $ym, $manager_uid, $sort );
    }
}
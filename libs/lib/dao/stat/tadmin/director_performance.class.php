<?php
/**
 * 统计-渠道主管-我的业绩
 * Mongo存储
 * 渠道主管各项业绩
 */
namespace Dao\Stat\Tadmin;
use \Dao\Stat\Stat;
use \Dao\Stat\Base\Promotion;

class Director_performance extends Stat {
    /**
     * @return Director_performance
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
    }

    /**
     * 市场经理uid
     * @param $uid
     */
    public function sync_uid($uid) {
    }

    public function sync_ymd($ymd_start, $ymd_end ) {
        $mg_director_performance = \Mongo\Stat\Tadmin_director_performance::i();
        $promotion_list   = [];
        foreach ( Promotion::get_instance()->get_list( Promotion::status_online ) as $row ) $promotion_list[$row['short_name']] = 0;

        $director_list = \Dao\Stat\Manager\Director::get_instance()->get_list();

        $dateline_start = strtotime($ymd_start);
        $dateline_end = strtotime( $ymd_end );
        if (empty($dateline_start)) return false;

        $data = [];
        while( $dateline_start <= $dateline_end ) {

            $ymd_now = intval( date('Ymd', $dateline_start) );
            $mg_director_performance->delete(['ymd' => $ymd_now]);
            foreach ( $director_list as $director_info )
                $data[$director_info['director_uid'] . '_' . $ymd_now ] = array_merge( $director_info, [ 'ymd' => $ymd_now ], $promotion_list );
            $dateline_start += 86400;
        }


        //admin
        $sql = "
            select director_uid,soft_id,ymd,ip_count from manager_director_performance_ymd_software where ymd BETWEEN {$ymd_start} and {$ymd_end}
        ";
        foreach ( $this->yield_result( $sql ) as $row ) {
            $ymd = intval( $row['ymd'] );
            $data[$row['director_uid'] . '_' . $ymd][ $row['soft_id']] = $row['ip_count'];
            $data['1_' . $ymd][ $row['soft_id'] ] += intval( $row['ip_count'] );//渠道主管
        }
        $mg_director_performance->add_all( $data );
        return true;
    }

    public function sync_ym( $ym = 0 ) {
        $this->sync_all($ym);
    }

    public function sync_all() {
    }

    public function get_list($director_uid, $ymd_start, $ymd_end){
		return \Mongo\Stat\Tadmin_director_performance::i()->get_list( $director_uid, $ymd_start, $ymd_end );
    }
}
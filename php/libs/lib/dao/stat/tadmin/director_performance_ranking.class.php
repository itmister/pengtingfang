<?php
/**
 * 统计-渠道主管-业绩排名
 * Mongo存储
 * 渠道主管月业绩排名
 */
namespace Dao\Stat\Tadmin;
use \Dao\Stat\Stat;
use \Dao\Stat\Base\Promotion;

class Director_performance_ranking extends Stat {
    /**
     * @return Director_performance_ranking
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
        $this->sync_ym_by_ymd( $ymd_start, $ymd_end);
    }

    public function sync_ym( $ym ) {
        $ym = intval( $ym );
        $mg_director_performance_rank = \Mongo\Stat\Tadmin_directory_performance_ranking::i();
        $mg_director_performance_rank->delete(['ym' =>  $ym ]);

        $promotion_list   = [];
        foreach ( Promotion::get_instance()->get_list( Promotion::status_online ) as $row ) $promotion_list[$row['short_name']] = 0;
        $director_list = \Dao\Stat\Manager\Director::get_instance()->get_list();
        $data = [];
        $init_field = ['ym' => $ym, 'ip_count' => 0, 'technician_credit30' => 0];
        foreach ( $director_list as $director_info ) $data["{$director_info['director_uid']}_{$ym}"] = array_merge( $director_info, $init_field, $promotion_list );

        $sql = "
            select director_uid,soft_id,ym,ip_count from manager_director_performance_ym_software where ym={$ym}
        ";
        foreach ( $this->yield_result( $sql ) as $row ) {
            $data[$row['director_uid'] . '_' . $row['ym']][ $row['soft_id']] = intval( $row['ip_count'] );
        }

        //月软件安装量
        foreach ( $this->yield_result( "select director_uid, ip_count from manager_director_performance_ym_pt where ym={$ym} and promotion_type=2") as $row )
            $data[ $row['director_uid'] . '_' . $ym]['ip_count'] = intval($row['ip_count']);

        //月新增30元技术员
        foreach ( $this->yield_result( "select director_uid, credit30_count from manager_director_ym_credit30 where ym={$ym}") as $row) {
            $data[ $row['director_uid'] . '_' . $ym]['technician_credit30'] = intval($row['credit30_count']);
        }

        $mg_director_performance_rank->add_all( $data );
        return true;
    }

    public function sync_all() {

    }

    public function get_list($ym,$sort){
        $ym = intval( $ym );
        return \Mongo\Stat\Tadmin_directory_performance_ranking::i()->get_list($ym,$sort);
    }
}
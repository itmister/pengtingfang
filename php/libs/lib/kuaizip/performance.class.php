<?php
namespace Kuaizip;

class Performance {

    //未上传
    const status_default    = 1;

    //已上传，待确认
    const status_uploaded   = 2;

    //已确认
    const status_confirmed  = 3;

    public function get_list( $ymd_start, $ymd_end, $status = null ) {
        return \Dao\Kuaizip\Performance::get_instance()->get_list( $ymd_start, $ymd_end, $status );
    }

    /**
     * 上传业绩
     * @param $ymd
     * @param $data
     */
    public function upload($ymd, $data) {
        $ymd = intval($ymd);
        if (empty($ymd) || empty($data)) return false;

        $_data = [];
        $ip_count_total = 0;

        $tn_list = [];
        foreach ($data as $row ) $tn_list[] = intval( $row['tn'] );

        $dao_user = \Dao\Kuaizip\User::get_instance();
        $user_list = [];
        foreach ( $dao_user->user_list_by_tn_list($tn_list) as $row ) $user_list[$row['uid']] = $row;
        $technician_count = 0;

        foreach ($data as $row){

            if (!isset($_data[$row['tn']])) {
                $_data[$row['tn']] = [
                    'tn' => $row['tn'],
                    'uid' => isset($user_list[$row['tn']]) ? $user_list[$row['tn']]['uid'] : 0,
                    'user_name' => isset($user_list[$row['tn']]) ? $user_list[$row['tn']]['user_name'] : '',
                    'ip_count' => 0,
                    'ymd' => $ymd
                ];
            }

            $_data[$row['tn']]['ip_count'] += intval($row['ip_count']);
            if (isset( $user_list[$row['tn']] ) ) $ip_count_total += intval( $row['ip_count'] );
        }
        foreach ( $_data as $row ) if ($row['ip_count'] > 0 && $row['uid'] > 0 ) $technician_count++;

        \Dao\Kuaizip\Performance::get_instance()->update('ymd=' . $ymd, [
            'technician_count' => $technician_count,
            'ip_count'          => $ip_count_total,
            'status'            => self::status_uploaded,
        ]);

        \Dao\Kuaizip\Performance_detail::get_instance()->delete('ymd=' . $ymd);
        \Dao\Kuaizip\Performance_detail::get_instance()->add_all( $_data );
        return true;
    }

    /**
     * @param $tn_list
     */
    public function get_uid_by_tns($tn_list) {
        $dao_user = \Dao\Kuaizip\User::get_instance();
        $result = [];
        foreach (  $dao_user->uid_available_check( $tn_list ) as $row ) {
            $result[$row['uid']] = $row['uid'];
        }
        return $result;
    }


    public function user_list_by_tn_list($tn_list) {
        $dao_user = \Dao\Kuaizip\User::get_instance();
        $result = [];
        foreach (  $dao_user->user_list_by_tn_list( $tn_list, 'uid,user_name' ) as $row ) {
            $result[$row['uid']] = $row;
        }
        return $result;

    }

    /**
     * 确认业绩->同步用户余额
     * @param $ymd
     */
    public function confirm( $ymd ) {
        $ymd = intval($ymd );
        $where = 'ymd=' . $ymd;
        $time_now = time();
        \Dao\Kuaizip\Performance::get_instance()->update( $where, [
            'status'            => self::status_confirmed,
            'dateline_confirm' => $time_now
        ]);

        \Dao\Kuaizip\Performance_detail::get_instance()->update( $where, [
            'status'            => self::status_confirmed,
            'dateline_confirm' => $time_now
        ]);

        //同步帐户余额
        $money = new \Kuaizip\Money();
        $performance_list  = \Dao\Kuaizip\Performance_detail::get_instance()->get_list( $ymd );
        foreach ($performance_list as $row ) {
            $money->performance_add( $row );
        }
        return true;
    }

    /**
     * 取业绩明细
     * @param $ymd
     */
    public function get_detail_list( $ymd, $uid = null ) {
        $data_list = \Dao\Kuaizip\Performance_detail::get_instance()->get_list( $ymd, $uid );
        return $data_list;
    }

    /**
     * 取用户推广记录
     * @param $uid
     * @param $ymd_start
     * @param $ymd_end
     */
    public function get_user_available_list( $uid, $ymd_start, $ymd_end ) {
        $data_list = \Dao\Kuaizip\Performance_detail::get_instance()->get_user_available_list($uid, $ymd_start, $ymd_end);
        foreach ($data_list as &$row ) {
            $row['money'] = $row['ip_count'] * 20;
        }
        return $data_list;
    }

    /**
     * 取业绩状态
     * @param $ymd
     */
    public function get_status( $ymd ) {
        $ymd = intval( $ymd );
        return \Dao\Kuaizip\Performance::get_instance()->get_row('ymd=' . $ymd )['status'];
    }

}
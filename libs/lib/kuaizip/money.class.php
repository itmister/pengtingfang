<?php
namespace Kuaizip;

class Money {


    public function sync_available( $uid ) {

        $uid = intval($uid);
        if (empty($uid)) return false;
        $dao_performance_detail  = \Dao\Kuaizip\Performance_detail::get_instance();

        //本月赚得收入次月15号后可提现,每一个有效安装0.2元
        $day            = intval( date('d') );
        $ymd_end        =  date('Ym31', strtotime( $day >= 15 ? '-1 month' : '-2 month' ) );
        $money          = $dao_performance_detail->get_ip_count( $uid, $ymd_end ) * 20;
        $money_total    = $dao_performance_detail->get_ip_count_total( $uid ) * 20;

        $where = 'uid=' . $uid;
        \Dao\Kuaizip\User::get_instance()->update( $where, [
            'money'         => $money,
            'money_total'  => $money_total
        ]);

        return true;
    }

    /**
     * 业绩发放
     */
    public function performance_add( $info ) {
        $ip_count = intval($info['ip_count']);
        $uid = intval( $info['uid']);
        if (empty($ip_count) || empty($uid) ) return false;
        $dao_money_log  = \Dao\Kuaizip\Money_log::get_instance();
        $dao_user = \Dao\Kuaizip\User::get_instance();
        $price  = \Config::get('kuaizip_ip_count_price', null, 20, 'kuaizip/config');
        $money = $ip_count * $price;
        $ret = $dao_money_log->performance_add($info['uid'], $info['user_name'], $money , $info['id'], $info['ymd']);
        $ret = $dao_user->set_inc("uid={$uid}", 'money', $money );
        $ret = $dao_user->set_inc("uid={$uid} ", 'money_total', $money );
        return true;
    }


    /**
     * 每月15号后将上个月业绩金额设置为可提现
     * @param $performance_info
     * @return bool
     */
    public function update($info) {

        try {
            $dao_money_log = \Dao\Kuaizip\Money_log::get_instance();
            $user = new \Kuaizip\User();
            $dao_user = \Dao\Kuaizip\User::get_instance();
            $dao_user->begin_transaction();

            $uid = $info['uid'];
            $user_info          = $user->info( $uid );
            $money              = intval( $info['money'] );

            $ret = $dao_user->set_inc("uid={$uid} and money_available=" . $user_info['money_available'], 'money_available', $money );
            if (!$ret) throw new \Exception('更新可用余额失败');

            $ret = $dao_money_log->update('id=' . $info['id'], ['status' => 1]);
            if (!$ret ) throw new \Exception('update_money_log_fail');
            $dao_user->commit();
        }
        catch (\Exception $e ) {
            $dao_user->rollback();
            return false;
//                throw new \Exception('withdraw_deny_fail', 20112);
        }
        return true;
    }
}
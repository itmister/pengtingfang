<?php
namespace Kuaizip;
use \Util\Datetime;
use \Dao\Kuaizip\Money_log;

class Withdraw {

    //未处理
    const status_default    = 1;

    //已支付
    const status_payed   = 2;

    //驳回
    const status_deny  = 3;

    protected $_cfg = [
        'apply_money_min' => 1000,//最低提现金额，单位分
    ];

    /**
     * 提现申请
     * @param $uid
     * @param $money
     * @param $alipay
     * @throws \Exception
     */
    public function apply( $uid, $money, $alipay ) {
        $uid                = intval( $uid );
        $money              = intval( $money );
        $dao_withdraw       = \Dao\Kuaizip\Withdraw::get_instance();
        $dao_money_log      = \Dao\Kuaizip\Money_log::get_instance();
        $dao_user           = \Dao\Kuaizip\User::get_instance();

        $user               = new User();
        $user_info          = $user->info( $uid );
        $apply_money_min    = $this->_cfg['apply_money_min'];

        if ( $money < $apply_money_min ) throw new \Exception('提现金额不能低于10元', 20101);
        if ( $money > $user_info['money_available'] ) throw new \Exception('可提现金额不足', 20102);

        $data = [
            'uid'            => $uid,
            'user_name'     => $user_info['user_name'],
            'money'          => $money,
            'alipay'        => $alipay,
        ];
        $data['datetime'] = Datetime::now();
        $data['ymd'] = Datetime::ymd_now();

        try {
            $dao_withdraw->begin_transaction();
            $id = $dao_withdraw->add($data);
            if ( !$id ) throw new \Exception('withdraw_insert_fail');

            $id = $dao_money_log->withdraw_apply($uid, $user_info['user_name'], -1 * $money, $id);
            if ( !$id ) throw new \Exception('withdraw_apply_money_log_insert_fail');

            $ret = $dao_user->set_inc("uid={$uid} and money=" . $user_info['money'], 'money', -1 * $money );
            if (!$ret) throw new \Exception('更新余额失败');

            $ret = $dao_user->set_inc("uid={$uid} and money_available=" . $user_info['money_available'], 'money_available', -1 * $money );
            if (!$ret) throw new \Exception('更新可用余额失败');

            $dao_withdraw->commit();
            return true;
        }
        catch ( \Exception $e ) {
            $dao_withdraw->rollback();
            throw new \Exception($e->getMessage(), 20110);
        }
    }

    /**
     * 支付
     * @param $id 提现记录id
     * @throws Exception
     * @return boolean
     */
    public function pay( $id ) {

        $dao_withdraw = \Dao\Kuaizip\Withdraw::get_instance();
        $info = $this->info( $id );
        if (empty($info)) throw new \Exception('withdraw_record_not_exist');
        if (self::status_default != $info['status'] ) throw new \Exception('withdraw_status_need_default');

        $arr_update = [
            'status' => self::status_payed,
            'deal_datetime' => date('Y-m-d H:i:s')
        ];
        $ret = $dao_withdraw->update("id={$id} and status=" . self::status_default, $arr_update );
        if (!$ret) throw new \Exception('withdraw_pay_fail');
        return true;

    }

    /**
     * 驳回
     * @param $id
     * @throws Exception
     * @return boolean
     */
    public function deny( $id ) {
        $dao_withdraw = \Dao\Kuaizip\Withdraw::get_instance();
        $info = $this->info( $id );
        if (empty($info)) throw new Exception('withdraw_record_not_exist');
        if (self::status_default != $info['status'] ) throw new \Exception('withdraw_status_need_default');

        $uid = $info['uid'];
        try {
            $dao_withdraw->begin_transaction();
            $user               = new User();
            $dao_user           = \Dao\Kuaizip\User::get_instance();
            $user_info          = $user->info( $uid );

            $money              = intval( $info['money'] );
            $ret = Money_log::get_instance()->withdraw_deny( $info['uid'], $info['user_name'], $money, $info['id']);
            if (!$ret) throw new \Exception('withdraw_deny_money_log_insert_fail');

            $arr_update = [
                'status' => self::status_deny,
                'deal_datetime' => date('Y-m-d H:i:s')
            ];
            $ret = $dao_withdraw->update("id={$id} and status=" . self::status_default, $arr_update );
            if (!$ret) throw new \Exception('withdraw_deny_fail');

            $ret = $dao_user->set_inc("uid={$uid} and money=" . $user_info['money'], 'money', $money );
            if (!$ret) throw new \Exception('更新余额失败');

            $ret = $dao_user->set_inc("uid={$uid} and money_available=" . $user_info['money_available'], 'money_available', $money );
            if (!$ret) throw new \Exception('更新可用余额失败');

            $dao_withdraw->commit();
        }
        catch (\Exception $e ) {
            $dao_withdraw->rollback();
            throw new \Exception('withdraw_deny_fail', 20112);
        }


        return true;
    }

    /**
     * 提现记录信息
     * @param $id
     * @return array
     */
    public function info( $id ) {
        $id = intval( $id );
        $info = \Dao\Kuaizip\Withdraw::get_instance()->get_row('where id=' . $id);
        return $info;
    }

}
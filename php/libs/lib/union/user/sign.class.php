<?php
/**
 * Created by vl
 * Description : 签到
 * Date: 2015/12/17
 * Time: 15:22
 */
namespace union\user;

class sign extends \Core\Object {

    /**
     * @param array $option
     * @return sign
     */
    public static function i( $option = [] ) { return parent::i($option); }

    /**
     * 执行签到
     * @param $uid
     * @param $user_name
     * @param integer $type 类型,1:官网,2:微信
     * @return boolean
     */
    public function exe( $uid, $user_name = '', $type = 1, $sign_credit ,$sign_days) {

        $dateline = strtotime(date('Y-m-d'));
        $data = array(
            'uid'           => $uid,
            'type'          => $type,
            'dateline'      => $dateline,
            'credit'        => $sign_credit,
            'sign'          => $sign_days,
        );

//        $sign_credit = \Config::get('SIGN_CREDIT');
//        if ( empty($sign_credit) ) $sign_credit = 6;
        $dao_sign_log = \Dao\Union\Sign_Log::get_instance();
        $dao_sign_log->begin_transaction();
        try {
            if ( !$dao_sign_log->add($data) ) throw new \Exception('你今天已经签到过了');
            $obj_user = \union\user\user::i();
            $obj_user->credit_add($uid, $sign_credit, 1, 1, 'sign', 1);
            //积分日结 签到积分时时发放到用户账户可用积分 caolei 2014-06-14
            $ym_sign = date('ym', $dateline);
            $ymd_sign = date('Ymd', $dateline);
            $sign_arr = \Dao\Union\Credit_wait_confirm::get_instance()->get_row("uid={$uid} and ym={$ym_sign} and ymd={$ymd_sign} and is_get=0 and name='sign'");
            if (empty($sign_arr)) throw new \Exception("credit_wait_confirm找不到记录");
            $dao_sign_log->commit();

            $mongo_log = array(
                'uid' => $uid,
                'name' => $user_name,
                'code'=> 30001,
                'softID'=>'sign',
                'ymd' => date('Ymd', $dateline),
                'credit' =>$sign_credit
            );
            \Mongo\Union\User_check::get_instance()->add( $mongo_log );
            return true;
        }
        catch ( \Exception $e ) {
            $dao_sign_log->rollback();
            \Util\Debug::log(func_get_args(), "签到异常:{$e->getCode()}\t{$e->getMessage()}");
            return false;
        }
    }

    /**
     * 是否已经签到
     * @param $uid 用户uid
     * @param $ymd 年月日
     * @return boolean
     */
    public function is_singed( $uid, $ymd = 0 ) {
        $dateline = empty($ymd)  ?  strtotime( date('Ymd 00:00:00')) :  strtotime($ymd);
        return !empty(  \Dao\Union\Sign_Log::get_instance()->get_row(['uid'=> $uid, 'dateline' => $dateline], 'uid') );
    }
}
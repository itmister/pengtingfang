<?php
/**
 * 微信快压红包
 * Created by vl
 * Description :
 * Date: 2016/3/3
 * Time: 21:04
 */
namespace Union\Weixin;
class Act_weixin_kzip {

    protected $_config = [
//        'datetime_start'    => '2016-08-01 00:00:00',//活动开始时间
//        'datetime_end'      => '2016-08-28 23:59:59',//活动结束时间
        'money_min'         => 100,//领取的红包最小值,单位分
        'money_max'         => 200,//领取的红包最大值,单位分
    ];
    /**
     * 领取红包，返回领取的红包金额，0 即获取不到 单位分
     * @param $user_info
     * @param int $get_type
     * @return int
     * @throws \Exception
     */
    public function get( $user_info, $get_type = 1) {
        if (empty($user_info)) throw new \Exception('用户信息有误');

        $uid                = $user_info['uid'];
        $weixin_open_id     = $user_info['weixin_open_id'];

        $money_min          = intval( $this->_config['money_min'] );
        $money_max          = intval( $this->_config['money_max'] );
        $money_get          = mt_rand( $money_min, $money_max );//金额，单位分

        $act_weixin_kzip     = \Dao\Union\Act_weixin_kzip::get_instance();
        try {
            $act_weixin_kzip->begin_transaction();
            $info = \Dao\Union\User_weixin_hongbao_config::get_instance()->query(
                "select * from user_weixin_hongbao_config where uid={$uid} and `status`=0 AND `name`= '快压红包'limit 1;"
            );

            if (empty($info[0])){
                throw new \Exception('您没有红包了额！', 200003);
            }
            if($info[0]['type']==2 || $info[0]['type']==4){
                $money_get = mt_rand(500,600);
            }
            $ret = $act_weixin_kzip->get( $uid, $money_get, $get_type );

            if ( empty($ret) ) throw new \Exception('抢红包失败，请稍候再试');
            $t = date("Y-m-d H:i:s");
            $ret2 = \Dao\Union\User_weixin_hongbao_config::get_instance()->update(array("id"=>$info[0]['id'],"status"=>0),array('status'=>1,'datetime'=>$t));
            if ( empty($ret2) ) throw new \Exception('抢红包失败，请稍候再试');

            if ( $money_get >  0 ) {
                //调用微信api发送红包
                $weixin_hongbao = new \Union\Weixin\Hongbao();
                $weixin_hongbao->send([
                    're_openid'     => $weixin_open_id,//接收红包的微信用户openid
                    'total_amount'  => $money_get,
                    'total_num'     => 1,
                ]);
            }
            $act_weixin_kzip->commit();
        } catch (\Exception $e ) {
            $act_weixin_kzip->rollback();
            throw new \Exception( $e->getMessage(), $e->getCode() );
        }
        return $ret;
    }
}
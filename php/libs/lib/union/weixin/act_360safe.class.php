<?php
/**
 * 360safe红包
 * Created by vl
 * Description :
 * Date: 2016/3/3
 * Time: 21:04
 */
namespace Union\Weixin;
class Act_360safe {

    protected $_config = [
//        'datetime_start'    => '2016-06-01 00:00:00',//活动开始时间
//        'datetime_end'      => '2016-06-28 23:59:59',//活动结束时间
        //'uid_list'          => [8400, 8, 19, 1309],//可以领取到红包的uid列表，构造函数会覆盖
        'money_min'         => 100,//领取的红包最小值,单位分
        'money_max'         => 300,//领取的红包最大值,单位分
    ];

    public function __construct() {
        //$this->_config['uid_list'] = \Config::get('weixin_hongbao_uid', null, [], 'weixin');
    }
    /**
     * 用户关注
     * @param \Util\Weixin_sdk\Response $weixin_response
     * @return mixed
     */
    public function event_user_subscribe( $weixin_response ) {

        $items = [
            [
                'title'            => '超大福利丨360卫士红包雨周周领！！',
                'description'     => '废话不多说，赶紧戳我领红包，就酱紫！！！',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz/k6dHuLOhvAzibsIGK5ibHSMBHoofJp9fjOykrTsdjs8nJ3Tu4TKuruLQdnmGz5ooS7tRme0jke1ZLhTzicZqcPLZg/0?wx_fmt=jpeg',
                'url'              => 'https://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=502928611&idx=1&sn=d675bc7069df46336fd3fcc402e8cb76&scene=0&previewkey=3OmZ%2B7bOFY3uGLCdOss2%2BcNS9bJajjJKzz%2F0By7ITJA%3D&pass_ticket=F2bImjUK1HXNlkfsKb08fI22ROtGh42wLfQsRJfnRnxv1YKckKR0KGBBvuvV09gz',
            ]
        ];

        return $weixin_response->image_text( $items );

    }
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

        //$uid_list           = $this->_config['uid_list'];
        $money_min          = intval( $this->_config['money_min'] );
        $money_max          = intval( $this->_config['money_max'] );
        $money_get          = mt_rand( $money_min, $money_max );//金额，单位分

        $safe_hongbao = \Dao\Union\Act_360safe_hongbao::get_instance();
        try {
            $safe_hongbao->begin_transaction();
            $ret = $safe_hongbao->get( $uid, $money_get, $get_type );
            if ( empty($ret) ) throw new \Exception('领取红包失败，请稍候再试');
            $t = date("Y-m-d H:i:s");
            $ret1 = \Dao\Union\User_weixin_hongbao_log::get_instance()->add(
                array(
                    'uid'=>$uid ,
                    'name'=>'360安全卫士红包',
                    'get_type'=>$get_type,
                    'money'=>$money_get,
                    'datetime_get'=>$t,
                )
            );
            if ( empty($ret1) ) throw new \Exception('领取红包失败，请稍候再试');
            if ( $money_get >  0 ) {
                //return true;
                //调用微信api发送红包
                $weixin_hongbao = new \Union\Weixin\Hongbao();
                $weixin_hongbao->send([
                    're_openid'     => $weixin_open_id,//接收红包的微信用户openid
                    'total_amount'  => $money_get,
                    'total_num'     => 1,
                ]);
            }
            $safe_hongbao->commit();
        } catch (\Exception $e ) {
            $safe_hongbao->rollback();
            throw new \Exception( $e->getMessage(), $e->getCode() );
        }
        return $ret;
    }


    /**
     * 返回用户红包领取信息
     * @param $uid
     */
    public function info($uid) {
        $safe_hongbao = \Dao\Union\Act_360safe_hongbao::get_instance();
        return $safe_hongbao->info( $uid );
    }
}
<?php
/**
 * 微信开门红包
 * Created by vl
 * Description :
 * Date: 2016/3/3
 * Time: 21:04
 */
namespace Union\Weixin;
class Act_qhongbao {

    protected $_config = [
        'datetime_start'    => '2016-04-21 00:00:00',//活动开始时间
        'datetime_end'      => '2016-06-12 23:59:59',//活动结束时间
        //'uid_list'          => [8400, 8, 19, 1309],//可以领取到红包的uid列表，构造函数会覆盖
        'money_min'         => 100,//领取的红包最小值,单位分
        'money_max'         => 500,//领取的红包最大值,单位分
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
        $time_now = time();
        if ( $time_now > strtotime( $this->_config['datetime_end']) ) {
            //活动结束
            return '';
        }

//        if (  $time_now < strtotime( $this->_config['datetime_start'])  ) {
//            //活动未开始，发送预告信息
//            $items = [
//                [
//                    'title'            => '7654现金红包，三月来袭 ',
//                    'description'     => '联盟首创真正的现金红包，即将来袭',
//                    'picture'          => 'https://mmbiz.qlogo.cn/mmbiz/k6dHuLOhvAwFUOnKoYTK6jPKvuOy7C8NPkSmNy2zIqTuDPFump70Gbia6Pf3KTC6WHfDxWP2aB0ic5wScicZficgDQ/0?wx_fmt=png',
//                    'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=402564395&idx=1&sn=6eaa76d5fddbf5d791d286e800c9b901&scene=0&previewkey=yK%2F9ma5m%2F0bDjBTVfNYsbsNS9bJajjJKzz%2F0By7ITJA%3D#wechat_redirect',
//                ]
//            ];
//            $ext = \Config::get( 'weixin_image_text_common', null, [], 'weixin' );
//            $items = array_merge( $items, $ext );
//            return $weixin_response->image_text( $items );
//        }

        $items = [
            [
                'title'            => '超大福利丨QQ管家，2000积分还能周周领红包！！',
                'description'     => '还在为5.1小长假出门而心疼油钱么？还在为长途旅行买高铁票而纠结么？请看这里！！',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz/k6dHuLOhvAzmUS9PCnuh5iaM4W8hCtOAaepgaEYAoibl7XMPkRobkhgtsgmQpSDJ4ILJCUT26cvuFAzDhsrw8V4A/0?wx_fmt=png',
                'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=502928579&idx=1&sn=cfb10ca9884f13476fe3aeeb7d5b71cf&scene=0&previewkey=3OmZ%2B7bOFY3uGLCdOss2%2BcNS9bJajjJKzz%2F0By7ITJA%3D#wechat_redirect',
            ]
        ];

//        $ext = \Config::get( 'weixin_image_text_common', null, [], 'weixin' );
//        $items = array_merge( $items, $ext );
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

        $time_now           = time();
        $time_start         = strtotime( $this->_config['datetime_start'] );
        $time_end           = strtotime( $this->_config['datetime_end'] );

        if ( $time_now < $time_start ) throw new \Exception('活动还没开始!', 200001);
        if ( $time_now > $time_end ) throw new \Exception('活动已结束！', 200002);

        //$uid_list           = $this->_config['uid_list'];
        $money_min          = intval( $this->_config['money_min'] );
        $money_max          = intval( $this->_config['money_max'] );
        $money_get          = mt_rand( $money_min, $money_max );//金额，单位分
//        $money_get          = in_array( $uid, $uid_list ) ? 100 : 0;//金额，单位分

        $dao_weixin_qhongbao = \Dao\Union\Act_weixin_qhongbao::get_instance();
        //$info = $dao_weixin_qhongbao->info( $uid );
        //$q = \Dao\Union\User::get_instance();
        try {
            $dao_weixin_qhongbao->begin_transaction();
            $info = \Dao\Union\User_weixin_hongbao_config::get_instance()->query(
                "select * from user_weixin_hongbao_config where uid={$uid} and `status`=0 limit 1;"
            );
            if (empty($info[0])){
                throw new \Exception('您没有红包了额！', 200003);
            }
            if($info[0]['type']==1){
                $money_get = 100;
            }
            $ret = $dao_weixin_qhongbao->get( $uid, $money_get, $get_type );

            if ( empty($ret) ) throw new \Exception('抢红包失败，请稍候再试');
            $t = date("Y-m-d H:i:s");
            $ret1 = \Dao\Union\User_weixin_hongbao_log::get_instance()->add(
                array(
                  'uid'=>$uid ,
                  'name'=>$info[0]['name'],
                  'get_type'=>$get_type,
                  'money'=>$money_get,
                  'datetime_get'=>$t,
                )
            );

            if ( empty($ret1) ) throw new \Exception('抢红包失败，请稍候再试');

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
            $dao_weixin_qhongbao->commit();
        } catch (\Exception $e ) {
            $dao_weixin_qhongbao->rollback();
            throw new \Exception( $e->getMessage(), $e->getCode() );
        }
        return $ret;
    }


    /**
     * 返回用户红包领取信息
     * @param $uid
     */
    public function info($uid) {
        $dao_weixin_qhongbao = \Dao\Union\Act_weixin_qhongbao::get_instance();
        return $dao_weixin_qhongbao->info( $uid );
    }
}
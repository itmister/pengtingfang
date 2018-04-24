<?php
/**
 * 夺宝
 * Created by vl
 * Description :
 * Date: 2016/3/3
 * Time: 21:04
 */
namespace Union\Weixin;
class Act_duobao{

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
    /*
    *  md5签名，$array中务必包含 appSecret
    */
    public function sign($array){
        ksort($array);
        $string="";
        while (list($key, $val) = each($array)){
            $string = $string . $val ;
        }
        return md5($string);
    }
    /*
    *  签名验证,通过签名验证的才能认为是合法的请求
    */
    public function signVerify($appSecret,$array){
        $newarray=array();
        $newarray["secret"]=$appSecret;
        reset($array);
        while(list($key,$val) = each($array)){
            if($key != "sign" ){
                $newarray[$key]=$val;
            }

        }
        $sign=$this->sign($newarray);
        \Util\Debug::write_log($sign,'sign',date("Ymd")."_doubao.log");
        if($sign == $array["sign"]){
            return true;
        }
        return false;
    }

}
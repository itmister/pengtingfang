<?php
/**
 * 微信红包
 * Created by vl
 * Description :
 * Date: 2016/3/3
 * Time: 21:04
 */
namespace Union\Weixin;
class Hongbao {

    /**
     * 发送微信红包
     * @param array $option
     */
   public function send( $option = array() ) {
       $weixin_config   = \Config::get('weixin');
       $client_ip       = \Config::get('server_ip');//发起红包请求的服务器ip

       $arr_data = [
           'nonce_str' => substr(md5(time() . mt_rand(1, 100000)), 0, 31),//随机串,需要少于32个字符
           'sign'       => '',//签名
           'mch_billno' => '',//订单id
           'mch_id'     => '1288605501',//商户号
           'wxappid'    => $weixin_config['app_id'],
           'send_name'  => '7654技术员联盟',//商户名称

           're_openid' => '',//接收红包的微信用户openid
           'total_amount' => 0,
           'total_num' => 0,
           'wishing' => '恭喜发财，大吉大利',
           'client_ip' => $client_ip, //发送请求的服务器的ip
           //'act_name' => '开门红包',
           'act_name' => '快压红包',
           //'remark' => '开门红包',
           'remark' => '快压红包',
           'datetime_send' => date('Y-m-d H:i:s'),
           'result' => ''
       ];
       if (!empty($option) && is_array($option) ) foreach ($option as $key => $value ) {
           $arr_data[$key] = $value;
       }

       //插入记录取得订单id
       $dao_log_weixin_hongbao = \Dao\Union\Log_weixin_hongbao::get_instance();
       $order_id = $dao_log_weixin_hongbao->add($arr_data);
       if (empty($order_id)) throw new \Exception('红包记录失败');
       $arr_data['mch_billno'] = $arr_data['mch_id'] . date('Ymd') . \Util\Tool::fill_string( $order_id, '0', 10 );

       //签名
       $sign_key = ['mch_billno', 'mch_id', 'wxappid', 'send_name', 're_openid', 'total_amount', 'total_num', 'wishing', 'client_ip', 'act_name', 'remark', 'nonce_str'];
       sort($sign_key);
       $arr_sign = [];
       foreach ($sign_key as $key ) $arr_sign[$key] = "{$key}={$arr_data[$key]}";

       $sign_str = implode('&', $arr_sign)  . '&key=' . 'zhanmengyangling7654weixinfuwumy';
       //http_build_query的结果微信不认
       \Util\Debug::log( $arr_sign );
       \Util\Debug::log( $sign_str );

       $sign = strtoupper(md5( $sign_str ));
       $arr_data['sign'] = $sign;
$xml=<<<eot
<xml>
<sign><![CDATA[{$arr_data['sign']}]]></sign>
<mch_billno><![CDATA[{$arr_data['mch_billno']}]]></mch_billno>
<mch_id><![CDATA[{$arr_data['mch_id']}]]></mch_id>
<wxappid><![CDATA[{$arr_data['wxappid']}]]></wxappid>
<send_name><![CDATA[{$arr_data['send_name']}]]></send_name>
<re_openid><![CDATA[{$arr_data['re_openid']}]]></re_openid>
<total_amount><![CDATA[{$arr_data['total_amount']}]]></total_amount>
<total_num><![CDATA[{$arr_data['total_num']}]]></total_num>
<wishing><![CDATA[{$arr_data['wishing']}]]></wishing>
<client_ip><![CDATA[{$arr_data['client_ip']}]]></client_ip>
<act_name><![CDATA[{$arr_data['act_name']}]]></act_name>
<remark><![CDATA[{$arr_data['remark']}]]></remark>
<nonce_str><![CDATA[{$arr_data['nonce_str']}]]></nonce_str>
</xml>
eot;
       $result = $this->_send( $xml );
       if (!empty($result)) {
           $post_str = str_replace( ['<![CDATA[', ']]>'], ['', ''], $result );
           $arr = (array)simplexml_load_string( $post_str );
           if ( $arr['result_code'] != 'SUCCESS' ) {
               \Util\Debug::log( $arr );
               throw new \Exception('发送红包至微信服务器失败');
           }
       }
       else {
           throw new \Exception('发送红包至微信服务器失败');
       }

       return $dao_log_weixin_hongbao->update( ['id' => $order_id], ['result' => $result, 'mch_billno' => $arr_data['mch_billno'], 'sign' => $arr_data['sign']] );
   }

    /**
     *
     * @param string $xml_str
     */
    protected function _send( $xml_str ) {
        $api = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        \Util\Debug::log( $xml_str );
        $result = $this->_curl_post_ssl($api, $xml_str);
        return $result;
    }

    protected function _curl_post_ssl( $url, $vars, $second = 30,$aHeader = [] ) {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

        //以下两种方式需选择一种

        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT, PATH_LIB . '/conf/weixin/apiclient_cert.pem');
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY, PATH_LIB . '/conf/weixin/apiclient_key.pem' );

        //第二种方式，两个文件合成一个.pem文件
//        curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');

        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new \Exception("call faild, errorCode:$error\n");
            return false;
        }
    }
}
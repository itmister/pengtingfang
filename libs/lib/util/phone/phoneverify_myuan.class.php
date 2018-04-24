<?php
namespace Util\Phone;
class PhoneVerify_myuan extends Phone_Verify {
    /**
     * curl提交
     * @param $url
     * @param $post_data
     * @return mixed|string
     */
    public function c_post($url,$post_data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3); //建立连接超时
        curl_setopt($curl, CURLOPT_TIMEOUT, 300); //最大持续连接时间
        $result = curl_exec($curl);
        error_log(date("Y-m-d H:i:s",time())."\t"."请求地址:".$url."?".http_build_query($post_data)."返回结果:".str_replace(array("\r","\n"),'',$result)."\r\n",3,"data/log/sms_log.log");
        $error = curl_error($curl);
        if($result) {
            return $result;
        }
        error_log(date("Y-m-d H:i:s",time())."\t"."请求地址:".$url."?".http_build_query($post_data)."返回错误:".str_replace(array("\r","\n"),'',$error)."\r\n",3,"data/log/sms_log.log");
        return $error;
    }

    /**
     * 处理返回xml
     * @param $url
     * @param $post_data
     * @return bool|mixed
     */
    public function c_xml($url,$post_data){
        $xml  = $this->c_post($url,$post_data);
        $xmldata = @simplexml_load_string($xml);
        if(is_object($xmldata)&&!empty($xmldata)) {
            $arr_xml = json_decode(json_encode($xmldata),1);
            return $arr_xml;
        } else {
            echo $xml;
        }
        return false;
    }

    /**
     * @param $phone_number
     * @param $msg
     * @return bool
     */
    public function _send($phone_number, $msg) {
        $r_xml = $this->c_xml("http://118.178.117.163/myuan/sms",array_merge(array(
            'action'=>'send',
            'mobile'=>$phone_number,
            'content'=>$msg,
            'sendTime'=>'',
            'checkcontent'=>1,
            'taskName'=>'',
            'countnumber'=>1,
            'mobilenumber'=>1,
            'telephonenumber'=>1
        ),array(
            'userid'=>127,
            'account'=>'100004',
            'password'=>'AQ503gJN'
            //'password'=>'password123'
        )));
        if(is_array($r_xml)&&!empty($r_xml)) {
            if($r_xml['returnstatus']=='Success') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * curl提交
     * @param $url
     * @param $post_data
     * @return mixed|string
     */
    public function c_get($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300); //最大持续连接时间
        $result = curl_exec($curl);
        error_log(date("Y-m-d H:i:s",time())."\t"."请求地址:".$url."返回结果:".str_replace(array("\r","\n"),'',$result)."\r\n",3,"data/log/sms_log.log");
        $error = curl_error($curl);
        if($result) {
            return $result;
        }
        error_log(date("Y-m-d H:i:s",time())."\t"."请求地址:".$url."返回错误:".str_replace(array("\r","\n"),'',$error)."\r\n",3,"data/log/sms_log.log");
        return $error;
    }
}
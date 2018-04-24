<?php
namespace Util\Phone;
/**
 *
 */
class PhoneVerify_jianzhou extends Phone_Verify {
    /**
     * @param $phone_number
     * @param $msg
     * @return bool
     */
    public function _send($phone_number, $msg) {
        $msg = str_replace("【7654联盟网】","",$msg);
        $msg .= "【7654联盟网】";
        error_reporting(0);
        require_once('lib/nusoap.php');
        //echo '<h2>Hello</h2>';

        $client = new nusoap_client('http://www.jianzhou.sh.cn/JianzhouSMSWSServer/services/BusinessService?wsdl', true);
        $client->soap_defencoding = 'utf-8';
        $client->decode_utf8      = false;
        $client->xml_encoding     = 'utf-8';
        $err = $client->getError();
        if ($err) {
            //echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
        }
        $params = array(
            'account'=>'sdk_songheng',
            'password'=>'766990',
            'destmobile'=>$phone_number,
            'msgText'=>$msg
        );

        $result = $client->call('sendBatchMessage', $params, 'http://www.jianzhou.sh.cn/JianzhouSMSWSServer/services/BusinessService');
        error_log(date("Y-m-d H:i:s",time())."\t"."返回结果:".json_encode($result)."\r\n",3,"Uploads/log/".date("Ymd")."_sms_log.log");
        if ($client->fault) {
            //echo '<h2>Fault (This is expected)</h2><pre>'; print_r($result); echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                //echo '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                //echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
                if($result['sendBatchMessageReturn']>0){
                    return $result['sendBatchMessageReturn'];
                }else{
                    return false;
                }
            }
        }
        return false;

        //echo '<h2>Hello2</h2>';
    }
}
<?php
namespace Util;
/**
 *短信工具类
 */
use \Dao\Union\Sms_batch;
use \Dao\Union\Sms_queue;
class Sms {
    protected static $_instance = null;
    /**
     * @return \Util\Sms
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * 发送短信消息
     * @param array $m
     * @param string $message
     */
    public function sms( array $m,$message=''){
       $r_xml = $this->c_xml("http://118.178.117.163/myuan/sms",array_merge(array(
            'action'=>'send',
            'mobile'=>implode(',',$m),
            'content'=>$message,
            'sendTime'=>'',
            'checkcontent'=>1,
            'taskName'=>'None',
            'countnumber'=>count($m),
            'mobilenumber'=>count($m),
            'telephonenumber'=>1
        ),array(
            'userid'=>127,
            'account'=>'100004',
            'password'=>'AQ503gJN'
            //'password'=>'password123'
        )));
        if(is_array($r_xml)&&!empty($r_xml)) {
            if($r_xml['returnstatus']=='Success') {
                $data = array_unique($m);
                $rid = Sms_batch::get_instance()->add([
                        'type'=>2,
                        'content'=>$message,
                        'status'=>1,
                        'inputtime'=>time()
                ]);
                //拆分数组为每200条一份写入 array_chunk
                foreach(array_chunk($data,200) as $arr){
                    $queue = array();
                    foreach($arr as $v) {
                        $queue[] = array(
                            'batch_id'=>$rid,
                            'mobile'=>$v,
                            'type'=>2,
                            'status'=>1,
                            'inputtime'=>time()
                        );
                    }
                    Sms_queue::get_instance()->add_all($queue);
                }
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
    private function c_post($url,$post_data){
        $reponse = \Io\Http::post($url,$post_data);
        //@todo 短信日志目录的后续可以指定一个绝对路径目录
        if ($reponse){
            error_log(date("Y-m-d H:i:s")."\t"."请求地址:".\Io\Http::$url."返回结果:".str_replace(array("\r","\n"),'',$reponse)."\r\n",3,"uploads/sms_log.log");
            return $reponse;
        }
        error_log(date("Y-m-d H:i:s")."\t"."请求地址:".\Io\Http::$url."返回错误:".\Io\Http::$errro."\r\n",3,"uploads/sms_log.log");
        return false;
     }

    /**
     * 处理返回xml
     * @param $url
     * @param $post_data
     * @return bool|mixed
     */
    private function c_xml($url,$post_data){
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
}
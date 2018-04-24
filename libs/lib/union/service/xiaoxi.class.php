<?php
namespace Union\Service;
/**
 *独立消息处理类
 */
class XiaoXi {
	protected static $_instance = null;
	
	/**
	 * @return \Union\Service\XiaoXi
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
    public function sms($m=array(),$message=''){
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
            //'password'=>'password123'
            'password'=>'AQ503gJN'
        )));
        //$r_xml['returnstatus']='Success';
        if(is_array($r_xml)&&!empty($r_xml)) {
            if($r_xml['returnstatus']=='Success') {

        $data = array_unique($m);
        $rid = M('sms_batch')->add(array(
                'type'=>2,
                'content'=>$message,
                'status'=>1,
                'inputtime'=>time()
            ));
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
            $batch_sql = M('sms_queue')->addAll($queue);
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
     * 发送站内信
     * @param array $uids
     * @param string $message
     * @return bool
     */
    public function site_message($uids=array(),$message=''){
        $data = array_unique($uids);
        $rid = M('msg_batch')->add(array(
            'content'=>addslashes($message),
            'username'=>'message_module',
            'inputtime'=>time()
        ));
        //拆分数组为每200条一份写入 array_chunk
        foreach(array_chunk($data,200) as $arr){
            $queue = array();
            foreach($arr as $v) {
                $queue[] = array(
                    'batch_id'=>$rid,
                    'user_id'=>$v,
                    'from_user_id'=>0,
                    'is_read'=>0,
                    'inputtime'=>time()
                );
            }
            $batch_sql = M('msg_data')->addAll($queue);
        }
        return true;
    }







    /**
     * curl提交
     * @param $url
     * @param $post_data
     * @return mixed|string
     */
    private function c_post($url,$post_data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3); //建立连接超时
        curl_setopt($curl, CURLOPT_TIMEOUT, 300); //最大持续连接时间
        $result = curl_exec($curl);
        error_log(date("Y-m-d H:i:s",time())."\t"."请求地址:".$url."?".http_build_query($post_data)."返回结果:".str_replace(array("\r","\n"),'',$result)."\r\n",3,"uploads/xiaoxi_log.log");
        $error = curl_error($curl);
        if($result) {
            return $result;
        }
        error_log(date("Y-m-d H:i:s",time())."\t"."请求地址:".$url."?".http_build_query($post_data)."返回错误:".str_replace(array("\r","\n"),'',$error)."\r\n",3,"uploads/xiaoxi_log.log");
        return $error;
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

    /**
     * 批量写入数据库
     * @param $data
     * @param $t
     * @return string
     */
    private function insertmore($data,$t){
        $fieids = array();
        $vls = array();
        $m=0;
        foreach($data as $v){
            ++$m;
            $tmp = array();
            foreach($v as $x=>$y){
                if($m==1) $fieids[] = $x;
                $tmp[] = $y;
            }
            $vls[] = "'".implode("','",$tmp)."'";
        }
        $fieids = implode(',',str_replace("'","`",$fieids));
        $vls = "(".implode("),(",$vls).")";
        if(count($vls)>1) {
            $sql = "insert into $t ($fieids) values $vls";
        } else {
            $sql = "insert into $t ($fieids) value $vls";
        }
        return $sql;
    }
}
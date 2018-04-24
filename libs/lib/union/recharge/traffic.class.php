<?php
namespace Union\Recharge;

//$Traffic = new Traffic(1);
//echo "<pre>";
//print_r($Traffic->getBalance());die; //查询余额
//print_r($Traffic->getPackage(0));die; //获取流量包
//print_r($Traffic->phone_traffic(array('range'=>0,'mobile'=>'18679137929','package'=>20)));die; //单号码充流量
//print_r($Traffic->phone_charge(array('mobile'=>'18679137929','cardnum'=>1)));die; //单号码充话费

/**
 * 流量话费充值 - 月呈
 * @author pengtingfang
 *
 */
class Traffic{
    
    private $url_traffic = 'http://bcp.pro-group.cn/api/CallApi/Index'; //冲流量
    private $v_traffic   = '1.2'; //版本号-固定值
    
    private $url_charge  = 'http://bcp.pro-group.cn/PhoneCharge/CallApi/Index'; //充话费
    private $v_charge      = '1.0'; //版本号-固定值
    
    private $account ='KH0048'; //帐号 (签名)	代理商编号(非平台登入账号)
    private $key     = '2773cb4ea4b17acbfd9ba8645191eccd'; // key

    public $uid    ='';//用户ID
    public $source ='';//来源

    
    protected static $_instance = null;
    /**
     * @return \Union\Credit\Manager
     */
   
    
    
    public function __construct(){

      $this->log = \Dao\Union\Log_recharge::get_instance();
    }

    /**
     * 查询流量余额
     * @return array 余额信息
     */
   public function getBalance(){

      $params = array(
          'v'           => $this->v_traffic ,
          'action'      => 'getBalance' ,
          'account'     => $this->account ,
          'timestamp'   => date('YmdGis') ,
      );
     
      $formData = $this->getFormData($params);
      $response = $this->send($formData, $this->url_traffic);
      /* 记录日志 */
      $log = array(
          'uid' => $this->uid,
          'type'=>'查询',
          'source'=>$this->source,
          'url'=> $this->url_traffic,
          'request'=>serialize($formData),
          'response'=>serialize($response),
          'status'=>'/',
      );
      $this->log->add($log);
      return $response;
   }
   
   /**
    * 获取流量包定义
    * @param int $type 运营商参数0:不指定, 1:移动, 2:联通, 3:电信
    * @return array   curl request
    */
   public function getPackage($type=0){
       $params = [
            'action'    => 'getPackage',
            'v'         => $this->v_traffic ,
            'account'   => $this->account,
            'type'      => $type, 
            'timestamp' => date("YmdHis")
        ];
       $formData = $this->getFormData($params);
       return $this->send($formData, $this->url_traffic);
   }
   
   /**
    * 单号码充流量
    * @param arr $size 签名设置
    * @return array   curl request
    */
   public function phone_traffic($size){
       $params = [
           'action'    => 'charge',
           'v'         => $this->v_traffic,
           'range'     => $size['range'], //0 全国流量 1省内流量，不带改参数时默认为0
           'account'   => $this->account,
           'mobile'    => $size['mobile'], //手机号
           'package'   => $size['package'], //流量包大小(必须在getPackage返回流量包选择内)
           'orderno'   => @$size['orderNo']?:date('Ymd').strtoupper(uniqid()), //自定义订单号，1-32位字符
           'timestamp' => date("YmdHis")
       ];
       unset($params['range']);
       $formData = $this->getFormData($params);
       $response = $this->send($formData, $this->url_traffic);
      /* 记录日志 */
      $log = array(
          'uid' => $this->uid,
          'type'=>'流量',
          'source'=>$this->source,
          'url'=> $this->url_traffic,
          'request'=>json_encode($formData),
          'response'=>json_encode($response),
          'status'=>'/',
      );
      $this->log->add($log);
      if($response['Code']==0){ //充值成功
          return true;
      }else{
          return false;
      }
   }
   
   /**
    * 单号码充话费
    * @param arr $size 签名设置
    * @return array   curl request
    */
   public function phone_charge($size){
       $params = [
           'action'    => 'charge',
           'v'         => $this->v_charge,
           'account'   => $this->account,
           'mobile'    => $size['mobile'], //手机号
           'cardnum'   => $size['cardnum'], //充值金额
           'orderid'   => @$size['orderNo']?:date('Ymd').strtoupper(uniqid()), //自定义订单号，1-32位字符
           'timestamp' => date("YmdHis")
       ];
       $formData = $this->getFormData($params);
       $response = $this->send($formData, $this->url_charge);
       /* 记录日志 */
       $log = array(
           'uid' => $this->uid,
           'type'=>'话费',
           'source'=>$this->source,
           'url'=> $this->url_traffic,
           'request'=>serialize($formData),
           'response'=>serialize($response),
           'status'=>'/',
       );
       $this->log->add($log);
       if($response['Code']==0){ //充值成功
           return true;
       }else{
           return false;
       }
   }
   /**
    * 回调信息更新
    * @param array $params 回调数据
    * @return boolean
    */
   public function call_back($params){
       if(!$params)
           return false;
       /* 更新日志 */
       $log = array(
           'sta'=>$params['sta'],
       );
       $this->log->update(array('orderno'=>$params['orderid'],$log));
   }
   
    /**
     * 发送http请求
     * @param array $formData 参数
     * @param string $url 地址  
     */
    private function send($formData, $url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $formData);
        $return_str = curl_exec($curl);
        curl_close($curl);
        
        return json_decode(str_replace(array("\r","\n"),'',$return_str),'1');
    }
    /**
     * 处理组合参数
     * @param array $params 参数
     * @return 组合后的参数
     */
    private function getFormData($params){
       $formData = $params;
       unset($formData['action'],$formData['v']);
       ksort($formData);//参数升序
       $formData['key']    = $this->key;
       $params['sign'] = md5(http_build_query($formData));
       return $params;
    }
}

?>
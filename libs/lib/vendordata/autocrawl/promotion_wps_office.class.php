<?php
namespace VendorData\AutoCrawl;
class Promotion_wps_office extends Base {
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }
    public function __construct(){
        $this->_login_url = "http://union.wps.cn/dwr/call/plaincall/UserAction.isUser.dwr";
        $this->_cookiejar_file = $this->make_cookie_jar("wps");
        $this->_get_data_url = 'http://union.wps.cn/admin_ins.htm';
        $this->_login_params =[
            'callCount'=>1,
            'c0-scriptName'=>'UserAction',
            'c0-methodName'=>'isUser',
            'c0-id'=>0,
            'c0-param0'=>'string:7654',
            'c0-param1'=>"string:gaoxinHL016",
            'c0-param2'=>"boolean:true",
            'batchId'=>2
        ];
    }

    public function get_data($date = ''){
        $option = [
            'ip'=>true,
            'referer' => 'http://union.wps.cn/user.htm?method=user',
            'useragent'=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.73 Safari/537.36',
        ];
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $result = $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file,$option);
        $params = [
            "method"=>"check",
            'start'=>$date,
            'end'=>$date,
            'type'=>2,
            'flag'=>'JMrhe95Um0cNS7Hqh1vi5Q=='
         ];
        //第一页
        $reponse = $this->get_content($this->_get_data_url,$params,$this->_cookiejar_file,$option);
        $data = $this->fiter_data($reponse);
        return $data;
    }

    /**
     * 拿到表格数据
     * @param $data
     * @return array  [通道,有效报活]
     */
    public function fiter_data($data){
        $dom = new \DomDocument();
        $dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        $tr_list = $dom->getElementsByTagName("tr");
        for($i = 0; $i < $tr_list->length; $i++) {
            $tr = $tr_list->item($i);
            $td_list = $tr->getElementsByTagName("td");
            
            if ($td_list->length > 0 && trim($td_list->item(1)->nodeValue)  != "--" ){
                $temp = [];
                $org_id = trim($td_list->item(1)->nodeValue);//通道
                $org_id = substr( $org_id,strpos($org_id,'.') + 1 );//子渠道号
                $temp['org_id'] = trim($td_list->item(1)->nodeValue);
                $temp['count'] =intval(trim($td_list->item(3)->nodeValue));//有效报活
                $arr [] = $temp;
            }
        }
        return $arr;
    }
}
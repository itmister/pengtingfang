<?php
namespace VendorData\AutoCrawl;
class Promotion_sohutv extends Base {
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }
    public function __construct(){
        $this->_login_url = "http://coop.hd.sohu.com/login.ered?reqCode=login";
        $this->_cookiejar_file = $this->make_cookie_jar("sohutv");
        $this->_get_data_url = 'http://coop.hd.sohu.com/channelinfo.ered';
        $this->_login_params = [
            'account'=>'gaoxin2094',
            'password'=>'zhanmengadmin'
        ];
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        //sohu 识别浏览器
        $option['header'] = [
            'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36',
        ];
        $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file,$option);
        $params = [
            "reqCode"=>"listSubChannelByDay",
            "_"=>time(),
            "channel"=>"2094",
            "day"=>$date
        ];
        $reponse = $this->get_content($this->_get_data_url,$params,$this->_cookiejar_file);
        $data = $this->fiter_data($reponse);
        return $data;
    }

    /**
     * @param $data
     * @return array  [渠道编号,激活数]
     */
    public function fiter_data($data){
        $org_data = json_decode($data,true);
        $rows = $org_data['rows'];
        foreach($rows as $v){
            $temp = [];
            $temp['org_id'] =$v['longchannel'];//渠道编号
            $temp['count'] = $v['settle_instant_count'];//激活数
            $arr [] = $temp;
        }
        return $arr;
    }
}
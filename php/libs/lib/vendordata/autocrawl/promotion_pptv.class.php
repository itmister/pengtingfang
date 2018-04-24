<?php
namespace VendorData\AutoCrawl;
class Promotion_pptv extends Base {
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }
    public function __construct(){
        $this->_login_url = "http://union.pptv.com/login";
        $this->_cookiejar_file = $this->make_cookie_jar("pptv");
        $this->_get_data_url = 'http://union.pptv.com/subChannelQualityReport/1/100';
        $this->_login_params =[
            'username'=>'forqd1195',//'forqd2055',
            'password'=>'yinsuxitong'//'pptvyinsu'
        ];
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file);
        $params = [
                    "starttime"=>$date,
                    'endtime'=>$date,
                    'channelno'=>'',
                    'channelname'=>''
                ];
        //第一页
        $reponse = $this->post_content($this->_get_data_url,$params,$this->_cookiejar_file);
        $data = $this->fiter_data($reponse);
        $pages = $this->fiter_pages($reponse);
        //从2开始到后面（没有支持第一页不显示的页数）
        foreach ($pages as $page){
            $this->_get_data_url = sprintf('http://union.pptv.com/subChannelQualityReport/%d/100',$page);
            $reponse = $this->post_content($this->_get_data_url,$params,$this->_cookiejar_file);
            $data = array_merge((array)$data,(array)$this->fiter_data($reponse));
        }
        return $data;
    }

    /**
     * 获取分页数据
     * @param $data
     * @return array
     */
    public function fiter_pages($data){
        $pages = [];
        $dom = new \DomDocument();
        $dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        $page_note = $dom->getElementsByTagName("ul")->item(0)->nodeValue;
        preg_match_all('/共(\d*)页/',$page_note,$r);
        $page =   $r[1][0];
        if ($page > 1){
            return range(2,$page);
        }
        return [];
    }

    /**
     * 拿到表格数据
     * @param $data
     * @return array  [子渠道名,结算UV]
     */
    public function fiter_data($data){
        $dom = new \DomDocument();
        $dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        if ($dom->getElementsByTagName("tbody")->length >0){
            $tr_list = $dom->getElementsByTagName("tbody")->item(0)->getElementsByTagName('tr');
            for($i = 0; $i < $tr_list->length; $i++) {
                $tr = $tr_list->item($i);
                $td_list = $tr->getElementsByTagName("td");
                if ($td_list->length > 0){
                    $temp = [];
                    $temp['org_id'] =trim($td_list->item(4)->nodeValue);//子渠道名
                    $temp['count'] =intval(trim($td_list->item(5)->nodeValue));//结算UV
                    $arr [] = $temp;
                }
            }
            return $arr;
        }else{
            return false;
        }
    }
}
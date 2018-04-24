<?php
/**
 * 九天星辰诀
 */
namespace VendorData\AutoCrawl;
class Promotion_jxtcj extends Base {
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }
    public function __construct(){
        $this->_login_url = "http://tongji.602.com/index.php?r=site/login";
        $this->_cookiejar_file = $this->make_cookie_jar("jxtcj");
        $this->_get_data_url = 'http://tongji.602.com/index.php?r=Pay/PayList';
        $this->_login_params =[
            'LoginForm[username]'=>'7654cps',
            'LoginForm[password]'=>123456
        ];
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file);
        $params = [
            "startime"=>$date,
            "radio1"=>4,
            "endtime"=>$date,
            "uid"=>'',
            "subid"=>'',
            "g_id"=>133,
            "submit"=>1
            ];
        //第一页
        $reponse = $this->post_content($this->_get_data_url,$params,$this->_cookiejar_file);
        $data = $this->fiter_data($reponse);
        return $data;
    }

    /**
     * 拿到表格数据
     * @param $data
     * @return array  [子渠道,充值金额]
     */
    public function fiter_data($data){
        $dom = new \DomDocument();
        $dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        if ($dom->getElementsByTagName("table")->length >0){
            $tr_list = $dom->getElementsByTagName("table")->item(0)->getElementsByTagName('tr');
            for($i = 1; $i < $tr_list->length; $i++) {
                $tr = $tr_list->item($i);
                $td_list = $tr->getElementsByTagName("td");
                if ($td_list->length > 0){
                    $temp = [];
                    $temp['org_id'] =trim($td_list->item(8)->nodeValue);//子渠道
                    $temp['count'] =intval(trim($td_list->item(3)->nodeValue));//充值金额
                    $arr [] = $temp;
                }
            }
            return $arr;
        }else{
            return false;
        }
    }
}
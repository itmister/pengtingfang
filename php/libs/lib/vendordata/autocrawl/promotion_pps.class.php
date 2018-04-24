<?php
namespace VendorData\AutoCrawl;
class Promotion_pps extends Base {

    private $_captcha_url = "http://p.ppstream.com/check_coder";
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }
    public function __construct(){
        $this->_login_url = "http://p.ppstream.com/login";
        $this->_cookiejar_file = $this->make_cookie_jar("pps");
        $this->_get_data_url = 'http://p.ppstream.com/toIndex';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->_captcha_path = "c:\\pps_captcha_".time().".jpg";
        } else {
            $this->_captcha_path = "/tmp/pps_captcha_".time().".jpg";
        }
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        /**验证码识别**/
        $this->get_captcha($this->_captcha_url,$this->_cookiejar_file,$this->_captcha_path);
        $code = $this->identification_captcha($this->_captcha_path);
        if (!$code){
            return false;
        }
       // var_dump($code);
        $this->_login_params = [
            'check_code'=>$code,
            'loginName'=>'shanghaigaoxin',
            'loginPass'=>'zhanmengadmin'
        ];
        $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file);
        $params = [
            'fromDate'=>$date,
            'toDate'=>$date,
        ];
        //第一页
        $reponse = $this->post_content($this->_get_data_url,$params,$this->_cookiejar_file);
        $data = $this->fiter_data($reponse);
        return $data;
    }

    /**
     * @param $data
     * @return array  [日期	,CLT_ID,激活量]
     */
    public function fiter_data($data){
        $dom = new \DomDocument();
        $dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        if($dom->getElementsByTagName("tbody")->length >2){
            $tr_list = $dom->getElementsByTagName("tbody")->item(2)->getElementsByTagName('tr');
            for($i = 0; $i < $tr_list->length; $i++) {
                $tr = $tr_list->item($i);
                $td_list = $tr->getElementsByTagName("td");
                for($j = 0; $j < $td_list->length; $j++){
                    $arr[$i][] =trim($td_list->item($j)->nodeValue);
                }
            }
            return $arr;
        }else{
            return false;
        }
    }
}
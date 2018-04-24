<?php
namespace VendorData\AutoCrawl;
class Promotion_huorong extends Base {
    private $_captcha_url = "http://s.huorong.cn/cha/Index/loginVerify.shtml";
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }
    public function __construct(){
        $this->_login_url = "http://s.huorong.cn/cha/Index/index.shtml";
        $this->_cookiejar_file = $this->make_cookie_jar("hr");
        $this->_get_data_url = 'http://s.huorong.cn/cha/Trend/ChannelSubTrendSearch.shtml';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->_captcha_path = "c:\\hr_captcha_".time().".jpg";
        } else {
            $this->_captcha_path = "/tmp/hr_captcha_".time().".jpg";
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
        $this->_login_params = [
            'VerifyCode'=>$code,
            'password'=>'zhanmengadmin',
            'username'=>'shanghaizhanmeng'
        ];
        $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file);
        $params = [
            'chnl'=>'ALL',
            'dt'=>'',
            'tm_end'=>$date,
            'tm_start'=>$date
        ];
        //第一页
        $reponse = $this->post_content($this->_get_data_url,$params,$this->_cookiejar_file);
        $data = $this->fiter_data($reponse);
        return $data;
    }

    /**
     * @param $data
     * @return array  [子渠道,活跃,激活,锁首]
     */
    public function fiter_data($data){
        $data_list = json_decode($data,true);
        $arr = [];
        if($data_list){
            foreach ($data_list as $_data){
                if(!$_data['chnl_sub']){
                    continue;
                }
                $temp = [];
                $temp['org_id'] = $_data['chnl_sub'];
                $temp['count'] = $_data['active_cnt'];//有效访问量
                $arr [] = $temp;
            }
        }
        return $arr;
    }
}
<?php
/**
 *@author huxiaowei1238
 *金山导航
 */
namespace VendorData\AutoCrawl;
use \Util\Tool;

class Promotion_jsdh_auto extends Base{
    /**
     * 验证码请求地址
     */
    private $_captcha_url;
    private $vendor_data = [];
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        //初始化
        $this->_login_url = "http://union.ijinshan.com/?c=channels&a=login&chl=4011";
        $this->_get_data_url = 'http://union.ijinshan.com/?c=channels';

        //设置获取验证码地址、及存放地址
        $this->_captcha_url = "http://union.ijinshan.com/api.php?op=checkcode&code_len=4&font_size=24&width=130&height=55&font_color=&background=";
    }

    public function get_data($date,$org_id){
        //删除cookie 文件
        @unlink($this->_cookiejar_file);
        if ($this->_captcha_path){
            @unlink($this->_captcha_path);
        }
        $this->_cookiejar_file = $this->make_cookie_jar("jsdh_".$org_id);
        //获取验证码
        $code = $this->_get_code();
        if(!$code){
            echo "知码网，验证码识别失败\r\n";
//            \Dao\Union\Jsdh_auto_data::get_instance()->query(
//                "update jsdh_auto_data set status=2,note=CONCAT(note,',','知码网，验证码识别失败'),dateline=UNIX_TIMESTAMP() where soft_id='jsdh' and org_id='{$org_id}' and ymd={$date}"
//            );
            return false;
        }
        //登录参数
        $this->_login_params = [
            'dosubmit'              => 1,
            'mg_union[checkcode]'   => $code,
            'mg_union[password]'    => '123456',
            'mg_union[username]'    => $org_id,
        ];

        $login_result = $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file);
        $login_result  = json_decode($login_result,true);
        print_r($login_result);
        echo "\r\n";
        if($login_result[0] != 1){
            $err = $login_result['checkcode'];
            $err = str_replace(",",";",$err);
            echo $err."\r\n";
//            \Dao\Union\Jsdh_auto_data::get_instance()->query(
//                "update jsdh_auto_data set status=2,note=CONCAT(note,',','{$err}'),dateline=UNIX_TIMESTAMP() where soft_id='jsdh' and org_id='{$org_id}' and ymd={$date}"
//            );
            return false;
        }
        $date_start  = date("Y-m-d",strtotime("-6 days",strtotime($date)));
        $params = [
            'dosubmit'  => 1,
            'begin'     => $date_start,
            'end'       => $date,
        ];
        //第一页
        $reponse = $this->post_content($this->_get_data_url,$params,$this->_cookiejar_file);
        $data = $this->fiter_data($reponse,$org_id);
        print_r($data);
        echo "\r\n".$org_id."=>抓取成功\r\n";
        if(empty($data)){
            $data[0] = array(
                'org_id'=>$org_id,
                'ymd'=>$date,
                'num'=>0
            );
        }
        //删除cookie 文件
        @unlink($this->_cookiejar_file);
        if ($this->_captcha_path){
            @unlink($this->_captcha_path);
        }
        return $data;
    }

    /**
     * 拿到表格数据
     * @param $data
     * @return array [时间,有效访问量]
     */
    public function fiter_data($data,$org_id){
        $dom = new \DomDocument();
        @$dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        $tr_list = $dom->getElementsByTagName('tr');
        $arr = [];
        for($i = 3; $i < $tr_list->length; $i++){
            $tr = $tr_list->item($i);
            $td_list = $tr->getElementsByTagName("td");
            if ($td_list->length > 0 && ($i+1 != $tr_list->length)&&trim($td_list->item(0)->nodeValue)){
                $temp = [];
                $temp['org_id'] = $org_id;
                $temp['ymd'] = date("Ymd",strtotime(trim($td_list->item(0)->nodeValue)));
                $temp['num'] = trim($td_list->item(11)->nodeValue);//有效访问量
                $arr [] = $temp;
            }
        }
        return $arr;
    }
    
    /**
     * 获取验证码
     * @return boolean
     */
    protected function _get_code(){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
            $this->_captcha_path = "c:\\tmp\hr_captcha_".time().".jpg";
        }else{
            $this->_captcha_path = "/tmp/hr_captcha_".time().".jpg";
        }
        $this->_captcha_url .= "&0.".mt_rand(10000000, 99999999).mt_rand(10000000, 99999999);
        //识别验证码
        $this->get_captcha($this->_captcha_url,$this->_cookiejar_file,$this->_captcha_path);
        $code = $this->identification_captcha($this->_captcha_path);
        if (!$code){
            $i = 1;
            while ($i <= 3){
                $code = $this->identification_captcha($this->_captcha_path);
                if($code){
                    break;
                }
                $i++;
            }
            if(!$code){
                return false;
            }
        }
        return $code;
    }
}
<?php
/**
 *@author huxiaowei1238
 *搜狗导航
 */
namespace VendorData\AutoCrawl;
use \Util\Tool;

class Promotion_sgdh extends Base 
{
    /**
     * 验证码请求地址
     */
    private $_captcha_url;
    public static function get_instance()
    {
        return parent::get_instance(__CLASS__);
    }

    public function __construct()
    {
        //初始化
        $this->_login_url = "http://union.123.sogou.com/controller/login.php";
        $this->_cookiejar_file = $this->make_cookie_jar("sgdh");
        $this->_get_data_url = 'http://union.123.sogou.com/controller/xmlExport.php?startDate=%s&endDate=%s&act=subpidData';

        //设置获取验证码地址、及存放地址
        $this->_captcha_url = "http://union.123.sogou.com/inc/verification/code.php";
    }

    public function get_data($date = ''){
        //获取验证码
        $code = $this->_get_code();
        
        //登录参数
        $this->_login_params = [
            'username'  => 'zhanmeng7654',
            'pwd'       => md5('gaoxinHL'),
            'authcode'  => md5(strtoupper($code))
        ];
        $login_result = $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file);
        if($login_result != "{web/flow.html}")
        {
            return false;
        }
        
        $date_start  = $date ? $date : date("Y-m-d",strtotime("-1 days"));
        $url = sprintf($this->_get_data_url,$date_start,$date_start);
       
        $reponse = $this->get_content($url,'',$this->_cookiejar_file);
        $data = $this->fiter_data($reponse);
        return $data;
    }
    
    /**
     * 获取验证码
     * @return boolean
     */
    protected function _get_code()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $this->_captcha_path = "c:\\tmp\hr_captcha_".time().".jpg";
        }
        else
        {
            $this->_captcha_path = "/tmp/hr_captcha_".time().".jpg";
        }
        //识别验证码
        $this->get_captcha($this->_captcha_url,$this->_cookiejar_file,$this->_captcha_path);
        $code = $this->identification_captcha($this->_captcha_path);
        if (!$code)
        {
            $i = 1;
            while ($i <= 3)
            {
                $code = $this->identification_captcha($this->_captcha_path);
                if($code)
                {
                    break;
                }
                $i++;
            }
            if(!$code)
            {
                return false;
            }
        }
        
        return $code;
    }

    /**
     * 拿到xml数据
     * @param $str_xml
     * @return array [渠道代码,流量]
     */
    public function fiter_data($str_xml)
    {
        $source = Tool::get_xml_data($str_xml);
        $arr = [];
        if($source['item'])
        {
            foreach($source['item'] as $value)
            {
                $temp = [];
                $org_id = trim($value['pid'],'_');  //渠道代码
                $count = intval($value['data']);    //流量
                //处理子渠道
                $org_id_arr = explode('_', $org_id);
                if(count($org_id_arr) == 2)
                {
                    $org_id = $org_id_arr[0];
                }
                
                if(array_key_exists($org_id, $arr))
                {
                    $arr[$org_id]['count'] += $count;
                }
                else
                {
                    $temp['org_id'] = $org_id;//渠道代码
                    $temp['count'] = $count;
                    $arr[$org_id] = $temp;
                }
            }
        }
        return array_values($arr);
    }
}
<?php
/**
 *@author huxiaowei1238
 *鲁大师
 */
namespace VendorData\AutoCrawl;

class Promotion_lds extends Base 
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
        $this->_login_url = "http://huanliang.ludashi.com/login/login";
        $this->_cookiejar_file = $this->make_cookie_jar("ludashi");
        $this->_get_data_url = 'http://huanliang.ludashi.com/index/index';

        //登录参数
        $this->_login_params = [
            ['tel' => '15800656117'  ,'passwd' => '123456','_' => '1440492053985'],
            ['tel' => '18721223929'  ,'passwd' => '123456','_' => '1440492053985'],
            ['tel' => '18608033837'  ,'passwd' => '123456','_' => '1440492053985'],
        ];
    }
    
    public function get_data($date = '')
    {
        $this->vendor_data = [];
        foreach ($this->_login_params as $login)
        {
            //登录
            $code = $this->_get_code();//获取验证码
            if(!$code)
            {
                continue;
            }
            
            $login['validate_code'] = $code;
            $login_result = $this->login($this->_login_url,"GET",$login,$this->_cookiejar_file);
            $login_result = json_decode($login_result);
            if($login_result->errno != 0)
            {
                continue;
            }
            
            //抓取数据
            $date_start  = $date ? $date : date("Y-m-d",strtotime("-3 days"));
            $params = [
                "date_end"  => $date_start,
                "date_start"=> $date_start,
            ];
            //第一页
            $reponse = $this->get_content($this->_get_data_url,$params,$this->_cookiejar_file);
            $data = $this->fiter_data($reponse);
        }
        return $this->vendor_data;
    }

    /**
     * 拿到表格数据
     * @param $data
     * @return array [渠道名称,安装激活人数]
     */
    public function fiter_data($data)
    {
        $dom = new \DomDocument();
        @$dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        $tr_list = $dom->getElementsByTagName('tr');
        $temp = [];
        for($i = 0; $i < $tr_list->length; $i++) 
        {
            $tr = $tr_list->item($i);
            $td_list = $tr->getElementsByTagName("td");
            if ($td_list->length > 0)
            {
                $temp['org_id'] =trim($td_list->item(1)->nodeValue);//渠道名称
                $temp['count'] =intval(trim($td_list->item(3)->nodeValue));//安装激活人数
                array_push($this->vendor_data, $temp);
            }
        }
        return $temp;
    }
    
    /**
     * 获取验证码
     * @return boolean
     */
    protected function _get_code()
    {
        //设置获取验证码地址、及存放地址
        $this->_captcha_url = "http://huanliang.ludashi.com/login/genValidateCode?_rnd=0.".mt_rand(10000000, 99999999).mt_rand(10000000, 99999999);
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
}
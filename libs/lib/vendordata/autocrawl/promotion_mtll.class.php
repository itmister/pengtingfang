<?php
/**
 *@author huxiaowei1238
 *美图浏览
 */
namespace VendorData\AutoCrawl;
use \Util\Tool;

class Promotion_mtll extends Base 
{
    /**
     * 验证码请求地址
     */
    private $_captcha_url;
    /**
     * 厂商数据
     */
    private $vendor_data;
    /**
     * tn号
     */
    private $org_id;
    
    public static function get_instance()
    {
        return parent::get_instance(__CLASS__);
    }

    public function __construct()
    {
        //初始化
        $this->_login_url = "http://tj.meituview.com/ChannelClient/Manage/Login";
        $this->_cookiejar_file = $this->make_cookie_jar("mtll");
        $this->_get_data_url = 'http://tj.meituview.com/ChannelClient/Information/InstallDetail?start=%s&end=%s';
        
        //登录参数
        $this->_login_params = [
            ['UserName' => 'taoqq'  ,'Password' => '123123','org_id' => 'taoqq'],
            ['UserName' => 'taoqq1' ,'Password' => '123123','org_id' => '220'],
            ['UserName' => 'haoli'  ,'Password' => '123123','org_id' => '225 '],
            ['UserName' => 'haoli1' ,'Password' => '123123','org_id' => '226'],
        ];
       
    }

    public function get_data($date = '')
    {
        $this->vendor_data = [];
        foreach ($this->_login_params as $params)
        {
            $login = [
                'UserName' => $params['UserName'],
                'Password' => $params['Password']
            ];
            
            //tn号
            $this->org_id = $params['org_id'];
            
            //登录
            $login_result = $this->login($this->_login_url,"POST",$login,$this->_cookiejar_file);
            
            $date_start  = $date ? $date : date("Y-m-d",strtotime("-1 days"));
            
            //第一页
            $url = sprintf($this->_get_data_url,$date_start,$date_start);
            $reponse = $this->get_content($url,'',$this->_cookiejar_file);
            $data = $this->fiter_data($reponse);
        }
        
        return $this->vendor_data;
    }   

    /**
     * 拿到表格数据
     * @param $data
     * @return array [日期,安装量  ]
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
                $temp['org_id'] = $this->org_id;
                $temp['count'] =intval(trim($td_list->item(1)->nodeValue));//安装量
                array_push($this->vendor_data, $temp);
            }
        }
        return $temp;
    }
}
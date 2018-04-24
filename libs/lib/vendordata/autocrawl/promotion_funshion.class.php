<?php
namespace VendorData\AutoCrawl;
class Promotion_funshion extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_login_url = "http://partner.funshion.com/login/login.php";
        $this->_cookiejar_file = $this->make_cookie_jar("funshion");
        $this->_get_data_url = 'http://partner.funshion.com/stat/stat.php';
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $sid = $this->get_sid();
        if (!$sid) return false;
        $this->_login_params = [
            'module'=>'login',
            'opt'=>'login',
            'password'=>md5(md5('zhanmengadmin').$sid),
            'subopt'=>'end',
            'username'=>'zhangmeng'
        ];
        $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file);
        $params = [
                    'channel_id'=>'',
                    'channel_name'=>'',
                    'cur_page'=>1,
                    'direction'=>'',
                    'end_date'=>$date,
                    'module'=>'publish',
                    'opt'=>'view_list',
                    'parent_channel_id'=>'175866',
                    'per_page_num'=>0,
                    'search_choice'=>'channel_id',
                    'search_type'=>'',
                    'search_value'=>'',
                    'sortby'=>'',
                    'start_date'=>$date,
                    'type'=>'7valid',
                    'per_page_num'=>2000
                ];
        //第一页
        $reponse = $this->post_content($this->_get_data_url,$params,$this->_cookiejar_file);
        $data = $this->fiter_data($reponse);
        array_shift($data);
        $pages = $this->fiter_pages($reponse);
        //从2开始到后面（没有支持第一页不显示的页数）
        foreach ($pages as $page){
            $params['cur_page'] = $page;
            $reponse = $this->post_content($this->_get_data_url,$params,$this->_cookiejar_file);
            array_shift($reponse);
            $data = array_merge((array)$data,(array)$this->fiter_data($reponse));
        }
        return $data;
    }

    /**
     * 拿到sid
     * @return bool
     */
    public  function get_sid(){
        $sid_url = "http://partner.funshion.com/login/login.php?module=login&opt=login&subopt=begin";
        $response = $this->get_content($sid_url,[],$this->_cookiejar_file);
        if ($response){
            preg_match_all('/var sid = (\d*)/',$response,$r);
            return  $r[1][0];
        }
        return false;
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
        $page_note = $dom->getElementById('trlist');
        if ($page_note){
            $a_list = $page_note->getElementsByTagName("a");
            for($i = 0; $i < $a_list->length; $i++) {
                $a = $a_list->item($i);
                if ( intval($a->nodeValue)){
                    $pages[] = intval($a->nodeValue);
                }
            }
        }
        return $pages;
    }

    /**
     * 拿到表格数据
     * @param $data
     * @return array [渠道ID,新增用户数]
     */
    public function fiter_data($data){
        $dom = new \DomDocument();
        $dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        $tr_list = $dom->getElementById("tablelist")->getElementsByTagName("tr");
        for($i = 0; $i < $tr_list->length; $i++) {
            $tr = $tr_list->item($i);
            if (!$tr->attributes->getNamedItem("id")){
                $td_list = $tr->getElementsByTagName("td");
                /*for($j = 0; $j < $td_list->length; $j++){
                    $arr[$i][] = trim($td_list->item($j)->nodeValue);
                }*/
                if ($td_list->length > 0){
                    $temp = [];
                    $temp['org_id'] =trim($td_list->item(0)->nodeValue);//渠道ID
                    $temp['count'] =intval(trim($td_list->item(3)->nodeValue));//新增用户数
                    $arr [] = $temp;
                }
            }
        }
        return $arr;
    }
}
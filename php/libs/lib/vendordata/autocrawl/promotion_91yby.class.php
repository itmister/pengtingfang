<?php
namespace VendorData\AutoCrawl;
class Promotion_91yby extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_login_params = ['TxtPassword'=>'aq12ws',
                                'TxtUserName'=>'zjlm',
                                'UserLoginBtn'=>'',
                                '__EVENTVALIDATION'=>'/wEdAATKxodqI2alI0PHwWVXYQG3wq7Fr2euId72M7tGGVWWmtkujToWXoNg/+RhNc/N0HZsEVqsE+yU9Tdnnkp8u8SxckrcL5Z31jeqADiEg2IoAdpzgPEKFBNDIlQrQEi5tdw=',
                                '__VIEWSTATE'=>'/wEPDwUKLTMzNDE0ODA3MmRkb+5BFwNPtd3vsQ3KbSj8tvU/wleaCGKAV5f7O21wlcU=',
                                ' __VIEWSTATEGENERATOR'=>'C2EE9ABB'
                                ];
        $this->_login_url = "http://qd.fuyuncc.com/Login.aspx";
        $this->_cookiejar_file = $this->make_cookie_jar("91yby");
        $this->_get_data_url = 'http://qd.fuyuncc.com/Union/Union_Details_Sub.aspx';
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file);
        $params = [
                "__VIEWSTATE"=>"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",
                "__VIEWSTATEGENERATOR"=>"26C4CB20",
                "txtDate1"=>$date,
                "txtDate2"=>$date,
                "__EVENTVALIDATION"=>"/wEdAATvOUl+5kZ4ZXeRNN3YBNScHc2J6nQnI0WN3AG53VWWj0F6hHX+JKqBmM1w+s9uW4fN+DvxnwFeFeJ9MIBWR693HmWd/cwYoBP+TlaxvXtUZ+GKu0HXDexn9gcWzIhDemI=",
               // "Button1"=>"查询",
                "__EVENTTARGET"=>"AspNetPager1",
                "__EVENTARGUMENT"=>1
                ];
        //第一页
        $reponse = $this->post_content($this->_get_data_url,$params,$this->_cookiejar_file);
        $data = $this->fiter_data($reponse);
        $pages = $this->fiter_pages($reponse);
        //从2开始到后面（没有支持第一页不显示的页数）
        foreach ($pages as $page){
            $params['__EVENTARGUMENT'] = $page;
            $reponse = $this->post_content($this->_get_data_url,$params,$this->_cookiejar_file);
            $data = array_merge($data,$this->fiter_data($reponse));
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
        $page_note = $dom->getElementById('AspNetPager1');
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
     * @return array [子渠道号,回访率]
     */
    public function fiter_data($data){
       // var_dump($data);
        $dom = new \DomDocument();
        $dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        $tr_list = $dom->getElementsByTagName('tr');
        $arr = [];
        for($i = 0; $i < $tr_list->length; $i++) {
            $tr = $tr_list->item($i);
            $td_list = $tr->getElementsByTagName("td");
            /*for($j = 0; $j < $td_list->length; $j++){
               $arr[$i][] =$td_list->item($j)->nodeValue;
            }*/
            if ($td_list->length > 0){
                $temp = [];
                $temp['org_id'] =trim($td_list->item(1)->nodeValue);//子渠道号
                $temp['count'] =intval(trim($td_list->item(10)->nodeValue));//回访率
                $arr [] = $temp;
            }
        }
        return $arr;
    }
}
<?php
namespace VendorData\DownloadFile;
class Promotion_duba extends Base {
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }
    public function __construct(){
        $this->_remote_file_path = "http://union.qidou.com/api/getdatas.php?token=a9d84cc99c3db67496fd5f0613a238fe&date=%s";
        //$this->_save_path = "c:/test/%s.html";
        $this->_save_path = "/app/www/jf7654/Public/upload/PerUpload/%s.html";
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-2 days")); //毒霸抓两天前的数据
        $ymd = intval(date("Ymd",strtotime($date)));
        $remote_file_path = sprintf($this->_remote_file_path ,$ymd);
        $save_path = sprintf($this->_save_path ,$ymd);
        if($this->get_remote_file($remote_file_path,$save_path)){
            $data = file_get_contents($save_path);
            $data = preg_replace("/<(head.*?)>(.*?)<(\/head.*?)>/si","",$data); //过滤head标签
            return $this->fiter_data($data);
        }else{
            @unlink($save_path);
            return false;
        }
    }

    public function fiter_data($data){
        $dom = new \DomDocument();
        $dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        $tr_list = $dom->getElementsByTagName('tr');
        $arr = [];
        for($i = 0; $i < $tr_list->length; $i++) {
            $tr = $tr_list->item($i);
            $td_list = $tr->getElementsByTagName("td");
            if ($td_list->length > 0){
                $temp = [];
                $temp['org_id'] =trim($td_list->item(0)->nodeValue);//渠道号
                $temp['count'] =intval(trim($td_list->item(1)->nodeValue));//激活数
                $arr [] = $temp;
            }
        }
        array_shift($arr); //去掉title
        array_shift($arr); //去掉表头
        return $arr;
    }
}
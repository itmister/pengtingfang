<?php
namespace VendorData\Attachment;
/**
 * QQ浏览器
 * Class Promotion_qqbrowser
 * @package VendorData\Attachment
 */
class Promotion_qqbrowserv2 extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_attachment_path = "/app/www/jf7654/emailAttach/attachments/rainyu@tencent.com/tencent qqbrowser_100241_%s(11).html";
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $file_path = sprintf($this->_attachment_path,$date);
        if (!is_file($file_path)){
           return false;
        }
        $data = file_get_contents($file_path);
        $data = preg_replace("/<(head.*?)>(.*?)<(\/head.*?)>/si","",$data); //过滤head标签
        $data = $this->fiter_data($data);
        return $data;
    }

    /**
     * 拿到表格数据
     * @param $data
     * @return array [渠道号,激活数]
     */
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
                $temp['org_id'] =trim($td_list->item(1)->nodeValue);//渠道号
                $temp['count'] =intval(trim(str_replace([',','，'], '',$td_list->item(2)->nodeValue)));//激活数
                $arr [] = $temp;
            }
        }
        array_shift($arr); //去掉表头
        return $arr;
    }
}
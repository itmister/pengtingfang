<?php
namespace VendorData\Attachment;
/**
 * qqmgr Q管
 * Class Promotion_qqmgr
 * @package VendorData\Attachment
 */
class Promotion_qqmgr extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_attachment_path = "/app/www/jf7654/emailAttach/attachments/rainyu@tencent.com/";
    }

    public function get_data($date = ''){
        if (!file_exists($this->_attachment_path)){
           return false;
        }
        $date  = $date ? $date : date("Y-m-d",strtotime("-1 days"));
        
        //获取文件数据
        $filename = $this->_attachment_path."tencent_ qqmgr_".$date.".html";
        if(!file_exists($filename)){
            return false;
        }
        $data = file_get_contents($filename);
        $result = $this->fiter_data($data,$date);
        
        return $result;
    }
    
    /**
     * 拿到表格数据
     * @param $data
     * @return array [日期,安装量  ]
     */
    public function fiter_data($data,$date)
    {
        $data = mb_convert_encoding($data,"gb2312", "utf-8");
        $dom = new \DomDocument();
        $dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        $tr_list = $dom->getElementsByTagName('tr');
        $arr = [];
        for($i = 1; $i < $tr_list->length; $i++)
        {
            $tr = $tr_list->item($i);
            $td_list = $tr->getElementsByTagName("td");
            if ($td_list->length > 0)
            {
                $ymd = date('Y-m-d',strtotime(trim($td_list->item(0)->nodeValue)));
                if($ymd == $date)
                {
                    $temp = [];
                    $temp['org_id'] = trim($td_list->item(1)->nodeValue);
                    $temp['count']  = intval(trim($td_list->item(2)->nodeValue));//安装量
                    $arr [] = $temp;
                }
                
            }
        }
        
        return $arr;
     }
}
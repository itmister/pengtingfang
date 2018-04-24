<?php
namespace VendorData\Attachment;
/**
 * 益盟爱炒股
 * Class Promotion_cgym
 * @package VendorData\Attachment
 */
class Promotion_cgym extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_attachment_path = "/app/www/jf7654/emailAttach/attachments/istock@emoney.cn/%s-益盟爱炒股软件286渠道用户有效管量.csv";
        //$this->_attachment_path = "C:/Users/H-067/Desktop/attachments/istock@emoney.cn/%s.csv";
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $file_path = sprintf($this->_attachment_path,$date);
        if (!is_file($file_path)){
            return false;
        }
        $data = file($file_path);
        $data = $this->fiter_data($data);
        return $data;
    }

    /**
     * 拿到表格数据
     * @param $data
     * @return array [渠道号,有效量]
     */
    public function fiter_data($data){
        array_shift($data);
        $arr = [];
        foreach($data as $val){
            list ($org_id,$count) = explode(",",$val);
            $temp = [];
            $temp['org_id'] = trim($org_id);
            $temp['count'] =intval($count);
            $arr [] = $temp;
        }
        return $arr;
    }
}
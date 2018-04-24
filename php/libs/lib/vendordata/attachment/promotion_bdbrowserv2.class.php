<?php
namespace VendorData\Attachment;
/**
 * 百度浏览器
 * Class Promotion_bdbrowserv2
 * @package VendorData\Attachment
 */
class Promotion_bdbrowserv2 extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_attachment_path = "/app/www/jf7654/emailAttach/attachments/bd_merge/%s.csv";
    }

    /**
     * 拿到表格数据
     * @param $data
     * @return array [渠道号,激活数]
     */
    public function fiter_data($data){
        array_shift($data);
        $arr = [];
        foreach($data as $val){
            $temp = [];
            $temp['org_id'] =trim($val[0]);//子渠道号
            $temp['count'] =intval($val[3]);//回访率
            $arr [] = $temp;
        }
        return $arr;
    }
}
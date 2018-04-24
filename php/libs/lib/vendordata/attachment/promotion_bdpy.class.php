<?php
namespace VendorData\Attachment;
/**
 * 百度拼音
 * Class Promotion_bdpy
 * @package VendorData\Attachment
 */
class Promotion_bdpy extends Base {

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
            $temp['org_id'] =trim($val[0]);
            $temp['count'] =intval($val[4]);
            $arr [] = $temp;
        }
        return $arr;
    }
}
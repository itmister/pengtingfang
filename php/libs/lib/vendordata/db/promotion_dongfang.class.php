<?php
namespace VendorData\DB;
/**
 * 百度桌面
 * Class Promotion_dongfang
 * @package VendorData\Attachment
 */
class Promotion_dongfang extends Base {
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd = intval(date("ymd",strtotime($date)));
        $info = \Dao\Union\Vendor_Org_Data::get_instance()->query("call get_dongfang_7654_validinstall({$ymd})");
        $arr  = [];
        foreach ($info as $item ) {
            $temp = [];
            $temp['org_id'] = $item['qid'];
            $temp['count']  = $item['ct'];
            $arr [] = $temp;
        }
        return $arr;
    }

}
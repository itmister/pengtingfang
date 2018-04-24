<?php
namespace VendorData\DB;
/**
 * QQ管家
 * Class Promotion_ktw
 * @package VendorData\Attachment
 */
class Promotion_ktw extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd = intval(date("ymd",strtotime($date)));
        $info = \Dao\Union\Vendor_Org_Data::get_instance()->query("call get_kantu_7654_validinstall('{$ymd}','{$ymd}')");
        $arr  = [];
        foreach ($info as $item ) {
            $temp = [];
            $temp['org_id'] = $item['qid'];
            $temp['count']  = $item['dayinstall'];
            $temp['active_count'] =$item['validcnt'];
            $arr [] = $temp;
        }
        return $arr;
    }
}
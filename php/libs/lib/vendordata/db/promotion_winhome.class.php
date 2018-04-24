<?php
namespace VendorData\DB;
/**
 * 主页卫士
 * Class Promotion_winhome
 * @package VendorData\Attachment
 */
class Promotion_winhome extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd   = date("Ymd",strtotime($date));

        $info = \Dao\Winhome\Channel_pay_data::get_instance()->select(
            array(
                "where"=>"ymd={$ymd} and channel='7654'",
                "field"=>"sub_channel,install,online"
            )
        );


        $arr  = [];
        foreach ($info as $item){
            $temp = [];
            $temp['org_id'] = $item['sub_channel'];
            $temp['count']  = $item['install'];
            $temp['active_count']  = $item['online'];
            $arr [] = $temp;
        }
        return $arr;
    }
}
<?php
namespace VendorData\DB;
/**
 * U大师使用UEFI版
 * Class Promotion_uds_sy_uefi
 * @package VendorData\Attachment
 */
class Promotion_uds_sy_uefi extends Base{

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function get_data($date=''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd   = date("Ymd",strtotime($date));

        $sql = "SELECT qid,ymd,active
                FROM stat_channel_data_uefi
                where ymd={$ymd} and active>0
                ORDER BY active DESC";
        $kuaizip_data = \Dao\Udashi_admin\Stat\Channel_data::get_instance()->query($sql);
        $arr  = [];
        foreach ($kuaizip_data as $item) {
            $temp = [];
            $qid = $item['qid'];
            if(empty($qid)||empty($item['active'])) continue;
            $temp['org_id'] = $qid;
            $temp['count']  = $item['active'];
            $arr[] = $temp;
        }
        return $arr;
    }
}
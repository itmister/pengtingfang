<?php
namespace VendorData\DB;
/**
 * QQ管家锁7654导航
 * Class Promotion_7654_dh_qqpcmgr
 * @package VendorData\Attachment
 */
class Promotion_7654_dh_qqpcmgr extends Base{

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function get_data($date=''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd   = date("Y-m-d",strtotime($date));

        $sql = "SELECT channelname,determine_ip
                FROM `data`
                where dt='{$ymd}' and channelname like '7654qg_%' and determine_ip>0
                ORDER BY determine_ip DESC";
        $kuaizip_data = \Dao\Daohang_admin\Data::get_instance()->query($sql);
        $arr  = [];
        foreach ($kuaizip_data as $item) {
            $temp = [];
            $qid = $item['channelname'];
            $ip = intval($item['determine_ip']);
            if(empty($qid)||empty($ip)) continue;
            $temp['org_id'] = $qid;
            $temp['count']  = $ip;
            $arr[] = $temp;
        }
        return $arr;
    }
}
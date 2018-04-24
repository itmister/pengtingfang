<?php
namespace VendorData\DB;
/**
 * 快压
 * Class Promotion_jsdh
 * @package VendorData\DB
 */
class Promotion_jsdh extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function get_data($date=''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd   = date("Ymd",strtotime($date));

        $sql = "SELECT org_id FROM jsdh_auto_data
                where soft_id='jsdh' and ymd={$ymd} and status <> 1";
        $kuaizip_data = \Dao\Union\Jsdh_auto_data::get_instance()->query($sql);
        if($kuaizip_data){//数据没有完全抓取成功
            return false;
        }
        $sqlTwo = "SELECT org_id,num FROM jsdh_auto_data
                where soft_id='jsdh' and ymd={$ymd} and status=1";
        $kuaizipData = \Dao\Union\Jsdh_auto_data::get_instance()->query($sqlTwo);
        if(empty($kuaizipData)){//数据没有抓取成功
            return false;
        }

        $arr  = [];
        foreach ($kuaizipData as $item) {
            $temp = [];
            $qid = $item['org_id'];//渠道id
            $num = intval($item['num']);//安装量
            $temp['org_id'] = $qid;
            $temp['count']  = $num;
            $arr [] = $temp;
        }
        return $arr;
    }
}
<?php
namespace VendorData\DB;
/**
 * 快压
 * Class Promotion_kyzip
 * @package VendorData\Attachment
 */
class Promotion_kyzip extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

//    public function get_data($date = ''){
//        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
//        $ymd   = date("Ymd",strtotime($date));
//
//        $params = [
//            'where' => "logdt = {$ymd} AND logtype = 600101 AND softid ='KuaiZip'",
//        ];
//        $info = \Dao\Quya\Rt_kuaizip_qid_data::get_instance()->select($params);
//        $arr  = [];
//        foreach ($info as $item ) {
//            $temp = [];
//            $temp['org_id'] = $item['qid'];
//            $temp['count']  = $item['cnt'];
//            $arr [] = $temp;
//        }
//        return $arr;
//    }

    public function get_data($date=''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd   = date("Ymd",strtotime($date));

        $sql = "SELECT qid,
                       IFNULL(sum(case when logtype = 600101 then cnt end),0) as num,
                       IFNULL(sum(case when logtype = 600102 then cnt end),0) as num_total,
                       IFNULL(sum(case when logtype = 600119 then cnt end),0) as xishu,
                       sum(case when logtype = 600120 then cnt end) as zuobi,
                       IFNULL(SUM(CASE WHEN `logtype` = 600104 THEN `cnt` END),0) AS start_num,
                       IFNULL(SUM(CASE WHEN `logtype` = 600108 THEN `cnt` END),0) AS uninstall_num,
                       IFNULL(SUM(CASE WHEN `logtype` = 600212 THEN `cnt` END),0) AS morrow_num,
                       IFNULL(SUM(CASE WHEN `logtype` = 600101 THEN `cnt` END),0) AS morrow_num2
                FROM rt_kuaizip_qid_data
                where softid='KuaiZip' and logdt={$ymd} and logtype in (600101,600102,600104,600108,600119,600212,600120)
                GROUP BY qid having num>0 ORDER BY num DESC";
        $kuaizip_data = \Dao\Quya\Rt_kuaizip_qid_data::get_instance()->query($sql);

        $arr  = [];
        foreach ($kuaizip_data as $item) {
            $temp = [];
            $qid = $item['qid'];//渠道id
            $num = intval($item['num']);//安装量
            $num_total = intval($item['num_total']);//累计安装量
            //$xishu = intval($item['xishu']);//系数
            $xishu = $this->_get_xishu($item);
            $zuobi = $item['zuobi'];//是否作弊
            $zuobi = 0 ;//没有作弊的感念 2016-08-10
            //if($num_total>10){
            if($num>10){
                $install = floor($num*$xishu);
            }else{
                $install = floor($num);
            }
            if($install<=0 || $zuobi==-1 || $xishu<=0){
                continue;
            }
            $temp['org_id'] = $qid;
            $temp['count']  = $install;
            $arr[] = $temp;
        }
        return $arr;
    }


    public function get_data_csv($date=''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd   = date("Ymd",strtotime($date));

        $sql = "SELECT qid,
                       IFNULL(sum(case when logtype = 600101 then cnt end),0) as num,
                       IFNULL(sum(case when logtype = 600102 then cnt end),0) as num_total,
                       IFNULL(sum(case when logtype = 600119 then cnt end),0) as xishu,
                       sum(case when logtype = 600120 then cnt end) as zuobi,
                       IFNULL(SUM(CASE WHEN `logtype` = 600104 THEN `cnt` END),0) AS start_num,
                       IFNULL(SUM(CASE WHEN `logtype` = 600108 THEN `cnt` END),0) AS uninstall_num,
                       IFNULL(SUM(CASE WHEN `logtype` = 600212 THEN `cnt` END),0) AS morrow_num,
                       IFNULL(SUM(CASE WHEN `logtype` = 600101 THEN `cnt` END),0) AS morrow_num2
                FROM rt_kuaizip_qid_data
                where softid='KuaiZip' and logdt={$ymd} and qid like '7654%' and logtype in (600101,600102,600104,600108,600119,600212,600120)
                GROUP BY qid having num>0 ORDER BY num DESC";
        $kuaizip_data = \Dao\Quya\Rt_kuaizip_qid_data::get_instance()->query($sql);

        $arr  = [];
        foreach ($kuaizip_data as $item) {
            $temp = [];
            $qid = $item['qid'];//渠道id
            $num = intval($item['num']);//安装量
            $num_total = intval($item['num_total']);//累计安装量
            //$xishu = intval($item['xishu']);//系数
            $xishu = $this->_get_xishu($item);
            $zuobi = $item['zuobi'];//是否作弊
            $zuobi = 0 ;//没有作弊的感念 2016-08-10
            //if($num_total>10){
            if($num>10){
                $install = floor($num*$xishu);
            }else{
                $install = floor($num);
            }
            if($install<=0 || $zuobi==-1 || $xishu<=0){
                continue;
            }
            $temp['ymd'] = $ymd;
            $temp['org_id'] = $qid;
            $temp['num'] = $num;
            $temp['num_total'] = $num_total;
            $temp['xishu'] = $xishu;
            $temp['count']  = $install;
            $arr[] = $temp;
        }

        \Util\Tool::another_export_csv($arr,['日期','渠道号','全库去重安装量','累计安装总量','结算系数','有效量'],$ymd);
        //return $arr;
        exit("ok");
    }


    //计算系数
    private function _get_xishu($data){

        $undiff_rate = 15;
        $startdiff = 30;
        $start_rate     = round( $data['start_num'] / $data['num_total'] * 100 , 2);//总启动率
        $uninstall_rate = round( $data['uninstall_num'] / $data['num_total'] * 100 , 2);//总卸载率
        $morrow_rate    = round( $data['morrow_num'] / $data['morrow_num2'] * 100 , 2);//次日使用率

        //总启动率系数
        $start_rate_xishu       = round((40 * $start_rate / $startdiff) / 100,2);
        $start_rate_xishu       = ($start_rate_xishu > 0.4) ? 0.4 : ($start_rate_xishu<0?0:$start_rate_xishu);

        //总卸载率系数
        $uninstall_rate_xishu   = round((40 + 40 * ($undiff_rate - $uninstall_rate) / $undiff_rate) / 100,2);
        $uninstall_rate_xishu   = ($uninstall_rate_xishu > 0.4) ? 0.4 : ($uninstall_rate_xishu < 0 ? 0 : $uninstall_rate_xishu);

        //次日留存率系数
        $morrow_rate_xishu      = round((20 * $morrow_rate / 50) / 100,2);
        $morrow_rate_xishu      = ($morrow_rate_xishu > 0.2) ? 0.2 : ($morrow_rate_xishu<0?0:$morrow_rate_xishu);

        $xishu = $start_rate_xishu + $uninstall_rate_xishu + $morrow_rate_xishu;

        return $xishu ? $xishu : 1;
    }
}
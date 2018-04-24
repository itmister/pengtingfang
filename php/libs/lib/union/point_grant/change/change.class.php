<?php
namespace Union\Point_grant\Change;
use Util\Tool;
/**
 * Created by JetBrains PhpStorm.
 * User: caolei
 * Date: 16-4-7
 * Time: 上午10:28
 * To change this template use File | Settings | File Templates.
 * 原始渠道号转换uid
 */
class Change{

    protected static $_instance = null;

    /**
     * @return \Union\Point_grant\Change\Change
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /*
    *TODO 包回收发放业绩 把第三方org_id转换成7654,uid
    * $arr 渠道号=》有效量
    */
    public function org_id_change_uid($orgData,$soft_id,$ymd){
        $o_arr_ymd = $o_arr_ymd_r = [];
        //厂商返回的org_id集合
        $orgArr = array_keys($orgData);
        $orgStr = implode("','",$orgArr);
        $orgStr = "'".$orgStr."'";
        //当前时间内未被回收的用户org_id 、uid
        $sql = "select uid,org_id from assign_orgid where softID='{$soft_id}' and status=0 and org_id in ({$orgStr}) and FROM_UNIXTIME(dateline,'%Y%m%d')<={$ymd};";
        $orgUserOnline = \Dao\Union\Assign_orgid::get_instance()->query($sql);
        if(!empty($orgUserOnline)){
            foreach($orgUserOnline as $key => $value){
                $key = $value['uid'].'_'.$key.'_2';
                $o_arr_ymd[$key] = $value['org_id'];
            }
        }

        //当前时间内被回收的用户org_id 、uid
        $sqlHuiShou = "select uid,org_id from assign_orgid where softID='{$soft_id}' and status>0 and org_id in ({$orgStr}) and FROM_UNIXTIME(dateline,'%Y%m%d')<={$ymd} and FROM_UNIXTIME(updateline,'%Y%m%d')>={$ymd} and dateline<>updateline;";
        $orgUserHuiShou = \Dao\Union\Assign_orgid::get_instance()->query($sqlHuiShou);
        if(!empty($orgUserHuiShou)){
            foreach($orgUserHuiShou as $k_y_r=>$v_y_r){
                $key = $v_y_r['uid'].'_'.$k_y_r.'_3';
                $o_arr_ymd_r[$key] = $v_y_r['org_id'];
            }
        }

        $o_arr_ymd_r_1 = array_diff((array)$o_arr_ymd_r,(array)$o_arr_ymd);
        $o_arr = array_merge((array)$o_arr_ymd,(array)$o_arr_ymd_r_1);
        if(empty($o_arr)){
            return false;
        }
        //当前日期线上收量平台没有找到对应uid的orgid
        //$not_send_org = array_diff($orgArr,$o_arr);

        //获取厂商返回包是否已回收
//        $o_arr_yu_list = array();
//        if($not_send_org)
//        {
//            $yu_org_id = implode("','",$not_send_org);
//            $yu_org_id = "'".$yu_org_id."'";
//
//            //已分配出去的org_id;
//            $yu_list = $this->assignOrgidModel->orgid_select($yu_org_id,$soft_id);
//            if(!empty($yu_list))
//            {
//                foreach($yu_list as $k_y_1=>$v_y_1)
//                {
//                    $key = '0_'.$k_y_1.'_1';
//                    $o_arr_yu_list[$key] = $v_y_1['org_id'];
//                }
//
//                $json_str = " 余量org_id=>num分配过\r\n".json_encode($yu_list);
//                Tool::write_log($filepath,$json_str,$filename);
//                Tool::write_log($filepath,$this->assignOrgidModel->get_last_sql(),$filename);
//            }
//
//        }
//        if(!empty($o_arr_yu_list))
//        {
//            $o_arr = array_merge($o_arr,$o_arr_yu_list);
//        }
//        $json_str = " {$ymd}当天的uid=>org_id余量\r\n".json_encode($o_arr);
//        Tool::write_log($filepath,$json_str,$filename);

        //统计org_id分配的次数
        $o_arr_count = array_count_values($o_arr);
        $promotion_data = array();
        #已org_id为键值的数组 方便取uid
        #TODO 下面是 处理一个org_id 分配给多个人 给他们分配有效量
        foreach($o_arr as $key => $org_id){
            //有效量
            $num = isset($orgData[$org_id]) ? $orgData[$org_id] : 0;
            //取出uid
            $key_array = explode('_',$key);
            $uid = $key_array[0];
            if(!$uid){
                continue;
            }
            $count = $o_arr_count[$org_id];
            if($count > 1){
                #TODO 一个org_id 分配给多个人 暂时不用考虑
            }else{
                $promotion_data[$uid] = !isset($promotion_data[$uid])?$num:$promotion_data[$uid]+$num; //有效量
            }
        }
        return $promotion_data;
    }

    public function get_cheat($uidList,$softId){
        //作弊标签
        $uidStr = implode(',',$uidList);
        $cheatUidList = array();
        $tagsUidList = \Dao\Union\Hao123_blackname::get_instance()->select(
            array(
                "where"=>"name='{$softId}' and delete_flag=0 and uid in ({$uidStr})",
                "field"=>"DISTINCT uid"
            )
        );
        \Util\Tool::write_log("/app/joblog/",\Dao\Union\Hao123_blackname::get_instance()->get_last_sql(),$softId.".txt");
        if($tagsUidList){
            foreach($tagsUidList as $v){
                $cheatUidList[] = $v['uid'];
            }
        }
        //0冻结登录 2冻结获取积分
        $blackUidList = \Dao\Union\User::get_instance()->select("status in (0,2) and id in ({$uidStr})","id");
        \Util\Tool::write_log("/app/joblog/",\Dao\Union\User::get_instance()->get_last_sql(),$softId.".txt");
        if($blackUidList){
            foreach($blackUidList as $v){
                $cheatUidList[] = $v['id'];
            }
        }
        \Util\Tool::write_log("/app/joblog/",json_encode(array_unique($cheatUidList)),$softId.".txt");
        return array_unique($cheatUidList);
    }
}

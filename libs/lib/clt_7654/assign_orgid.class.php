<?php
namespace Clt_7654;
use Util\Tool;
/**
 * 技术员分配推广软件包
 * @author huxiaowei1238
 *
 */
class Assign_orgid 
{
    /**
     * 技术员分配推广软件包表模型
     */
    protected $assignOrgidModel;
    protected $XishuConfigModel;
    public function __construct(){
        //初始化数据库模型
        $this->assignOrgidModel  = \Dao\Clt_7654\Assign_Orgid::get_instance();
        $this->XishuConfigModel  = \Dao\Clt_7654\Xishu_config::get_instance();
    }
    
    /*
     *TODO 包回收发放业绩 把第三方org_id转换成7654,uid
     * $arr 渠道号=》有效量
     * $a_org 渠道号 数组
     * $promotion_id，该软件的promotion表中id
     */
    public function change_uid($vendor_data,$promotion,$ymd,$filepath,$fafang = 1)
    {
        //记录日志
        $soft_id  = $promotion['soft_id'];
        $filename = $soft_id."_".$ymd.".txt";
        $json_str = "厂商返回量org_id=>num\r\n".json_encode($vendor_data);
        Tool::write_log($filepath,$json_str,$filename);
        
        $o_arr_ymd = $o_arr_ymd_r = [];
        
        //当前时间内未被回收的用户org_id 、uid
        $org_user_arr = $this->assignOrgidModel->user_orgid($soft_id,$ymd);
        if(!empty($org_user_arr))
        {
            foreach($org_user_arr as $key => $value)
            {
                $key = $value['uid'].'_'.$key.'_2';
                $o_arr_ymd[$key] = $value['org_id'];
            }
            
            //记录未被回收的包日志
            $json_str = "{$ymd}没有被回收过的uid=>org_id\r\n".json_encode($o_arr_ymd);
            Tool::write_log($filepath,$json_str,$filename);
            Tool::write_log($filepath,$this->assignOrgidModel->get_last_sql(),$filename);
        }
        
        //当前时间内被回收的用户org_id 、uid
        $org_user_arr_r = $this->assignOrgidModel->user_orgid_recover($soft_id,$ymd);
        if(!empty($org_user_arr_r))
        {
            foreach($org_user_arr_r as $k_y_r=>$v_y_r)
            {
                $key = $v_y_r['uid'].'_'.$k_y_r.'_3';
                $o_arr_ymd_r[$key] = $v_y_r['org_id'];
            }
            
            $json_str = "{$ymd}之后到今天被回收过的uid=>org_id\r\n".json_encode($o_arr_ymd_r);
            Tool::write_log($filepath,$json_str,$filename);
            Tool::write_log($filepath,$this->assignOrgidModel->get_last_sql(),$filename);
        }
        
        $o_arr_ymd_r_1 = array_diff((array)$o_arr_ymd_r,(array)$o_arr_ymd);
        $o_arr = array_merge((array)$o_arr_ymd,(array)$o_arr_ymd_r_1);
        
        $json_str = " {$ymd}当天的uid=>org_id\r\n".json_encode($o_arr);
        Tool::write_log($filepath,$json_str,$filename);
        if(empty($o_arr) && $fafang == 2)
        {
            $vendor_where = "promotion = {$soft_id} AND ymd = {$ymd}";
            \Dao\Union\Vendor_Data_History::get_instance()->update($vendor_where, ['status' => 0]);
            return false;
        }
        
        //厂商返回的org_id集合
        $vendor_org_id_array = array_keys($vendor_data);
        
        //当前日期线上收量平台没有找到对应uid的orgid
        $not_send_org = array_diff($vendor_org_id_array,$o_arr);
        $json_str = "余量org_id=>num\r\n".json_encode($not_send_org);
        Tool::write_log($filepath,$json_str,$filename);
        
        //获取厂商返回包是否已回收
        $o_arr_yu_list = array();
        if($not_send_org)
        {
            $yu_org_id = implode("','",$not_send_org);
            $yu_org_id = "'".$yu_org_id."'";

            //已分配出去的org_id;
            $yu_list = $this->assignOrgidModel->orgid_select($yu_org_id,$soft_id);
            if(!empty($yu_list))
            {
                foreach($yu_list as $k_y_1=>$v_y_1)
                {
                    $key = '0_'.$k_y_1.'_1';
                    $o_arr_yu_list[$key] = $v_y_1['org_id'];
                }
                
                $json_str = " 余量org_id=>num分配过\r\n".json_encode($yu_list);
                Tool::write_log($filepath,$json_str,$filename);
                Tool::write_log($filepath,$this->assignOrgidModel->get_last_sql(),$filename);
            }
            
        }
        if(!empty($o_arr_yu_list))
        {
            $o_arr = array_merge($o_arr,$o_arr_yu_list);
        }
        $json_str = " {$ymd}当天的uid=>org_id余量\r\n".json_encode($o_arr);
        Tool::write_log($filepath,$json_str,$filename);
        
        //统计org_id分配的次数
        $o_arr_count = array_count_values($o_arr);
        
        #已org_id为键值的数组 方便取uid
        #TODO 下面是 处理一个org_id 分配给多个人 给他们分配有效量
        $promotion_sum  = $promotion_user_num = 0;
        $promotion_data = $temp_data = [];
        
        foreach($o_arr as $key => $org_id)
        {
            //有效量
            $num   = isset($vendor_data[$org_id]) ? $vendor_data[$org_id] : 0;
            $xishu = $this->XishuConfigModel->find(['where' => "soft_id = '{$soft_id}'"]);
            if($xishu){
                $num = (int)($xishu['xishu'] * $num);
            }
            
            //取出uid
            $key_array = explode('_',$key);
            $uid = $key_array[0];
            if(!$uid){
                continue;
            }
            
            $count = $o_arr_count[$org_id];
            if($count > 1)
            {
                #TODO 一个org_id 分配给多个人 暂时不用考虑
               
            }
            else
            {
                $temp = array(
                    'uid'           => $uid,            //用户id
                    'qid'           => $org_id,         //软件渠道号或tn号
                    'soft_id'       => $soft_id,        //软件soft_id
                    'pid'           => $promotion['id'],//软件id
                    'num'           => $num,            //有效量
                    'num_original'  => $num,            //原始有效量
                    'ymd'           => $ymd,            //发放日期
                    'dateline'	    => time(),          //添加时间
                    'state'		    => 0,               //状态
                    'fafang'        => $fafang,         //发放类型
                    'zuobi'		    => 1                //作弊 1 未作弊  2已作弊
                );
                array_push($promotion_data,$temp);
            }
            
            //实际发放总量
            $promotion_sum += $num;
            
            //实际发放总人数
            if(!$temp_data[$uid]){
                $temp_data[$uid] = $uid;
                $promotion_user_num += 1;
            }
        }
        
        $json_str = "最终发放的数据\r\n".json_encode($promotion_data);
        Tool::write_log($filepath,$json_str,$filename);
        
        $return_data = [
            $promotion_data,$promotion_user_num,$promotion_sum
        ];
        return $return_data;
    }
}
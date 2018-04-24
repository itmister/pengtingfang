<?php
namespace Union\Stat\Manager;

/**
 * 统计-经理人推广、推广软件厂商数据
 * Class Promotion
 * @package Union\Stat\Manager
 */

class Promotion {
    
    private $arr_area_id_list = array();

    /**
     * @param $software_name
     * @return array
     */
    public function get_detail( $software_name ) {

    }


    /**
     * 取每天推广详情
     * @param $ymd_start
     * @param $ymd_end
     * @param $software_name
     * @param $channel_master_id
     */
    public function get_day_detail_list( $ymd_start, $ymd_end, $software_name, $channel_master_id ) {

    }

    /**
     * 取某一天市场经理的推广详情
     * @param $ymd
     * @param $soft_id
     * @return array(
     * array(
            ymd : 年月日
            invite_user_name : 市场经理用户名
            phone : 手机号
            invite_uid : 市场经理uid
            user_total : 发放技术员人数
            performance_total : 实际发放量
            total_org : 厂商返回量
        )
     * );
     */
    public function get_manager_day_detail_list( $soft_id,$ymd,$arr_channel_master_id,$column,$updown, $user_name = '' ) {
        if(!$this->arr_area_id_list && $arr_channel_master_id)
            $this->arr_area_id_list  = \Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id );
        $dao_credit = \Dao\Union\Log_credit::get_instance();
        $arr_result = $dao_credit->get_softid_manager_detail_by_ymd($soft_id,$ymd,$this->arr_area_id_list,$column,$updown,$user_name );
        
        $arr_uid = $arr_org_total = $arr_user = array();
        $dao_promotion = \Dao\Union\Promotion::get_instance();
        $arr_promotion = $dao_promotion->get_promotion_id($soft_id);
        
        $soft_promotion_id = $arr_promotion[0]['id'];
        $dao_product = \Dao\Union\Ad_product_record_fafang_log::get_instance();
        foreach ($arr_result as $info) {
            $arr_uid[] = $info['invite_uid'];
            $arr_org_total[$info['invite_uid']] = $dao_product->get_org_total_by_promotion_id_ymd_uid ($soft_promotion_id,$ymd,$info['invite_uid']);
        }
        $dao_user = \Dao\Union\User::get_instance();
        $arr_data = $dao_user->get_user_info_by_uids( $arr_uid );
        
        foreach ($arr_data as $v) {
            $arr_user[$v['id']] = $v['phone'];
        }
        
        foreach ($arr_result as $info){
            $info['phone'] = $arr_user[$info['invite_uid']];
            $info['total_org'] = $arr_org_total[$info['invite_uid']];
            $arr_detail[] = $info;
        }
        return $arr_detail;
    }

    /**
     * @desc 历史推广过该产品的市场经理总数
     * @desc 所有市场经理 = areaid > 0 or refer_type = 2
     *        历史推广过该产品的市场经理总数 = 该产品有业绩的技术员的所属市场经理 （不包含市场经理本身）
     *        该产品有业绩的技术员的市场经理 = 该产品有业绩的技术员的邀请人和市场经理的交集
     * @return int $marketer_uid;
     */
    public function get_promotion_manager_total_by_soft($soft_id){
        $dao_credit         =  \Dao\Union\Log_credit::get_instance();
        $formal_manager_uid = $this->get_manager_list();
        #该产品有业绩的技术员的市场经理
        $all_marketer_uid_promotion = $dao_credit->get_manager_uid_by_promotion_area_id($soft_id);
        $all_marketer_uid = array();
        foreach($all_marketer_uid_promotion as $user){
            $all_marketer_uid[] = $user['invite_uid'];
        }
        #邀请人和市场经理的交集
        $marketer_uid = array_intersect($formal_manager_uid,$all_marketer_uid);
        #var_dump($marketer_uid);
        return $marketer_uid ? count($marketer_uid) : 0;
    }
    
    /**
     * @desc 历史发放过该产品业绩的技术员总数
     * @param type $soft_id
     * @return int $uid_total
     */
    public function get_promotion_user_total_by_soft($soft_id){
        $dao_credit = \Dao\Union\Log_credit::get_instance();
        $arr_uid = $dao_credit->get_promotion_uid_total_by_softid($soft_id);
        //var_dump($arr_uid);
        return $arr_uid ? count($arr_uid) : 0;
    }
    
    /**
     * @desc 取市场经理推广明细列表 通过城市id，软件id，
     * @param type $soft_id
     * @param type $arr_area_id
     * @return array(
        array(
            ymd : 年月日
            manager_total : 推广市场经理人数
            user_total : 发放技术员人数
            performance_total : 实际发放量
            total_org : 厂商返回量
        )
     */
    public function get_softid_manager_detail($soft_id,$arr_channel_master_id,$ymd_start,$ymd_end,$start,$limit){
        $ymd_start  = \Util\Datetime::get_ymd( $ymd_start, '-8 day', 'Ymd');
        $ymd_end    = \Util\Datetime::get_ymd( $ymd_end, ' -1 day', 'Ymd');
        if ( $ymd_end - $ymd_start > 1000 ) $ymd_end = date('ymd', strtotime($ymd_start) + 86400 * 100 );
        if(!$this->arr_area_id_list && $arr_channel_master_id)
            $this->arr_area_id_list  = \Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id );
        $dao_credit = \Dao\Union\Log_credit::get_instance();
        $arr_result = $dao_credit->get_softid_manager_detail($soft_id,$this->arr_area_id_list,$ymd_start,$ymd_end,$start,$limit);
        return $arr_result;
    }
    
    /**
     * @desc 获取软件明细总数 通过城市id，软件id，
     * @param type $soft_id
     * @param type $arr_area_id
     * @return int $detail_count_num
     */
    public function get_softid_manager_detail_total($soft_id,$arr_channel_master_id,$ymd_start,$ymd_end){
        $ymd_start  = \Util\Datetime::get_ymd( $ymd_start, '-8 day', 'Ymd');
        $ymd_end    = \Util\Datetime::get_ymd( $ymd_end, ' -1 day', 'Ymd');
        if ( $ymd_end - $ymd_start > 1000 ) $ymd_end = date('ymd', strtotime($ymd_start) + 86400 * 100 );
        if(!$this->arr_area_id_list && $arr_channel_master_id)
            $this->arr_area_id_list  = \Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id );
        $dao_credit = \Dao\Union\Log_credit::get_instance();
        $detail_count_num = $dao_credit->get_softid_manager_detail_total($soft_id,$this->arr_area_id_list,$ymd_start,$ymd_end);
        //var_dump($detail_count_num);
        return $detail_count_num;
    }
    
    /**
     * @desc 所有市场经理 = areaid > 0 or refer_type = 2
     * @desc 通过类型取市场经理类型
     * @return array
     */
    protected function get_manager_list(){
        $dao_user_marketer  = \Dao\Channel_7654\User_marketer::get_instance();
        $arr_user_marketer_list = $dao_user_marketer->get_user_marketer_list();
        $arr_type_manager_uid = array();
        foreach($arr_user_marketer_list as $arr_user){
            $arr_type_manager_uid[] = $arr_user['userid'];
        }
        return $arr_type_manager_uid;
    }
    
    /**
     * @desc 获取经理人下的技术员推广信息
     * @param integer $user_id      经理人id
     * @param integer $start_time   开始时间 
     * @param integer $end_time     结束时间
     * @return array(
         array(
             ymd : 年月日
             total_technician : 绑定技术员人数
             software_total : 软件安装总量
             array(
                 name: 软件名
                 detail_total 数量
             )
             'total' : array(
                ymd : 当天日期
                software_sum ： 软件安装总量
                total_technician ： 绑定技术员总量
                software_group_name ： array(
                    name ：  软件名
                    software_total ： 总量
                )
             )
         )
     * )
     */
    public function get_technician_credit_from_manager( $user_id , $start_time , $end_time , $promotion_id , $start_day , $end_day){

        //实例化
        $dao_credit    =  \Dao\Union\Log_credit::get_instance();
        
        /* $start_time  =  $start_time ? $start_time : date('Ymd' ,strtotime('-7 day',strtotime($start_time) ) );
        if( !$end_time ) {
            $end_time = date('Ymd');
        } */
        
        //当前经理人下的技术员推广信息
        $technician_credit_info = $dao_credit->get_technician_credit_by_invite_uid( $user_id , $start_time , $end_time , $promotion_id ,$start_day , $end_day);
        if( !$technician_credit_info ) {
            return false;
        }
        
        //日期数组
        $date_array = array();

        $technician_credit_total = array();
        $credit_detail = $technician_credit_info['software_group_ymd_total'];
        $total_technician_info = $technician_credit_info['total_technician_group_time'];
        
        if($technician_credit_info['software_total']){
            foreach( $technician_credit_info['software_total'] as $key=>$technician ) {
                
                $index_key = date('Y-m-d',strtotime($technician['ymd']));
                $technician_credit_total['lists'][$key] = $technician;
                $technician_credit_total['lists'][$key]['ymd'] = $index_key;
                
                //绑定技术员数数
                if( $total_technician_info ) {
                    foreach ( $total_technician_info as $k=> $technician_info ) {
                        if( in_array( $technician['ymd'] - 20000000, $technician_info) ){
                            $technician_credit_total['lists'][$key]['total_technician'] = $technician_info['total_technician'];
                        }
                    }
                }
                
                //软件详细
                if( $credit_detail ){
                    foreach ( $credit_detail as $detail ) {
                        if( in_array( $technician['ymd'] , $detail ) ) {
                            $technician_credit_total['lists'][$key]['detail'][] = $detail;
                        }
                    }
                }
            }
        }

        $technician_credit_total['history_software_total'] = $technician_credit_info['history_software_total'];
        $technician_credit_total['total']['software_total'] = $technician_credit_info['software_all_total'];
        $technician_credit_total['total']['total_technician'] = $technician_credit_info['total_technician'];
        $technician_credit_total['total']['software_group_name'] = $technician_credit_info['software_group_name'];
        
        return $technician_credit_total;
    } 
}
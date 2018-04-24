<?php
namespace Union\Credit;

/**
 * 积分管理
 * Class Manager
 * @package Union\Credit
 */

class Manager {

    protected static $_instance = null;
    /**
     * @return \Union\Credit\Manager
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 积分删除
     * @param $arr_credit_wait_confirm credit_wait_confirm表原始记录
     * @return boolean
     */
    public function delete( $arr_credit_wait_confirm ) {
        if (empty($arr_credit_wait_confirm)) return false;
        \Dao\Union\Credit_wait_confirm_delete::get_instance()->add_all( $arr_credit_wait_confirm, true);

        $arr_id = array();
        foreach ( $arr_credit_wait_confirm as $item ) {
            $arr_id[] = $item['id'];
        }
        \Dao\Union\Log_credit::get_instance()->delete('id in(' .implode(',', $arr_id) . ')' );

    }

    /**
     * 活动发积分
     * @param $user_id
     * @param $credit
     * @param $type
     * @param $sub_type
     * @param string $name
     * @param bool $direct 直接发可用
     */
    public function ad_activity_credit($user_id, $credit, $type, $sub_type, $name = '',$direct = false,$dateline=0){
        $time_now = empty($dateline) ? time() : $dateline;
        \Dao\Union\Credit_wait_confirm::get_instance()->begin_transaction();
        $ret = \Dao\Union\Credit_wait_confirm::get_instance()->add_credit($user_id, $credit, $type, $sub_type, $name);
        if (!$ret){
            \Dao\Union\Credit_wait_confirm::get_instance()->rollback();
            return false;
        }
        $data = [
            'uid'		=> $user_id,
            'type'		=> $type,
            'sub_type'	=> 1,
            'credit'	=> $credit,
            'dateline'	=> $time_now,
            'name'		=> $name,
            'ymd'		=> date('Ymd', $time_now),
            'is_get'	=> 1,
        ];
        $ret = \Dao\Union\Credit_Stat_Detail::get_instance()->add_stat($data);
        if (!$ret){
            \Dao\Union\Credit_wait_confirm::get_instance()->rollback();
            return false;
        }
        $info = \Dao\Union\Credit_Stat::get_instance()->get_day_info($user_id,$type,date("ymd",$time_now));
        if ($info){
            $data = ['credit' => $info['credit'] + $credit];
            $ret = \Dao\Union\Credit_Stat::get_instance()->update_info($info['id'],$data);
        }else{
            $data = [
                'uid' => $user_id,
                'type' => 2,
                'credit' => $credit,
                'ymd' =>  date("ymd",$time_now),
                'dateline' => $time_now,
                'is_get' => 1
            ];
            $ret = \Dao\Union\Credit_Stat::get_instance()->add_stat($data);
        }
        if (!$ret){
            \Dao\Union\Credit_wait_confirm::get_instance()->rollback();
            return false;
        }
        //直接发可以用
        if($direct){
            $ym_sign = date('ym', $time_now);
            $ymd_sign = date('Ymd', $time_now);
            $conditions = [
                "uid"=>$user_id,
                "ym"=>$ym_sign,
                "ymd"=>$ymd_sign,
                "is_get"=>0 ,
                "name"=>$name
            ];
            $sign_arr = \Dao\Union\Credit_wait_confirm::get_instance()->get_row_by_coditions($conditions);
            //直接更新积分
            $ret = \Dao\Union\User::get_instance()->add_credit($user_id,$sign_arr['credit']);
            if (!$ret){
                \Dao\Union\Credit_wait_confirm::get_instance()->rollback();
                return false;
            }
            //更新发放状态
            $ret = \Dao\Union\Credit_wait_confirm::get_instance()->update('id='.$sign_arr['id'],['is_get'=>1]);
            if (!$ret){
                \Dao\Union\Credit_wait_confirm::get_instance()->rollback();
                return false;
            }
            $data= array(
                'uid' => $user_id,
                'credit' => $sign_arr['credit'],
                'type' => $type,
                'dateline' => $time_now,
                'ym' => $ym_sign,
                'ymd' => $ymd_sign,
                'is_get' => 1,
                'name' => $name,
                'datetime' => date('Y-m-d H:i:s', $time_now)
            );
            $ret = \Dao\Union\User_Credit_Log::get_instance()->add($data);
            if (!$ret){
                \Dao\Union\Credit_wait_confirm::get_instance()->rollback();
                return false;
            }
        }
        \Dao\Union\Credit_wait_confirm::get_instance()->commit();
        return true;
    }

    public function get_type_name_list( $type = 0 ) {
        static $types;
        if (!empty($types)) return $types;
        $activity = \Dao\Union\Credit_Name_Decs_Map::get_instance()->get_map();
/*        $sign = array(
            'sign' => '签到'
        );*/
        $promotion_list = $list = \Dao\Union\Promotion::get_instance()->get_list(0);
        $software = array();
        foreach ($promotion_list as $item) {
            $software[$item['short_name']] = $item['name'];
        }
        $result = array_merge( $activity, $software );


        $types = $result;
        return $types ;
    }
    
    /**
     * 根据uid获取月份的总积分
     */
    public function get_month_credit_total_by_uid($uid, $ymd, $promotion){
    	$ym = date('ym',strtotime($ymd));
    	//导航推广量 

    	$ymd_start = date('Ym01',strtotime($ymd));
    	$ymd_end = date('Ymd',strtotime("$ymd_start +1 month -1 day"));

    	$dh_list = \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->get_month_ip_count_by_uid($uid, $ymd_start, $ymd_end);
    	$credit = \Dao\Union\Credit_wait_confirm::get_instance()->get_credit_ip_count_by_uid($uid, $ym);
    	$promotion_list = array_merge($dh_list,$credit);
    	$total_credit = 0;
    	if(!empty($promotion_list)){
    		foreach ($promotion_list as $key => $val){
    			if(isset($val['credit']) && !empty($val['credit'])){
    				$total_credit += $val['credit'];
    			}else{
    				if($promotion[$val['name']]['credit_install']>0){
    					$total_credit += $promotion[$val['name']]['credit_install']*$val['ip_count'];
    				}elseif($promotion[$val['name']]['credit_online']>0){
    					$total_credit += $promotion[$val['name']]['credit_online']*$val['ip_count'];
    				}elseif($promotion[$val['name']]['credit_rebate']>0){
    					$total_credit += $promotion[$val['name']]['credit_rebate']*$val['ip_count'];
    				}
    			}    			
    		}
    	}
		return $total_credit;
    }

    /*
     * 历史累计未发总收入 及只发有效量不发积分的软件 软件有哪些天积分未发
    * type =6 导航
    */
    public function get_soft_noincome_ymd($type=1){
        if($type==6){
            $softArr = \Dao\Union\Promotion::get_instance()->select(array('field'=>'short_name,not_credit_ymd','where'=>'type=6 and is_credit=0'));
        }else{
            $softArr = \Dao\Union\Promotion::get_instance()->select(array('field'=>'short_name,not_credit_ymd','where'=>'type<>6 and is_credit=0'));
        }
        foreach($softArr as $v){
            $d[$v['short_name']] = $v['not_credit_ymd'];
        }
        return $this->get_soft_income_credit_ymd($d);
    }

    public function get_soft_noincome_ymd_state($type=1){
        if($type==6){
            $softArr = \Dao\Union\Promotion::get_instance()->select(array('field'=>'short_name,not_credit_ymd','where'=>'type=6 and state=1 and is_credit=0'));
        }else{
            $softArr = \Dao\Union\Promotion::get_instance()->select(array('field'=>'short_name,not_credit_ymd','where'=>'type<>6 and state=1 and is_credit=0'));
        }
        foreach($softArr as $v){
            $d[$v['short_name']] = $v['not_credit_ymd'];
        }
        return $this->get_soft_income_credit_ymd($d);
    }

    public function get_soft_income_credit_ymd($softArr){
        if(empty($softArr)) return false;
        $softStr = implode("','",array_keys($softArr));
        $softStr = "'".$softStr."'";
        $res = \Dao\Union\Log_soft_credit_time::get_instance()->get_ymd($softStr);
        if(!empty($res)){
            foreach($res as $v){
               $ymdArr = $this->get_ymd_array($v['start'],$v['end']);
               if($data[$v['soft_id']]){
                   $data[$v['soft_id']] = array_merge($data[$v['soft_id']],$ymdArr);
                   $data[$v['soft_id']] = array_unique($data[$v['soft_id']]);
               }else{
                   $data[$v['soft_id']] = $ymdArr;
               }
            }
        }

        foreach($softArr as $k=>$vs){
            unset($noIncome);
            $noIncome = $this->get_ymd_array($vs,date("Ymd"));
            if(empty($data[$k])) $data[$k] = array();
            $data[$k] = array_diff($noIncome,$data[$k]);
        }
        return $data;
    }
    public function get_ymd_array($ymd_start,$ymd_end) {
        unset($list);
        if ($ymd_end < $ymd_start) return array();
        $timestamp_start    = strtotime($ymd_start);
        $timestamp_end      = strtotime($ymd_end);
        $_timestamp_start   = $timestamp_end;
        $_timestamp_end     = $timestamp_start;
        if ($_timestamp_end < $timestamp_start) $_timestamp_end = $timestamp_start;
        $list = array();
        for ($i = $_timestamp_start; $i >= $_timestamp_end; $i -= 86400) {
            $ymd = date('Ymd', $i);
            $list[] = $ymd;
        }
        unset($ymd);
        return $list;
    }

  /*
   * 取软件未发积分的有效量
   */
    public function get_soft_noincome_num($uid){
        $softArr = \Dao\Union\Promotion::get_instance()->select(array('field'=>'short_name,not_credit_ymd','where'=>'is_credit=0'));
        foreach($softArr as $v){
            $d[$v['short_name']] = $v['not_credit_ymd'];
            $where[] = "(name='{$v['short_name']}' and ymd>={$v['not_credit_ymd']})";
        }
        $credit_sql = "select name,sum(ip_count) as num from credit_wait_confirm where uid={$uid} and type=2 and credit=0 and (".implode(' or ',$where)." ) group by name";
        //总的未发积分的有效量
        $r = \Dao\Union\Credit_wait_confirm::get_instance()->query($credit_sql);
        foreach($r as $kk=>$v_n){
            $dnum[$v_n['name']] = $v_n['num'];
        }
        //已发积分的有效量
        $softStr = implode("','",array_keys($d));
        $softStr = "'".$softStr."'";
        $res = \Dao\Union\Log_soft_credit_time::get_instance()->get_ymd($softStr);
        if(!empty($res)){
            foreach($res as $v){
                $data[$v['soft_id']][] = "(ymd>={$v['start']} and ymd<={$v['end']})";
            }
        }
        foreach($data as $k=>$vv){
            $credit_sql_1 = "select sum(ip_count) as num from credit_wait_confirm where uid={$uid} and type=2 and credit=0 and (".implode(' or ',$vv)." ) and name='{$k}'";
            $r_num = \Dao\Union\Credit_wait_confirm::get_instance()->query($credit_sql_1);
            $count[$k] = $r_num?$r_num[0]['num']:0;
        }
        foreach($softArr as $v){
            $notNum[$v['short_name']] = $dnum[$v['short_name']] - $count[$v['short_name']];
        }
        return $notNum;
    }

    /*
   * 取导航未发积分的有效量
   */
    public function get_dh_noincome_num($uid){
        $softArr = \Dao\Union\Promotion::get_instance()->select(array('field'=>'short_name,not_credit_ymd','where'=>'type=6 and is_credit=0'));
        if(empty($softArr)) return array();
        foreach($softArr as $v){
            $d[$v['short_name']] = $v['not_credit_ymd'];
            $where[] = "(name='{$v['short_name']}' and ymd>={$v['not_credit_ymd']})";
        }
        $credit_sql = "select name,sum(ip_count) as num from activity_hao123_vip_num_new where uid={$uid} and (".implode(' or ',$where)." ) group by name";
        //总的未发积分的有效量
        $r = \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->query($credit_sql);
        foreach($r as $kk=>$v_n){
            $dnum[$v_n['name']] = $v_n['num'];
        }
        //已发积分的有效量
        $softStr = implode("','",array_keys($d));
        $softStr = "'".$softStr."'";
        $res = \Dao\Union\Log_soft_credit_time::get_instance()->get_ymd($softStr);
        if(!empty($res)){
            foreach($res as $v){
                $data[$v['soft_id']][] = "(ymd>={$v['start']} and ymd<={$v['end']})";
            }
        }
        foreach($data as $k=>$vv){
            $credit_sql_1 = "select sum(ip_count) as num from activity_hao123_vip_num_new where uid={$uid} and (".implode(' or ',$vv)." ) and name='{$k}'";
            $r_num = \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->query($credit_sql_1);
            $count[$k] = $r_num?$r_num[0]['num']:0;
        }
        foreach($softArr as $v){
            $notNum[$v['short_name']] = $dnum[$v['short_name']] - $count[$v['short_name']];
        }
        return $notNum;
    }
}
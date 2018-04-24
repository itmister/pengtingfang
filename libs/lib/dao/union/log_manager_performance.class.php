<?php

namespace Dao\Union;
use \Dao;
class Log_manager_performance extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Log_manager_performance
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * @param $ymd_start
     * @param $ymd_end
     * @param array $arr_channel_master_id 渠道主管id
     * @return array
     */
    public function get_list( $ymd_start, $ymd_end, $arr_channel_master_id = array() ) {
        $table_log_manager_performance = $this->_get_table_name();
        $ymd_start = $this->_get_ymd( $ymd_start );
        $ymd_end = $this->_get_ymd( $ymd_end );

        if (empty($arr_channel_master_id)) {
            $sql = "SELECT * FROM {$table_log_manager_performance} WHERE ymd between {$ymd_start} and {$ymd_end} AND channel_master_id=0";
            $arr_data = $this->query( $sql );
            return $arr_data;
        }
        else {
            $arr_result = array();
            $channel_master_id = implode(',', $arr_channel_master_id);
            $sql = "SELECT * FROM {$table_log_manager_performance} WHERE ymd between {$ymd_start} and {$ymd_end} AND channel_master_id in({$channel_master_id})";
            $arr_data = $this->query( $sql );
            foreach ( $arr_data as $item ) {
                $ymd = $item['ymd'];
                if (!isset($arr_result[$ymd])) {
                    $arr_result[$ymd] = $item;
                }
                else {
                    foreach( array_keys( $arr_result[$ymd] ) as $field) $arr_result[$ymd][$field] += $item[$field];
                }
            }
            return $arr_result;
        }
    }

    /**
     * 同步日志
     * @param $ymd_start
     * @param $ymd_end
     */
    public function sync_log( $ymd_start = 0, $ymd_end = 0 ){
        /*
         REPLACE into log_credit (id,uid,credit,type,sub_type,dateline,ym,ymd,ip_count, `name`, user_name,invite_uid,invite_user_name,area_id )
        select c.id,c.uid,c.credit,c.type,c.sub_type,c.dateline,c.ym,c.ymd,ip_count,c.`name`,
            u.name as user_name,
            m.userid as invite_uid,m.username as invite_user_name,m.areaid as area_id  from credit_wait_confirm c
            LEFT JOIN  `user` u on c.uid=u.id
            left JOIN channel_7654.`user_marketer` m on u.invitecode = m.idcode
         where c.is_get = 1 and  c.delete_flag=0 ORDER BY id;
         */
        $channel_master_list    = \Union\Manager\Channel_master::get_instance()->get_list();
        $ymd_start              = \Util\Datetime::get_ymd( $ymd_start, '-8 day', 'Ymd');
        $ymd_end                = \Util\Datetime::get_ymd( $ymd_end, ' -1 day', 'Ymd');
        $timestamp_start        = strtotime( $ymd_start );
        $timestamp_end          = strtotime( $ymd_end );
        for ( $i = $timestamp_start; $i <= $timestamp_end; $i += 86400 ) {
            $ymd = date('Ymd', $i);
            $this->_sync_log($ymd);//总
            foreach ( $channel_master_list as $item ) $this->_sync_log( $ymd, $item['uid'] );
        }
    }


    /**
     * @param $ymd
     * @param integer $channel_master_id
     * @return boolean
     */
    protected function _sync_log( $ymd, $channel_master_id = 0) {
        $ymd = intval($ymd);
        if (empty($ymd)) $ymd = date('Ymd', strtotime('-7 day'));

        $dao_log_register   = \Dao\Union\Log_register::get_instance();
        $dao_log_credit     = \Dao\Union\Log_credit::get_instance();
        $dao_user_marketer  = \Dao\Channel_7654\User_marketer::get_instance();

        //日新增市场经理
        $arr_area_id_list   =  !empty($channel_master_id) ? (\Union\Manager\Channel_master::get_instance()->get_area_id_list( $channel_master_id )) : array();
        $arr_data           = $dao_user_marketer->get_manager_newly(0, $ymd, $arr_area_id_list);
//        \io::output($dao_user_marketer->get_last_sql() );
        $arr_uid            = array();
        foreach ($arr_data as $item) {
            $arr_uid[] = $item['uid'];
        }
        $time_now           = strtotime( $ymd );
        $ymd_range          = array( $ymd, date('Ymd', strtotime('+7 day', $time_now)) );//7天内时间范围
        $manager_newly      = count($arr_uid);
        $arr_result = array(
            'ymd'                                          => date('ymd', $time_now),
            'channel_master_id'                          => $channel_master_id,
            'manager_newly'                               => $manager_newly,
        );
        if (empty( $manager_newly )) {
            return $this->add( $arr_result , true);
        }
        $arr_result['manager_active']                              = $dao_log_register->count_active(0, date('Ymd', $time_now), $arr_uid);//日活跃 manager_active
        $arr_result['manager_active_day2']                        = $dao_log_register->count_active(0, date('Ymd', strtotime('+1 day', $time_now)), $arr_uid );//次日活跃 manager_active_day2
        $arr_result['manager_active_day7']                        = $dao_log_register->count_active(0, date('Ymd', strtotime('+7 day', $time_now)), $arr_uid );//7日活跃 manager_active_day7
        $arr_result['manager_active_in_day7']                     = $dao_log_register->count_active(0, $ymd_range, $arr_uid);//7日内活跃 manager_active_in_day7
        $arr_result['technician_bind_in_day7']                    = $dao_log_register->count_invite(0, $ymd_range, $arr_uid);//7日内绑定技术员数量 technician_bind_in_day7
        $arr_result['manager_has_performance_in_day']            = $dao_log_credit->count_manager_has_performance(0, $ymd, $arr_uid);//当日有业绩市场经理 manager_has_performance_in_day
        $arr_result['manager_has_performance_day2']              = $dao_log_credit->count_manager_has_performance(0, strtotime('+1 day', $time_now), $arr_uid);//次日有业绩市场经理 manager_has_performance_day2
        $arr_result['manager_has_performance_day7']              = $dao_log_credit->count_manager_has_performance(0, strtotime('+7 day', $time_now), $arr_uid);//7日有业绩市场经理 manager_has_performance_day7
        $arr_result['manager_has_performance_in_day7']          = $dao_log_credit->count_manager_has_performance(0, $ymd_range, $arr_uid);//7日内有业绩市场经理 manager_has_performance_in_day7
        $arr_result['technician_has_performance_in_day7']       = $dao_log_credit->count_technician_has_performance(0, $ymd_range, $arr_uid);//7日内有业绩市场技术员 technician_has_performance_in_day7
        $arr_result['promotion_install_num_in_day7']             = $dao_log_credit->count_install_num(0, $ymd_range, $arr_uid);//7日内推广安装量 promotion_install_num_in_day7
        $ret  = $this->add($arr_result, true);
        return $ret;

    }

    /**
     * 年月日转换
     * @param $ymd
     * @return integer
     */
    protected function _get_ymd( $ymd ) {
        $ymd = intval($ymd);
        return $ymd > 20000000 ? ($ymd - 20000000) : $ymd;
    }
}

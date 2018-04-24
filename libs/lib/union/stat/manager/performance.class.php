<?php
namespace Union\Stat\Manager;

/**
 * 统计-经理人业绩
 * Class Performance
 * @package Union\Stat\Manager
 */

class Performance {
    
    private $arr_area_id_list;

    /**
     * 取月统计列表
     * @param array $arr_channel_master_id 渠道主管数组
     * @return array(
        array(
            ym : 年月
            manager_newly : 月度新市场经理人数
            manager_login : 当月登录的市场经理人数
            manager_active : 月度活跃市场经理人数
            manager_has_performance : 月度有业绩市场经理人数
            install_num : 月度软件安装量
            technician_has_performance : 月度有业绩技术员人数
        )
     * )
     */
    public function get_month_detail( $arr_channel_master_id = array() ) {
        $result = array();
        /*
        $struct = \Util\Data\Virtual::table(array(
            'ym' => array(),
            'manager_newly'=> array( 'default' => 0 ),
            'manager_login' => array( 'default' => 0 ),
            'manager_active' => array( 'default' => 0 ),
            'manager_has_performance'=> array( 'default' => 0 ),
            'install_num' => array( 'default' => 0 ),
            'technician_has_performance' => array( 'default' => 0 )
        ));

        foreach ( $struct as $item ) {
            $ym             = date('ym', strtotime($item['ym']));
            $result[ $ym ]  = $item;
        }
        */

        //城市id列表
        $arr_area_id_list   =  !empty($arr_channel_master_id) ? (\Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id )) : array();

        $dao_user_marketer  = \Dao\Channel_7654\User_marketer::get_instance();
        $dao_log_login      = \Dao\Channel_7654\Log_login::get_instance();
        $dao_log_register   = \Dao\Union\Log_register::get_instance();
        $dao_credit         = \Dao\Union\Log_credit::get_instance();

//        $result = array();
        $arr_data['manager_newly']                   = $dao_user_marketer->get_newly_ym_list( $arr_area_id_list );
        $arr_data['manager_login']                   = $dao_log_login->get_login_ym( $arr_area_id_list );
        $arr_data['manager_active']                  = $dao_log_register->get_manager_active_ym_group( $arr_area_id_list );
        $arr_data['manager_has_performance']        = $dao_credit->get_manager_has_performance_ym_group( $arr_area_id_list );
        $arr_data['install_num']                     = $dao_credit->get_promotion_install( $arr_area_id_list );
        $arr_data['technician_has_performance']    = $dao_credit->get_technician_has_performance( $arr_area_id_list );

        foreach ( $arr_data as $field =>  $arr_item ) foreach ( $arr_item as $ym => $value ) {
            if (!isset($result[$ym])) $result[$ym] = array();
            $result[$ym][$field] = $value;
        }
        foreach ( $result as $key => $item ) $result[$key]['ym'] = date('Y-m', strtotime('20' . $key . '01'));
        krsort( $result );
//        print_r($result);
        return $result;

    }

    /**
     * get_manager_active_total
     * 取总活跃经理人数
     * @param array $arr_channel_master_id
     * @param int $ym
     * @param int $ymd
     * @param string $user_name
     * @return int
     */
    public function get_manager_active_total( $arr_channel_master_id = array(), $ym = 0, $ymd = 0, $user_name = '' ) {
        $dao_log_register   = \Dao\Union\Log_register::get_instance();
        //城市id列表
        $arr_area_id_list   =  !empty($arr_channel_master_id) ? (\Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id )) : array();
        return $dao_log_register->get_manager_active_total( $arr_area_id_list, $ym, $ymd, $user_name );
    }

    /**
     * 取活跃经理人列表
     * @param array $arr_channel_master_id 渠道主管数组
     * @param int $ym 限定月份，默认不限 1503
     * @param int $ymd
     * @param int $row_begin 开始行
     * @param int $num 数量
     * @param string $user_name
     * @return array
        uid1 : array(
            uid : 装机员id
            user_name : 装机员帐号名
            phone : 装机员手机
            technician_num : 绑定技术员历史总量
            technician_has_performance ：有业绩技术员历史总量
            install_num ：软件安装历史总量
            //ym 指定时
            month_technician_num : 当月绑定技术员总量
            month_technician_has_performance ：当月有业绩技术员总量
            month_install_num ：当月软件安装总量
            //ymd 指定时
            day_technician_num : 当日绑定技术员总量
            day_technician_has_performance ：当日有业绩技术员总量
            day_install_num ：当日软件安装总量
        )
        ...
     )
     */
    public function get_manager_active_list( $arr_channel_master_id = array(), $ym = 0,  $ymd, $row_begin = 0, $num = 10, $user_name = '') {
        $dao_log_register   = \Dao\Union\Log_register::get_instance();
        //城市id列表
        $arr_area_id_list   = !empty($arr_channel_master_id) ? (\Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id )) : array();
        $result             = $dao_log_register->get_manager_active_list( $arr_area_id_list, $ym, $ymd, $row_begin, $num, $user_name );

//        \Lib\Core::dead( $dao_log_register->get_last_sql() );
        $this->_fill_performance_detail( $result );
        if (!empty($ym)) $this->_fill_performance_detail( $result, $ym );
        if (!empty($ymd)) $this->_fill_performance_detail( $result, 0, $ymd );
        return $result;
    }


    /**
     * get_manager_has_performance_total
     * 取有业绩经理人数
     * @param array $arr_channel_master_id 渠道主管数组
     * @param int $ym 限定月份，默认不限
     * @param int $ymd 限定年月日，默认不限
     * @param array $arr_promotion_short_name_list 推广软件short_name 数组
     * @param string $user_name
     * @return integer
     */
    public function get_manager_has_performance_total(  $arr_channel_master_id = array(), $ym = 0, $ymd = 0, $arr_promotion_short_name_list = array(), $user_name = '' ) {
        $dao_credit         =  \Dao\Union\Log_credit::get_instance();
        $ym                 = intval($ym);
        $ymd                = intval($ymd);
        $arr_area_id_list   =  !empty($arr_channel_master_id) ? (\Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id )) : array();
        return $dao_credit->get_manager_has_performance_total( $arr_area_id_list, $ym, $ymd, $arr_promotion_short_name_list, $user_name );
    }

    /**
     * 取有业绩经理列表
     * @param array $arr_channel_master_id 渠道主管数组
     * @param int $ym 限定月份，默认不限
     * @param int $ymd 限定年月日
     * @param array $arr_promotion_short_name_list 推广软件short_name 数组
     * @param int $row_begin 开始行
     * @param int $num 数量
     * @param string $user_name
     * @return array
        uid1 : array(
            uid : 装机员id
            user_name : 装机员帐号名
            phone : 装机员手机
            technician_num : 绑定技术员历史总量
            technician_has_performance ：有业绩技术员历史总量
            install_num ：软件安装历史总量
            //ym 指定时
            month_technician_num : 当月绑定技术员总量
            month_technician_has_performance ：当月有业绩技术员总量
            month_install_num ：当月软件安装总量
            //ymd 指定时
            day_technician_num : 当日绑定技术员总量
            day_technician_has_performance ：当日有业绩技术员总量
            day_install_num ：当日软件安装总量
        )
        ...
        ）
     */
    public function get_manager_has_performance_list( $arr_channel_master_id = array(), $ym = 0, $ymd = 0, $arr_promotion_short_name_list, $row_begin = 0, $num = 10, $user_name = '' ) {
        $ym                 = intval($ym);
        $ymd                = intval($ymd);

        $dao_credit         =  \Dao\Union\Log_credit::get_instance();
        $arr_area_id_list   =  !empty($arr_channel_master_id) ? (\Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id )) : array();
        $result = $dao_credit->get_manager_has_performance_list( $arr_area_id_list, $ym, $ymd, $arr_promotion_short_name_list, $row_begin, $num, $user_name );

        $this->_fill_performance_detail( $result, 0, 0, $arr_promotion_short_name_list);
        if ( !empty($ym) ) $this->_fill_performance_detail( $result, $ym, 0, $arr_promotion_short_name_list );
        if ( !empty($ymd) ) $this->_fill_performance_detail( $result, 0, $ymd, $arr_promotion_short_name_list);
        return $result;
    }

    /**
     * get_manager_total
     * 取经理人总数
     * @param array $arr_channel_master_id 渠道主管数组
     * @return integer
     */
    public function get_manager_total( $arr_channel_master_id = array() ) {
        $dao_user_marketer  = \Dao\Channel_7654\User_marketer::get_instance();
        $arr_area_id_list   =  !empty($arr_channel_master_id) ? (\Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id )) : array();
        return $dao_user_marketer->get_manager_total( $arr_area_id_list );
    }

    /**
     * 取经理人列表
     * @param array $arr_channel_master_id 渠道主管数组
     * @param int $row_begin 开始行
     * @param int $num 数量
     * @return array(
        array(
            uid : 推广市场经理id
            user_name : 推广市场经理帐号,
            phone : 推广市场经理绑定手机
            technician_num : 绑定技术员历史总量
            technician_has_performance ：有业绩技术员历史总量
            install_num ：软件安装历史总量
        )
     * )
     */
    public function get_manager_list( $arr_channel_master_id = array(), $row_begin = 0, $num = 10 ) {
        /*
        $struct = \Util\Data\Virtual::table( array(
            'user_name'=> array( 'default' => 0 ),
            'phone' => array( 'default' => 0 ),
            'technician_num' => array( 'default' => 0 ),
            'technician_has_performance'=> array( 'default' => 0 ),
            'install_num' => array( 'default' => 0 ),
        ) );
        return $struct;
        */
        //城市id列表
        $arr_area_id_list   =  !empty($arr_channel_master_id) ? ( \Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id ) ) : array();
        $dao_user_marketer = \Dao\Channel_7654\User_marketer::get_instance();
        $list = $dao_user_marketer->get_manager_list( $arr_area_id_list, $row_begin, $num );
        return $list;

    }

    /**
     * 取日市场经理业绩明细
     * @param array $arr_channel_master_id 渠道主管数组
     * @param int $ymd_start 开始年月日，默认8天前
     * @param int $ymd_end 结束年月日，默认8天前
     * @return array
     * @return array(
        array(
            ymd : 年月日
            manager_newly : 当日新市场经理人数
            manager_login : 当月登录的市场经理人数
            manager_active : 当日活跃市场经理人数
            manager_has_performance : 当日有业绩市场经理人数
            install_num : 当日软件安装量
            technician_has_performance : 当日有业绩技术员人数
        )
        ...
     )
     */
    public function get_day_detail( $arr_channel_master_id = array(), $ymd_start = 0, $ymd_end = 0 ) {
        $ymd_start  = \Util\Datetime::get_ymd( $ymd_start, '-8 day', 'ymd');
        $ymd_end    = \Util\Datetime::get_ymd( $ymd_end, ' -1 day', 'ymd');

        if ( $ymd_end - $ymd_start > 1000 ) $ymd_end = date('ymd', strtotime($ymd_start) + 86400 * 100 );
        $result = array();
        /*
        $struct = \Util\Data\Virtual::table(array(
            'ymd' => array(),
            'manager_newly'=> array( 'default' => 0 ),
            'manager_login' => array( 'default' => 0 ),
            'manager_active' => array( 'default' => 0 ),
            'manager_has_performance'=> array( 'default' => 0 ),
            'install_num' => array( 'default' => 0 ),
            'technician_has_performance' => array( 'default' => 0 )
        ));
        foreach ( $struct as $item ) {
            $ymd             = date('ymd', strtotime($item['ymd']));
            $result[ $ymd ]  = $item;
        }
        */
        //城市id列表
        $arr_area_id_list   =  !empty($arr_channel_master_id) ? (\Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id )) : array();

        $dao_user_marketer  = \Dao\Channel_7654\User_marketer::get_instance();
        $dao_log_login      = \Dao\Channel_7654\Log_login::get_instance();
        $dao_log_register   = \Dao\Union\Log_register::get_instance();
        $dao_credit         = \Dao\Union\Log_credit::get_instance();

//        $result = array();
        $arr_data['manager_newly']                   = $dao_user_marketer->get_newly_list_group_ymd( $arr_area_id_list, $ymd_start,  $ymd_end );
        $arr_data['manager_login']                   = $dao_log_login->get_login_ymd( $arr_area_id_list, $ymd_start, $ymd_end );
        $arr_data['manager_active']                  = $dao_log_register->get_manager_active_group_ymd(  $arr_area_id_list, $ymd_start, $ymd_end  );

        //@todo log_credit ymd8位年月日修改为6位年月日
        $ymd_start  = date('Ymd', strtotime('20' . $ymd_start ));
        $ymd_end    = date('Ymd', strtotime('20' . $ymd_end ));
        $arr_data['manager_has_performance']        = $dao_credit->get_manager_has_performance_group_ymd( $arr_area_id_list, $ymd_start, $ymd_end );
        $arr_data['install_num']                     = $dao_credit->get_promotion_install_group_ymd( $arr_area_id_list, $ymd_start, $ymd_end );
        $arr_data['technician_has_performance']    = $dao_credit->get_technician_has_performance_group_ymd( $arr_area_id_list, $ymd_start, $ymd_end );

        foreach ( $arr_data as $field =>  $arr_item ) foreach ( $arr_item as $ym => $value ) {
            $ymd_field = ($ym - 20000000) > 0 ? ($ym - 20000000) : $ym;
            if (!isset($result[$ymd_field])) $result[$ymd_field] = array();
            $result[$ymd_field][$field] = $value;
        }

        foreach ( $result as $key => $item ) $result[$key]['ymd'] = date('Y-m-d', strtotime('20' . $key));
//        print_r($result);
        krsort( $result );
        return $result;
    }

    /**
     * 取日市场经理统计
     * @param array $arr_channel_master_id 渠道主管id
     * @param int $ymd_start 开始年月日 默认8天前
     * @param int $ymd_end 结束年月日 默认1天前
     * @return array
     */
    public function get_manager_newly_stat( $arr_channel_master_id = array(), $ymd_start = 0, $ymd_end = 0 ) {

        $dao_log_manager_performance = \Dao\Union\Log_manager_performance::get_instance();
        $ymd_start                   = \Util\Datetime::get_ymd( $ymd_start, '-8 day', 'ymd');
        $ymd_end                     = \Util\Datetime::get_ymd( $ymd_end, ' -1 day', 'ymd');
        $result                      = $dao_log_manager_performance->get_list( $ymd_start, $ymd_end, $arr_channel_master_id );
        krsort( $result );
        return $result;
        /**
        if ( $ymd_end - $ymd_start > 1000 ) $ymd_end = date('ymd', strtotime($ymd_start) + 86400 * 100 );
        $result = array();

        $struct = \Util\Data\Virtual::table( array(
            'ymd'                                   => array(),//年月日
            'manager_newly'                        => array( 'default' => 0 ),//当日新市场经理人数
            'manager_active'                       => array( 'default' => 0 ),//当日活跃市场经理人数
            'manager_active_day2'                 => array( 'default' => 0 ),//次日活跃市场经理人数
            'manager_active_day7'                 => array( 'default' => 0 ),//7日活跃市场经理人数
            'manager_active_day7_rate'           => array( 'default' => 0 ),//7日活跃率
            'manager_active_in_day7'             => array( 'default' => 0 ),//7日内活跃市场经理人数
            'technician_bind_in_day7'            => array( 'default' => 0 ),//7日内绑定技术员总量
            'manager_has_performance_in_day'    => array( 'default' => 0 ),//当日有业绩市场经理人数
            'manager_has_performance_day2'      => array( 'default' => 0 ),//次日有业绩市场经理人数
            'manager_has_performance_day7'      => array( 'default' => 0 ),//7日有业绩市场经理人数
            'manager_has_performance_in_day7'  => array( 'default' => 0 ),//7日内有业绩市场经理人数
            'technician_has_performance_in_day7' => array( 'default' => 0 ),//7日内活跃市场经理人数
            'promotion_install_num_in_day7'      => array( 'default' => 0 ),//7日内推广安装量
        ));
        foreach ( $struct as $item ) {
            $ymd             = date('ymd', strtotime($item['ymd']));
            $result[ $ymd ]  = $item;
        }

        return $result;
        //城市id列表
        $arr_area_id_list   =  !empty($arr_channel_master_id) ? (\Union\Manager\Channel_master::get_instance()->get_area_id_list( $arr_channel_master_id )) : array();
        return $result;
         *
         */
    }




    /**
     * 填充业绩明细,绑定的技术员总量\绑定的技术员总量\软件安装历史总量
     * @param array $result 技术员id为索引的数组
        uid1 : array
        uid2 : array
     * @param int $ym 年月
     * @param int $ymd 年月日
     * @param array $arr_promotion_short_name_list 推广软件short_name 数组
     * @return mixed
     */
    protected function _fill_performance_detail( &$result, $ym = 0, $ymd = 0 , $arr_promotion_short_name_list = array() ){

        $ym             = intval( $ym );
        $arr_uid        = array_keys( $result );

        $field_prefix   = !empty( $ymd ) ? 'day_' : ( (!empty($ym)) ? 'month_' : '' );
        //绑定的技术员总量
        $arr_technician = \Dao\Union\Log_register::get_instance()->get_technician_total_by_manager_id_list( $arr_uid, $ym, $ymd, $arr_promotion_short_name_list);
        $field = $field_prefix . 'technician_num';
        foreach ($arr_technician as $manager_id => $technician_num ) $result[ $manager_id ][ $field ] = $technician_num;

        //有业绩技术员总量
        $arr_technician = \Dao\Union\Log_credit::get_instance()->get_technician_has_performance_total_by_manager_id_list( $arr_uid, $ym, $ymd, $arr_promotion_short_name_list);
        $field = $field_prefix . 'technician_has_performance';
        foreach ($arr_technician as $manager_id => $technician_num ) $result[ $manager_id ][ $field ] = $technician_num;

        //软件安装历史总量
        $arr_install_list = \Dao\Union\Log_credit::get_instance()->get_promotion_install_total_by_uid_list( $arr_uid,  $ym, $ymd, $arr_promotion_short_name_list);
        $field = $field_prefix . 'install_num';

        if ( !empty($ym) ) {
            //本月同比上月
        }

        foreach ($arr_install_list as $uid => $technician_num ) $result[ $uid ][ $field ] = $technician_num;

    }

}
<?php
namespace Dao\Channel_7654;
use \Dao;
class User_marketer extends Channel_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Channel_7654\User_marketer
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 取新增市场经理，按月分组
     * @param array $arr_area_id_list 市场id数组
     * @return array
     */
    public function get_newly_ym_list( $arr_area_id_list = array() ) {
        /*
         select count(*) as newly, choose_area_ym, areaid as area_id from user_marketer
         WHERE choose_area_ym > 0
         GROUP BY choose_area_ym
         */
        $table_name = $this->_get_table_name();

        $where_area_id = ( !empty($arr_area_id_list) && is_array($arr_area_id_list) ) ? ( ' AND areaid in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $sql = "select count(*) as newly, choose_area_ym, areaid as area_id from {$table_name}
         WHERE choose_area_ym > 0 {$where_area_id}
         GROUP BY choose_area_ym";
//        echo $sql;die();
        $arr_data = $this->query($sql);
        $result = array();
        foreach ($arr_data as $item ) {
           $result[ $item['choose_area_ym'] ] = $item['newly'];
        }
        return $result;
    }

    /**
     * @param array $arr_area_id_list 城市id列表
     * @param $ymd_start 开始日期
     * @param $ymd_end 结束日期
     * @return mixed
     */
    public function get_newly_list_group_ymd( $arr_area_id_list = array(), $ymd_start, $ymd_end ) {
        $table_name = $this->_get_table_name();
        $where_area_id = ( !empty($arr_area_id_list) && is_array($arr_area_id_list) ) ? ( ' AND areaid in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $sql = "select count(*) as newly, choose_area_ymd from {$table_name}
         WHERE choose_area_ymd >= {$ymd_start} and choose_area_ymd <= {$ymd_end} {$where_area_id}
         GROUP BY choose_area_ymd";

        $arr_data = $this->query($sql);

        foreach ($arr_data as $item ) {
            $result[ $item['choose_area_ymd'] ] = $item['newly'];
        }
        return $result;
    }


    /**
     * 取市场经理总人数
     * @param array $arr_area_id_list
     * @return integer
     */
    public function get_manager_total( $arr_area_id_list = array() ) {
        $table_name = $this->_get_table_name();

        $where_area_id = ( !empty($arr_area_id_list) && is_array($arr_area_id_list) ) ? ( ' AND areaid in (' . implode(',', $arr_area_id_list) . ') ' ) : '';

        $sql = "select count(*) as manager_num from {$table_name}
         WHERE is_stat_manager > 0  {$where_area_id}";
        $arr_data = current( $this->query( $sql ) );
        return intval( $arr_data['manager_num']);
    }


    /**
     * 取市场经理列表
     * @param array $arr_area_id_list
     * @param int $row_begin 开始行
     * @param int $num 数量
     * @return array
     */
    public function get_manager_list( $arr_area_id_list = array(),  $row_begin = 0, $num = 10 ) {
        /**
        uid : 推广市场经理id
        user_name : 推广市场经理帐号,
        phone : 推广市场经理绑定手机
        technician_num : 绑定技术员历史总量
        technician_has_performance ：有业绩技术员历史总量
        install_num ：软件安装历史总量
         */
        $where_area_id = ( !empty($arr_area_id_list) && is_array($arr_area_id_list) ) ? ( ' AND m.areaid in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $table_user_marketer    = $this->_get_table_name();
        $table_union_user       = $this->_get_table_name('user', \Lib\Core::config('db_union'));
        $sql = "SELECT m.userid as uid, m.username as user_name, u.phone
        FROM {$table_user_marketer} m
        LEFT JOIN {$table_union_user} u on m.userid=u.id
        WHERE m.is_stat_manager > 0  {$where_area_id}
        LIMIT {$row_begin},{$num}";

//        echo $sql;
//        echo $this->get_error();
        $arr_data = $this->query(  $sql );
        $result   = array();
        if (empty($arr_data)) return $result;
        foreach ($arr_data as $item ) $result[$item['uid'] ] = $item;
        $arr_uid        = array_keys( $result );

        //绑定的技术员总量
        $arr_technician = \Dao\Union\Log_register::get_instance()->get_technician_total_by_manager_id_list( $arr_uid );
        foreach ($arr_technician as $manager_id => $technician_num ) $result[ $manager_id ]['technician_num'] = $technician_num;

        //有业绩技术员总量
        $arr_technician = \Dao\Union\Log_credit::get_instance()->get_technician_has_performance_total_by_manager_id_list( $arr_uid );
        foreach ($arr_technician as $manager_id => $technician_num ) $result[ $manager_id ]['technician_has_performance'] = $technician_num;

        //软件安装历史总量
        $arr_install_list = \Dao\Union\Log_credit::get_instance()->get_promotion_install_total_by_uid_list( $arr_uid );
        foreach ($arr_install_list as $uid => $technician_num ) $result[ $uid ]['install_num'] = $technician_num;
        return $result;
    }

    /**
     * 取新增市场经理
     * @param int $ym
     * @param int $ymd
     * @param array $arr_area_id_list
     * @return array
     */
    public function get_manager_newly( $ym = 0, $ymd = 0,  $arr_area_id_list = array()) {
        $table_user_marketer    = $this->_get_table_name();
        $arr_where              = array();
        if (!empty($ymd)) {
            $arr_where['ymd'] = 'choose_area_ymd=' . $this->_get_ymd($ymd);
        }
        elseif ( !empty($ym)) {
            $arr_where['ym'] = 'choose_area_ym=' . $ym;
        }
        if ( !empty($arr_area_id_list) ) {
            $arr_where['areaid']          = ( !empty($arr_area_id_list) && is_array($arr_area_id_list) ) ? ( 'areaid in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        }
        $where                  = !empty($arr_where) ? (' WHERE ' . implode(' AND ', $arr_where) ) : '';

        $sql                    = "SELECT userid as uid,username as user_name
         FROM {$table_user_marketer}
         {$where} ";
        $arr_list               = $this->query( $sql );
        return !empty($arr_list) ? $arr_list : array();

    }

    protected function _get_ymd( $ymd ) {
        return ( $ymd > 20000000 ) ?  ($ymd - 20000000) : $ymd;
    }
    
     /**
     * 取所有市场经理的信息 
     * @param 取所有市场经理 = areaid > 0 or refer_type = 2
     * @return array
     */
    public function get_user_marketer_list() {
        $table_name = $this->_get_table_name();        
        $sql = "select userid,username from {$table_name}
         WHERE areaid > 0 or refer_type=2";
        $arr_data = $this->query( $sql );
        return $arr_data ? $arr_data : array();
    }

    /**
     * 获取市场经理信息
     * @param $userid
     * @param $idcode
     * @return array
     */
    public function user_marketer_info($userid,$idcode = ''){
        if($userid){
            $sql = "select * from {$this->_get_table_name()} WHERE userid={$userid} limit 1";
        }else{
            $sql = "select * from {$this->_get_table_name()} WHERE idcode='{$idcode}' limit 1";
        }
        $arr_data = $this->query($sql);
        return $arr_data[0] ? $arr_data[0] : array();
    }
}

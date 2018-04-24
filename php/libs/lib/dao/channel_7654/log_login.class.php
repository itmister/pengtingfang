<?php
namespace Dao\Channel_7654;
use \Dao;
class Log_login extends Channel_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Channel_7654\Log_login
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 添加登录记录
     * @param array $arr_user_info 用户信息
        uid:用户id
        area_id:所在城市id
     * @return bool|int|string|void
     */
    public function add( $arr_user_info ) {
        if ( empty($arr_user_info['uid']) ) return false;
        $now = time();
        $data = array(
            'uid' => $arr_user_info['uid'],
            'area_id' => $arr_user_info['area_id'],
            'is_stat_manager' => $arr_user_info['is_stat_manager'],
            'dateline' => $now,
            'ymd' => date('ymd', $now),
            'ym' => date('ym', $now)
        );
        try {
            return parent::add($data);
        }
        catch (\Exception $e) {
            return false;
        }

    }

    /**
     * 取月登录统计列表
     * @param array $arr_area_id_list
     * @return array
     */
    public function get_login_ym( $arr_area_id_list = array() ) {
        /*
         select count(DISTINCT uid) as login,ym from log_login
         WHERE area_id > 0
         GROUP BY ym
         */
        $table_name = $this->_get_table_name();

        $where_area_id = ( !empty($arr_area_id_list) && is_array($arr_area_id_list) ) ? ( ' AND area_id in (' . implode(',', $arr_area_id_list) . ') ' ) : '';

        $sql = "select count(DISTINCT uid) as login,ym from {$table_name}
         WHERE is_stat_manager > 0 {$where_area_id}
         GROUP BY ym";
//        echo $sql;die();
        $arr_data = $this->query($sql);
        $result = array();
        foreach ($arr_data as $item ) {
            $result[ $item['ym'] ] = $item['login'];
        }
        return $result;
    }

    /**
     * 取日登录统计列表
     * @param array $arr_area_id_list
     * @param $ymd_start
     * @param $ymd_end
     * @return array
     */
    public function get_login_ymd( $arr_area_id_list = array(), $ymd_start, $ymd_end ) {
        $table_name = $this->_get_table_name();

        $where_area_id = ( !empty($arr_area_id_list) && is_array($arr_area_id_list) ) ? ( ' AND area_id in (' . implode(',', $arr_area_id_list) . ') ' ) : '';

        $sql = "select count(DISTINCT uid) as login,ymd from {$table_name}
         WHERE is_stat_manager > 0 AND ymd >='{$ymd_start}' AND ymd <='{$ymd_end}'
         {$where_area_id}
         GROUP BY ymd";
//        echo $sql;die();
        $arr_data = $this->query($sql);
        $result = array();
        foreach ($arr_data as $item ) {
            $result[ $item['ymd'] ] = $item['login'];
        }
        return $result;
    }

}

<?php
namespace Union\Package;

/**
 * 推广软件安装包分配管理
 * Class Assign
 * @package Union\Package
 */

class Assign {

    /**
     * 设置限制分配安装包的软件列表
     * @param $arr_software_list
     */
    public function set_limit_software( $arr_software_list ) {
        $obj_redis = \Union\Service\Redis\Main::get_instance();
        $cache_key = \Union\Service\Redis\Main::key_cache_package_assign_limit_software;
        $obj_redis->set( $cache_key, $arr_software_list);
    }

    /**
     * 取限制分配安装包的软件列表
     * @return bool|string
     */
    public function get_limit_software() {
        $obj_redis = \Union\Service\Redis\Main::get_instance();
        $cache_key = \Union\Service\Redis\Main::key_cache_package_assign_limit_software;
        return $obj_redis->get( $cache_key );
    }

    /**
     * 取特定的软件可下载的用户id列表
     * @param $software
     * @return array
     */
    public function get_available_user( $software ) {
        $result = array();
        if (empty($software)) return $result;
        $obj_redis = \Union\Service\Redis\Main::get_instance();
        $cache_key = $obj_redis->get_key(\Union\Service\Redis\Main::key_cache_package_assign_available_user, array('software' => $software));
        $data = $obj_redis->get( $cache_key );
        return $data;

    }

    /**
     * 设置指定的软件可下载的用户
     * @param $software
     * @param $arr_user_list
     * @return boolean
     */
    public function set_available_user( $software, $arr_user_list ) {
        if ( empty($software) ) return false;
        $obj_redis = \Union\Service\Redis\Main::get_instance();
        $cache_key = $obj_redis->get_key(\Union\Service\Redis\Main::key_cache_package_assign_available_user, array('software' => $software));
        return $obj_redis->set( $cache_key, $arr_user_list );
    }

}
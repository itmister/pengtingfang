<?php
namespace Union\Manager;

/**
 * 市场经理渠道主管
 * Class Channel_master
 * @package Union\Manager
 */

class Channel_master {

    protected static $_instance = null;

    /**
     * @return \Union\Manager\Channel_master
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 取渠道主管列表
     * @return array
     */
    public function get_list() {
        $dao_admin = \Dao\Channel_7654\Admin::get_instance();
        $list = $dao_admin->get_channel_master_list();
        return $list;
    }

    /**
     * 渠道主管管辖城市id列表
     * @param $channel_master_id
     * @return array
     */
    public function get_area_id_list( $channel_master_id ) {

        if ( empty($channel_master_id) ) return array();
        $result = array();
        $dao_admin  = \Dao\Channel_7654\Area_admin::get_instance();
        if ( is_array( $channel_master_id )){
            //数组
            foreach ($channel_master_id as $id ) {
                $list = $dao_admin->get_channel_master_area_list( $id );
                $result = array_merge( $result, $list );
            }
            return $result;
        }

        $result       = $dao_admin->get_channel_master_area_list( $channel_master_id );
        return $result ? $result : array();
    }
}
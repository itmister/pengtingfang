<?php
namespace Dao\Channel_7654;
use \Dao;
class Area_admin extends Channel_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Channel_7654\Area_admin
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 取渠道主管管辖的城市id列表
     * @param integer $channel_master_id
     * @return array
     */
    public function get_channel_master_area_list( $channel_master_id ) {

        $table_name = $this->_get_table_name();
        $sql = "select area_id from {$table_name} where admin_id='{$channel_master_id}'";
        $arr_data = $this->query($sql);
        $result = array();
        foreach ($arr_data as $item ) $result[] =  $item['area_id'];
        return $result;

    }

    /**
     * 根据area id 获取渠道主管
     * @param $area_id
     * @return array
     */
    public function  get_channel_master_by_area_id($area_id){
        $sql = "select * from {$this->_get_table_name()} where area_id='{$area_id}' limit 1";
        $arr_data = $this->query($sql);
        return $arr_data ? current($arr_data) : [];
    }
}

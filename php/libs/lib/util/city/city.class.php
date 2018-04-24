<?php
/**
 * Created by vl
 * Description :
 * Date: 2015/7/15
 * Time: 17:23
 */
namespace Util\City;
class City {

    protected static $_instance = null;

    protected $_city_list = [];

    /**
     * @return City
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     *
     */
    public function get_list() {
        return $this->_get_list();
    }

    public function get_province_id( $city_id ) {
        foreach ( $this->_get_list() as $province_id =>  $province_info )  if ( isset($province_info['children'][$city_id])) return $province_id;
    }


    public function get_city_province_map() {
        $result = [];
        foreach ( $this->_get_list() as $province_id =>  $province_info ) foreach ($province_info['children'] as $city ) $result[$city['id']] = $province_id;
        return $result;
    }

    protected function _get_list() {

        if (empty($this->_city_list)) {
            $data_file = dirname(__FILE__) . '/city.json';
            $this->_city_list = json_decode( file_get_contents($data_file), true );
        }
        return $this->_city_list;

    }

}
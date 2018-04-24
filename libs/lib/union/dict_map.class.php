<?php
namespace union;

/**
 * 数据字典
 * Class dict_map
 * @package union
 */

class dict_map extends \Core\Object {

    const CATEGORY_PROMOTE = 'promote';//推广 软件、导航、活动等名称

    protected $_data = [];

    /**
     * @param array $option
     * @return dict_map
     */
    public static function i( $option = [] ) { return parent::i( $option ); }


    /**
     * 取数据字典
     * @param string $category 分类
     * @param $key
     * @return mixed
     */
    public function get( $category, $key = null ) {
        if (empty($category)) return '';

        if ( empty( $this->_data[$category]) ) $this->_data_init( $category );
        if ( !isset($key) ) return isset( $this->_data[$category] ) ? $this->_data[$category] : [];
        return isset( $this->_data[$category][$key] ) ? $this->_data[$category][ $key ] : '';

    }

    /**
     * 数据初始化
     * @param $category
     */
    protected function _data_init( $category ) {
        switch ($category) {
            case self::CATEGORY_PROMOTE :
                $activity_list = [];
                foreach ( \Dao\Union\Credit_Name_Decs_Map::get_instance()->all() as $row ) $activity_list[$row['name']] = $row['desc'];
                $software_list = [];
                foreach ( \Union\Promotion::get_instance()->software_list(null) as $row ) $software_list[$row['short_name']] = $row['name'];
                $this->_data[ $category ] = array_merge( $activity_list, $software_list );
                break;
        }
    }

}
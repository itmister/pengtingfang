<?php
namespace Union\Activity;

/**
 * 活动基类
 * Class Base
 * @package Union\Activity
 */
class Base {

    protected $_config = [];

    public function __construct( $config = [] ) {
        $this->config( $config );
    }

    /**
     * 取或设置配置
     * @param array $config
     * @return array
     */
    public function config( $config = [] ) {
        if (!empty($config)) $this->_config = array_merge( $this->_config, $config);
        return $this->_config;
    }

    /**
     * 当前活动是否进行中
     * @return bool
     */
    public function is_available() {

        $ymd            = date('Ymd');
        $time_available = $ymd >= $this->_config['sdate'] && $ymd <= $this->_config['edate'];

        return $time_available;
    }
}
<?php
namespace Union\Service\Redis;

/**
 * 7654主redis服务
 * Class Main
 * @package Union\Service\Redis
 */

class Main extends \Redis{

    /**
     * 推广软件分配到安装包的用户id列表
     */
    const key_cache_package_assign_available_user = 'cache:package_assign_available_user:$software';

    const key_cache_package_assign_limit_software = 'cache_package_assign_limit_software';

    /**
     * @var Main
     */
    protected static $_obj_instance = null;

    /**
     * @return Main
     */
    public static function get_instance() {

        if (empty(self::$_obj_instance)) {
            $obj_instance = new self();
            $cfg = \Lib\Core::config('redis');
            $obj_instance->connect($cfg['host'], $cfg['port']);
            $obj_instance->setOption( \Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP );
            $obj_instance->select( intval($cfg['db']) );
            if ( !empty($cfg['prefix']) ) $obj_instance->setOption( \Redis::OPT_PREFIX, $cfg['prefix'] );
            //@todo
            $obj_instance->setOption( \Redis::OPT_READ_TIMEOUT, -1 );
            self::$_obj_instance = $obj_instance;
        }
        return self::$_obj_instance;

    }

    /**
     * 取redis的key,替换变量
     * @param $pattern
     * @param $arr_values
     * @return string
     */
    public function get_key( $pattern, $arr_values = array() ) {
        if ( empty($arr_values) || !is_array($arr_values) ) return $pattern;
        $arr_search = array();

        foreach ( $arr_values as $key => $value ) $arr_search[] = '$' . $key;
        return str_replace( $arr_search, array_values($arr_values), $pattern);

    }
}
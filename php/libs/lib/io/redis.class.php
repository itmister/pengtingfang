<?php
namespace io;

/**
 * 文件操作
 * @Author vl
 * @Date 2015-03-11
 *
 * Class Redis
 * @package io
 */
class Redis  extends \Redis{

    protected static $_instance_list = [];

    /**
     * @param $key
     * @return Redis
     */
    public static function i( $key ) {
        if (isset(self::$_instance_list[$key])) return self::$_instance_list[$key];
        $cfg = \Config::get($key);
        if ( empty($cfg) || empty($cfg['host']) || empty($cfg['port']) ) throw new \Exception('redis_config_empty:' . $key );

        $obj_instance = new self();
        $obj_instance->connect($cfg['host'], $cfg['port']);
        $obj_instance->setOption( \Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP );
        $obj_instance->select( intval($cfg['db']) );
        if ( !empty($cfg['prefix']) ) $obj_instance->setOption( \Redis::OPT_PREFIX, $cfg['prefix'] );
        //@todo
        $obj_instance->setOption( \Redis::OPT_READ_TIMEOUT, -1 );

        self::$_instance_list[$key] = $obj_instance;
        return $obj_instance;
    }

}
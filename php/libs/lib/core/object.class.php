<?php
/**
 * top class
 */
namespace Core;
class Object {

    public static $_instance_list = [];

    /**
     * 单例支持
     * @param array $option
     * @return mixed
     */
    public static function i( $option = []) {
        $class_current = get_called_class();
        if ( empty( self::$_instance_list[ $class_current ]) ) self::$_instance_list[ $class_current ] = new $class_current($option);
        return self::$_instance_list[$class_current];
    }
}
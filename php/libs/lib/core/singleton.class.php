<?php
/**
 * Created by vl
 * Description : 单例模式-特性支持
 * Date: 2015/11/4
 * Time: 11:07
 */
namespace Core;
trait Singleton {

    public static $_instance_list = [];

    /**
     * @param array $option
     * @return mixed
     */
    public static function i( $option = []) {
        $class_current = get_called_class();
        if ( empty( self::$_instance_list[ $class_current ]) ) self::$_instance_list[ $class_current ] = new $class_current($option);
        return self::$_instance_list[$class_current];
    }

}
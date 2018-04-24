<?php
/**
 * Created by vl
 * Description :
 * 单例类基类
 * public static function get_instance() { return parent::get_instance(); }
 *
 * Date: 2015/10/12
 * Time: 10:26
 */
namespace Core;

class Single {

    protected static $_instance_list = [];

    public static function get_instance(){
        $class_now = get_called_class();
        return empty(self::$_instance_list[$class_now])
            ? ( self::$_instance_list[$class_now] = new $class_now($class_now) ) : self::$_instance_list[$class_now];
    }
}
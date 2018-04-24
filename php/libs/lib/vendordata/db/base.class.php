<?php
namespace VendorData\DB;
abstract class Base {
    private static $_instace = [];
    
    public static function get_instance($class = __CLASS__){
        if(isset(self::$_instace[$class])){
            return self::$_instace[$class];
        }else {
            self::$_instace[$class]=new $class();
        }
        return self::$_instace[$class];
    }
}
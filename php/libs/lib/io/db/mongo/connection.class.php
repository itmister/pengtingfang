<?php
namespace Io\Db\Mongo;
class Connection {

    protected static $_instances = array();

    /**
     * @param $key
     * @return \MongoClient
     */
    public static function instance( $key ) {
        if (empty($key)) return false;
        if ( empty(self::$_instances[$key]) ) {
            $cfg = \Lib\Core::config($key);
//            $cfg = \C($key);
//            $obj = new self( $cfg );
            $obj = new \MongoClient( $cfg['server'] );
            self::$_instances[$key] = $obj;
        }
        return self::$_instances[$key];
    }

}
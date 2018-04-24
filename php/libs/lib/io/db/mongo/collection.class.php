<?php
/**
 * mongodb collection 相当于mysql的表
 */
namespace Io\Db\Mongo;

class Collection extends \MongoCollection {

    protected  $_connection_key = '';

    protected $_db_name          = '';

    protected static $_instance_list = [];

    public static function i(){

        $class_now = get_called_class();
        return empty(self::$_instance_list[$class_now])
            ? ( self::$_instance_list[$class_now] = new $class_now($class_now) ) : self::$_instance_list[$class_now];
    }

    public function __construct( $className ) {
        if ( empty($this->_connection_key) ) throw new \Exception( 'mongodb connection_key is require.' );
        if ( empty($this->_db_name) ) throw new \Exception( 'mongodb _db_name is require.' );
        $arr                = explode('\\', $className);
        $collection_name    = strtolower( array_pop($arr) );
        $obj_mongo_client   = \Io\Db\Mongo\Connection::instance( $this->_connection_key );
        $mongo_db           = $obj_mongo_client->selectDB( $this->_db_name);
        parent::__construct( $mongo_db,   $collection_name );
    }

    /**
     * 增加记录
     * @param $data
     */
    public function add( $data ) {
        $this->save($data);
    }

    /**
     * 批量插入，注意主键
     * @param $data
     */
    public function add_all( $data ) {
        $this->batchInsert( $data );
    }


    /**
     * 删除记录
     * @param array $condition
     */
    public function delete( $condition = [] ) {
        $this->remove( $condition );
    }

    /**
     * 清空整表记录，索引也将将被删除
     */
    public function clear() {
        $this->drop();
    }
}
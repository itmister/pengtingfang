<?php
namespace Mongo;
class Mongodb{

    protected static $_instances = array();

    protected $_link = null;

    protected $_cfg = array();


    public static function instance($key) {
        if (empty($key)) return false;
        if ( empty(self::$_instances[$key]) ) {
            $cfg = \Lib\Core::config($key);
//            $cfg = \C($key);
            $obj = new self( $cfg );
            self::$_instances[$key] = $obj;

        }
        return self::$_instances[$key];
    }

    public function __construct( $cfg ) {
        $this->_cfg = $cfg;
        if(empty($this->_link)) $this->connect($cfg);
    }



    public function connect($cfg) {
        $cfg = $this->_cfg;
        if(class_exists("MongoClient")&&\Lib\Core::config('mongo_switch')){//php是否装好mongo扩展 以及是否开起mongo记录日志
            $this->_link = new \MongoClient("mongodb://{$cfg['MONGODB_HOST']}:{$cfg['MONGODB_PORT']}");
            if (empty($this->_link)) {
                throw new \Exception('mongodb connect error', 4);
            }
            $this->_link = $this->_link->$cfg['MONGODB_NAME'];
        }
    }

    public function insert($array,$table){
        if(empty($this->_link)) return false;
        return $this->_link->$table->insert($array);
    }
    public function close() {
        return $this->_link->close();
    }

}
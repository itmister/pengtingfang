<?php
namespace Dao\Union;
use \Dao;

/**
 * @package Dao\Union
 */
class Iptag_Source extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Iptag_Source
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function add_ip($ip){
        $ret =  $this->add(['ip'=>$ip,'addtime'=>time()]);
        return $ret;
    }

    public function get_ip($ip){
        $sql = "select * from {$this->_realTableName} where ip = '{$ip}' limit 1";
        $ret =  $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }
}

<?php
namespace Dao\Union;
use \Dao;

/**
 * @package Dao\Union
 */
class Iptag_Config extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Iptag_Config
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function add_tag($data){
        $ret =  $this->add($data);
        return $ret;
    }

    /**
     * 获取这个ip的所有标签
     * @param $ip
     * @return mixed
     */
    public function get_ip_tag($ip){
        $sql = "select * from {$this->_realTableName} where c_uname = {$ip}";
        return $this->query($sql);
    }

    /**
     *  获取具体ip的某个标签（可选带软件id)
     * @param $ip
     * @param $tag_id
     * @param string $promotion_id
     * @return mixed
     */
    public function get_ip_tag_ext($ip,$tag_id,$promotion_id = ''){
        $with ='';
        if ($promotion_id) {
            $with = "and c_promotion_id = '{$promotion_id}'";
        }
        $sql = "select * from {$this->_realTableName} where c_uname = {$ip} AND  c_tagtype = {$tag_id} $with";
        return $this->query($sql);
    }
}

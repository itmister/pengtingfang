<?php
/**
 * 积分表中活动名称对应描述 mapping 表
 */
namespace Dao\Union;
use \Dao;
class Credit_Name_Decs_Map extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Credit_Name_Decs_Map
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_list(){
        $sql = "select * from {$this->_realTableName}";
        return $this->query($sql);
    }

    public function get_count(){
        $sql = "select count(1) as num from {$this->_realTableName}";
        $ret = $this->query($sql);
        return $ret[0]['num'];
    }

    public function get_info_by_name($name){
        $sql = "select * from {$this->_realTableName} where name = '{$name}'";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }

    public function add_info($data){
        return $this->add($data);
    }

    public function get_map(){
        $data = $this->get_list();
        $map = [];
        foreach($data as $val){
            $map[$val['name']]=$val['desc'];
        }
        return $map;
    }

}

<?php
namespace Dao\Channel_7654;
use \Dao;
class Area extends Channel_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Channel_7654\Area
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 获取区域列表
     * @return \Dao\mixed
     */
    public function  select($where = "",$field = "*"){
        $sql = "SELECT {$field} FROM {$this->_get_table_name()}";
        if($where){
            $sql .=" WHERE {$where}";
        }
        $query_result = $this->query($sql);
        return $query_result;
    }
}

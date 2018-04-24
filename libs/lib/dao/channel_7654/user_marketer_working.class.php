<?php
namespace Dao\Channel_7654;
use \Dao;
class User_marketer_working extends Channel_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Channel_7654\User_marketer_working
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 获取作业列表
     * @return \Dao\mixed
     */
    public function  select($where = "",$field = "*,COUNT(a.userid) AS work_num"){
        $sql = "SELECT {$field} FROM (SELECT * FROM {$this->_get_table_name()} ORDER BY id DESC) AS a";
        if($where){
            $sql .=" WHERE {$where}";
        }
        $sql .= " GROUP BY a.userid ORDER BY a.id DESC";
        $query_result = $this->query($sql);
        return $query_result;
    }
}

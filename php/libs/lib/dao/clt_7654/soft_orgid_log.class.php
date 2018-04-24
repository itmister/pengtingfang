<?php
namespace Dao\Clt_7654;
class Soft_orgid_log extends Clt_7654 {

    protected static $_instance = null;

    /**
     * @return Soft_orgid_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 列表
     * @param string $where
     * @param string $field
     * @return \Dao\mixed
     */
    public function get_list($where = "",$field ="*")
    {
        $sql = "SELECT {$field} FROM {$this->_get_table_name()} AS s LEFT JOIN user AS u ON s.uid = u.uid";
        if($where){
            $sql .= " WHERE {$where}";
        }
        $query_result = $this->query($sql);
        return $query_result;
    }
}

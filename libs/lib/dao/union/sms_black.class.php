<?php
namespace Dao\Union;
use \Dao;
class Sms_black extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Sms_black
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 获取list
     * @param string $where
     * @param string $fields
     * @return array
     */
    public function select($where = true,$fields='*'){
    	$table_name = $this->_get_table_name();
    	$sql = "select {$fields} from {$table_name} where {$where}";
    	$data = $this->query( $sql );
    	return $data ? $data : array();
    }
    
    /**
     * 获取数目
     * @param str $where
     */
    public function count($where){
    	$table_name = $this->_get_table_name();
    	$sql = "select count(*) as count from {$table_name} where {$where}";
    	$count = $this->query( $sql );
    	return $count[0]['count'];
    }
    
}

<?php
namespace Dao\Clt_7654;
class Soft_orgid extends Clt_7654 {

    protected static $_instance = null;

    /**
     * @return Soft_orgid
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 获取信息
     * @return array
     */
	public function get_all($where=true,$field='*'){
    	$sql = "SELECT {$field} FROM `{$this->_realTableName}` WHERE {$where}";
    	return $this->query($sql);
    }
    
    /**
     * 获取总数
     * @param string $where
     * @return array
     */
    public function get_count($where){
    	$sql = "SELECT count(*) as count FROM `{$this->_realTableName}` WHERE {$where}";
    	$result = $this->query($sql);
    	return $result[0]['count'];
    }
    
    /**
     * 查询列表
     * @param string $field
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return \Dao\mixed
     */
    public function select($params){
    	extract($params);
    	if(!$field){
    		$field = "*";
    	}
    
    	$sql ="SELECT {$field} FROM {$this->_get_table_name()} AS s LEFT JOIN user AS u ON s.uid = u.uid";
    	if($where){
    		$sql .= " WHERE {$where}";
    	}
    	if($orderby){
    		$sql .= " ORDER BY {$orderby}";
    	}
    	if($limit){
    		$sql .=" LIMIT {$limit}";
    	}
    	$result = $this->query($sql);
    	return $result;
    }
}

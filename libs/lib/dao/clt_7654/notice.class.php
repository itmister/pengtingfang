<?php
namespace Dao\Clt_7654;
class Notice extends Clt_7654 {

    protected static $_instance = null;

    /**
     * @return Notice
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
    public function get_all($where){
    	$sql = "SELECT * FROM `{$this->_realTableName}` WHERE {$where}";
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
     * 更新点击量
     * @param integer $id
     * @param integer $hit
     * @return \Dao\mixed
     */
	 public function update_hits($id,$hit = 1){
        $query_sql    = "UPDATE `{$this->_get_table_name()}` SET `hits` = `hits`+{$hit} WHERE `id` = ".$id;
        $query_result = $this->query( $query_sql );
        return $query_result;
	 }
}

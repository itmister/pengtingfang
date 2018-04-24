<?php
namespace Dao\Online_7654;
use \Dao;
class Promotion extends Online_7654 {
	protected static $_instance = null;
	/**
	 *
	 * @return Dao\Online_7654\Promotion
	 */
	public static function get_instance() {
		if (empty ( self::$_instance )) {
			self::$_instance = new self( __CLASS__ );
		}
		return self::$_instance;
	}
	
	/**
	 * 获取信息
	 * 
	 * @return array
	 */
	public function get_all($where = true, $field = '*') {
		$sql = "SELECT {$field} FROM `{$this->_realTableName}` WHERE {$where}";
		return $this->query( $sql );
	}
	
	/**
	 * 获取总数
	 * 
	 * @param string $where        	
	 * @return array
	 */
	public function get_count($where) {
		$sql = "SELECT count(*) as count FROM `{$this->_realTableName}` WHERE {$where}";
		$result = $this->query( $sql );
		return $result[0]['count'];
	}
	
	public function get_list() {
		$table_promotion = $this->_realTableName;
		$sql = "SELECT id,`soft_name`,`soft_id` FROM {$table_promotion}";
		$result = array ();
		$list = $this->query( $sql );
		foreach ( $list as $item ) {
			$result[$item['soft_id']] = $item;
		}
		return $result;
	}
	/**
     * @desc
     * @return array
     */
    public function get_price($soft_id){
        $table_promotion = $this->_realTableName;
        $sql = "SELECT price FROM {$table_promotion}
        WHERE soft_id='{$soft_id}'";
        $result   = $this->query( $sql );
        if($result){
            $result = $result[0];
        }
        return $result;
    }
	public function get_promotion_name() {
		$sql = "SELECT `soft_name`,soft_id FROM {$this->_get_table_name()}";
		$result = $this->query( $sql );
		$data = array ();
		foreach ( $result as $v ) {
			$data[$v['soft_id']] = $v['soft_name'];
		}
		return $data;
	}

    /**
     *
     * @return array
     */
    public function get_one_list($id) {
        $table_promotion = $this->_realTableName;
        $sql = "SELECT id,`soft_name`,soft_id FROM {$table_promotion}
    	WHERE id={$id}";
        $result = $this->query( $sql );
        if ($result) {
            $result = $result[0];
        }
        return $result;
    }
}
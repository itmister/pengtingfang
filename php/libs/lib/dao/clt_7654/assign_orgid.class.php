<?php
namespace Dao\Clt_7654;
class Assign_orgid extends Clt_7654 {
	protected static $_instance = null;
	/**
	 *
	 * @return Assign_orgid
	 */
	public static function get_instance() {
		if (empty ( self::$_instance )) {
			self::$_instance = new self ( __CLASS__ );
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
		return $this->query ( $sql );
	}
	
	/**
	 * 获取总数
	 * 
	 * @param string $where        	
	 * @return array
	 */
	public function get_count($where) {
		$sql = "SELECT count(*) as count FROM `{$this->_realTableName}` WHERE {$where}";
		$result = $this->query ( $sql );
		return $result [0] ['count'];
	}
	
	public function user_orgid($soft_id, $ymd) {
		$sql = "select uid,org_id from {$this->_realTableName}
    	where status=0 and softID='{$soft_id}' and FROM_UNIXTIME(dateline,'%Y%m%d')<={$ymd}";
		$ret = $this->query ( $sql );
		return $ret;
	}

    public function user_orgid_recover($soft_id, $ymd) {
        $sql = "select uid,org_id from {$this->_realTableName}
    	where status>0 and softID='{$soft_id}' and FROM_UNIXTIME(dateline,'%Y%m%d')<={$ymd} and FROM_UNIXTIME(updateline,'%Y%m%d')>{$ymd} and updateline>0";
        $ret = $this->query ( $sql );
        return $ret;
    }
	
	public function user_orgid_status($soft_id, $org_str, $status) {
		$sql = "select uid,org_id from {$this->_realTableName}
    	where status={$status} and softID='{$soft_id}' and org_id in ({$org_str})";
		$ret = $this->query ( $sql );
		return $ret;
	}
	
	public function get_soft_id_user($channel_id, $soft_id) {
		$sql = "select a.uid,b.name from {$this->_realTableName} as a inner join user as b on a.uid=b.id
    	where a.status=0 and a.softID='{$soft_id}' and b.channel_id={$channel_id}";
		$ret = $this->query ( $sql );
		return $ret;
	}
	 public function orgid_select($org_str,$soft_id){
        $sql = "select uid,org_id from {$this->_realTableName}
        where status>0 and softID='{$soft_id}' and org_id in ({$org_str}) group by org_id";
        $ret = $this->query($sql);
        return $ret;
    }
}

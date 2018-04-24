<?php
namespace Clt_7654;

/**
 * 软件渠道号管理
 * Class promotion
 * @package Union
 */

class Soft_orgid {
    protected static $_instance = null;

    /**
     * @return \Clt_7654\Soft_orgid
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 取渠道号列表
     * @return array
     */
    public function get_list_by_soft_id( $soft_id ) {
    	if ( empty($soft_id) ) return false;
    	$where = "soft_id = '{$soft_id}' and status = 0";
    	
    	$result = array();
    	$field = 'id,org_id';
    	$list   = \Dao\Clt_7654\Soft_orgid::get_instance()->get_all($where,$field);
    	
    	foreach ($list as $item ) {
    		$result[ $item['id'] ] = $item;
    	}
    	return $result;
    }
    
    /**
     * 取渠道号列表
     * @return array
     */
    public function get_list_by_uid( $uid ) {
    	if ( empty($uid) ) return false;
    	$where = "uid = '{$uid}'";
    	 
    	$result = array();
    	$field = 'id,org_id,soft_id';
    	$list   = \Dao\Clt_7654\Soft_orgid::get_instance()->get_all($where,$field);
 
    	foreach ($list as $item ) {
    		if(!$result[$item['soft_id']]){
    			$result[$item['soft_id']]['soft_id'] = $item['soft_id'];
    			$result[$item['soft_id']]['not_assign'] = $this->get_list_by_soft_id($item['soft_id']);
    		}
    		$result[$item['soft_id']]['has_assign'][] = $item['org_id'];
    	}
    	return $result;
    }
    
    /**
     * 分配渠道号
     * @param unknown $uid
     * @param unknown $soft_id_array
     * @param unknown $org_id_array
     */
    public function assign_org_id($uid,$soft_id_array,$org_id_array){
    	if ( empty($uid)) return false;

    	$arr_data['uid'] = $uid;
    	$arr_data['status'] = 1;
    	$arr_data['fdateline'] = time();
    	
    	foreach ($soft_id_array as $key => $val){
    		$list = $org_id_array[$val];
    		if(empty($list)){
    		    continue;
    		}
    		$str = implode("','",$list);
    		$where = "status = 0 and (uid = '' or uid is null ) and org_id in('{$str}') and soft_id = '{$val}'";
    		$this->write_log($where,$uid,1);
    		\Dao\Clt_7654\Soft_orgid::get_instance()->update($where, $arr_data);

    		$map = "status = 0 and softID = '{$val}' and org_id in('{$str}')";
    		$assign_list = \Dao\Clt_7654\Assign_orgid::get_instance()->get_all($map);
    		foreach ($assign_list as $ak => $av){
    			$exists_assign[] =  $av['org_id'];
    		}
    		$n=0;
			foreach ($list as $lk => $lv){
				if(!in_array($lv, $exists_assign)){
					$data[$n]['uid'] = $uid;
					$data[$n]['org_id'] = $lv;
					$data[$n]['softID'] = $val;
					$data[$n]['dateline'] = $arr_data['fdateline'];
					$n++;
				}
			}
    		\Dao\Clt_7654\Assign_orgid::get_instance()->addAll($data);
    	}

    	$has_assign_list = $this->get_assign_org_id_by_uid($uid);
    	$assign_list = array();
    	foreach ($has_assign_list as $hk => $hv){
    		if(!in_array($hv['org_id'], $org_id_array[$hv['soft_id']])){
    			$un_assign_list[$hv['soft_id']][] = $hv['org_id'];
    		}
    	}
    	
    	$un_data['uid'] = null;
    	$un_data['status'] = 0;
    	$un_data['hdateline'] = time();
    	$un_data['fdateline'] = null;

    	
    	foreach ($un_assign_list as $uk => $uv){
    		$ustr = implode("','",$uv);
    		$where = "uid = {$uid} and org_id in('{$ustr}') and soft_id = '{$uk}'";
    		$this->write_log($where,$uid,0);
    		\Dao\Clt_7654\Soft_orgid::get_instance()->update($where, $un_data);
    		
    		$map = "softID = '{$uk}' and org_id in('{$ustr}') order by id asc";
    		$ao_list = \Dao\Clt_7654\Assign_orgid::get_instance()->get_all($map);
    		foreach ($ao_list as $aok => $aov){
    			$d = array();
    			$d['status'] = $aov['status'] + 1;
    			if($aov['status'] == 0) $d['updateline'] = $un_data['hdateline'];
    			\Dao\Clt_7654\Assign_orgid::get_instance()->update("id={$aov['id']}", $d);
    		}
    	}
    }
    
    public function get_assign_org_id_by_uid($uid){
    	if ( empty($uid) ) return false;
    	$where = "uid = '{$uid}'";
    	$field = 'id,org_id,soft_id';
    	$list   = \Dao\Clt_7654\Soft_orgid::get_instance()->get_all($where,$field);
    	return $list;
    }
    
    /**
     * 分配日志
     * @param unknown $where
     * @param unknown $uid
     * @param unknown $status
     */
    public function write_log($where,$uid,$status){
    	$field = 'org_id,soft_id';
    	$list = \Dao\Clt_7654\Soft_orgid::get_instance()->get_all($where,$field);
    	$n = 0;
    	$time = time();
    	foreach($list as $key => $val){
    		$data[$n]['uid'] = $uid;
    		$data[$n]['status'] = $status;
    		$data[$n]['org_id'] = $val['org_id'];
    		$data[$n]['soft_id'] = $val['soft_id'];
    		$data[$n]['dateline'] = $time;
    		$n++;
    	}
    	\Dao\Clt_7654\Soft_orgid_log::get_instance()->addAll($data);
    }
}
<?php
namespace Union\Sms;

/**
 * 短信
 * Class Sms
 * @package Union
 */

class Sms {
    protected static $_instance = null;

    /**
     * @return \Union\Sms\Sms
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 添加短信黑名单
     * @param array $mobiles
     */
    public function add_sms_black($mobiles){
    	$str = implode(',', $mobiles);
    	$where = "mobile in ($str)";
    	$list = \Dao\Union\Sms_black::get_instance()->select($where);

    	$data = $has = array();
    	if($list){
    		foreach ($list as $key => $val){
    			if(in_array($val['mobile'],$mobiles)){
    				$has[] = $val['mobile'];
    			}
    		}
    	}
    	$result = array_diff($mobiles,$has);
    	$time = time();
    	foreach ($result as $k => $v){
    		$data[$k]['mobile'] = $v;
    		$data[$k]['inputtime'] = $time;
    	}
    	if($data)	\Dao\Union\Sms_black::get_instance()->add_all($data);
    }
    
    /**
     * 根据id删除黑名单
     * @param int $id
     * @return boolean
     */
    public function delete_sms_black($id){
    	if(!is_numeric($id)) return false;
    	$where = "id = $id";
    	$result = \Dao\Union\Sms_black::get_instance()->delete($where);
    	return $result;
    }
}
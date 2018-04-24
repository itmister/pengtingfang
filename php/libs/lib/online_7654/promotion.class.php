<?php
namespace Online_7654;

/**
 * 推广软件管理
 * Class promotion
 * @package Union
 */

class Promotion {
    protected static $_instance = null;

    /**
     * @return \Online_7654\Promotion
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 取推广软件列表
     * @return array
     */
    public function get_list( $state = 1 ) {
    	$state       = intval( $state );
    	$where = !empty($state) ? "state={$state}" : 'true';
    	
    	$result = array();
    	$field = 'id,soft_name,soft_id';
    	$list   = \Dao\Online_7654\Promotion::get_instance()->get_all($where,$field);
    	
    	foreach ($list as $item ) {
    		$result[ $item['soft_id'] ] = $item;
    	}
    	return $result;
    }

    /**
     * 取某一个推广软件信息
     * @param string $short_name 标识名,空则使用软件id检索，
     * @param int $id 软件id 使用软件id检索，不建议
     * @return array
        id : 软件id
        name : 名称
        template_type :
        short_name : 标识名
     */
    public function get_info_by_soft_id( $soft_id = 0 ) {
        if ( empty($soft_id) ) return false;
        $where = "soft_id = '{$soft_id}'";
        $info = \Dao\Online_7654\Promotion::get_instance()->get_all($where);
		return $info ? $info[0] : null;

    }
}
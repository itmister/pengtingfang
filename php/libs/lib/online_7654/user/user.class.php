<?php
namespace Online_7654\User;

/**
 * 用户管理
 * Class User
 * @package Union
 */

class User {
    protected static $_instance = null;

    /**
     * @return \Online_7654\User
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 获取渠道号关联的数目
     * @param unknown $channel_id
     * @return multitype:
     */
    public function get_count_by_channel_id($channel_id){
    	$where = "channel_id = {$channel_id}";
    	$count = \Dao\Online_7654\User::get_instance()->get_count($where);
    	return $count;
    }
    
    /**
     * 账号是否已经存在/关联
     * @param str $name
     */
    public function is_exists_by_name($name){
    	$where = "name = '{$name}'";
    	$info = \Dao\Online_7654\User::get_instance()->get_all($where);
    	return $info ? true : false;
    }
}
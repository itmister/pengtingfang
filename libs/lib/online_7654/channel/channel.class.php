<?php
namespace Online_7654\Channel;

/**
 * 渠道管理
 * Class Channel
 * @package Union
 */

class Channel {
    protected static $_instance = null;

    /**
     * @return \Online_7654\Channel
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 取渠道列表
     * @return array
     */
    public function get_list() {
    	$result = array();
    	$field = 'id,username,channel_desc';
    	$list   = \Dao\Online_7654\Channel::get_instance()->get_all(true,$field);
    	return $list;
    }
}
<?php
namespace Dao\Union;
use \Dao;

/**
 * @package Dao\Usertag_changelog
 */
class Usertag_changelog extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Usertag_changelog
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function add_log($data){
        $ret =  $this->add($data);
        return $ret;
    }
    
    /**
     * 获取用户log
     * @param int $uid
     */
    public function get_log($uid){
    	$sql = "select a.*,b.name as promotion_name from {$this->_realTableName} a left join promotion b on a.promotion = b.short_name where a.c_uid = {$uid} ORDER by c_addtime desc";
    	$ret = $this->query($sql);
    	return $ret;
    }
}

<?php
namespace Union\Store_validate_record;

/**
 * 推广方式认证
 * Class Store_validate_record
 * @package Union
 */

class Store_validate_record {
    protected static $_instance = null;

    /**
     * @return \Union\Store_validate_record
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 获取最后一条记录
     * @param int $uid
     * @return array
     */
    public function get_last($uid){
    	$where = "uid = $uid order by id desc limit 1";
    	$record = \Dao\Union\Store_validate_record::get_instance()->select($where);
    	return $record[0];
    }
    
    /**
     * 是否认证
     * @param int $uid
     */
    public function is_validate($uid){
    	$where = "uid = $uid order by id desc limit 1";
    	$record = $this->get_last($uid);
    	return ($record['status'] != 2) ? 0 : 1;
    }
    
    /**
     * 是否第一次通过验证
     * @param int $uid
     * @return boolean
     */
    public function is_first($uid){
    	$where = "uid = $uid and status = 2";
    	$count = \Dao\Union\Store_validate_record::get_instance()->count($where);
    	return $count ? false : true;
    }
    
    /**
     * 推广方式认证锁定等级
     * @param int $uid
     */
	public function lock_grade($uid){
    	$validate = $this->is_validate($uid);
    	if(!$validate){
    		\Union\User\User_ext::get_instance()->lock_grade($uid);
    	}else{
    		\Dao\Union\User_ext::get_instance()->update("uid={$uid}", array('lock_grade' => 0));
    	}
    }
    
    /**
     * 是否存在审核中的认证
     * @param int $uid
     */
    public function has_validate($uid){
    	$where = "uid = $uid and status = 1";
    	$record = \Dao\Union\Store_validate_record::get_instance()->select($where);
    	return ($record) ? 1 : 0;
    }
    
    /**
     * 发送站内信
     * @param int $uid
     * @param int $status
     */
    public function send_msg($uid,$status){
    	$msg = $status == 2 ? '审核通过' : '审核未通过';
    	$note = "亲爱的技术员：您的推广方式认证已经{$msg}，可到“我的任务”中查看。";
		\Union\Service\XiaoXi::get_instance()->site_message(array($uid),$note);	//站内信
    }
    
}
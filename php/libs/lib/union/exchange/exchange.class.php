<?php
namespace Union\Exchange;

/**
 * 兑换
 * Class exchange
 * @package Union
 */

class Exchange {
    protected static $_instance = null;

    /**
     * @return \Union\Exchange\Exchange
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 待定
     */
    public function pending($orderid,$note){
    	$where = "orderid = '$orderid' ";
    	$order_info = \Dao\Union\Exchange::get_instance()->select($where);
    	
    	$user_info = \Dao\Union\User::get_instance()->get_user_info_by_id($order_info[0]['uid']);
    	$a_note = '技术员您好！兑现订单'.$orderid.'需要进一步审核，请通过7654客服或7654个人中心的“我要申诉"功能反馈审核资料。';
    	$a_note2 = '尊敬的技术员朋友，您好！由于您的帐号存在异常，导致兑现订单号为'.$orderid.'的提现申请需要进一步审核，请通过以下渠道反馈审核资料：1、7654客服 2、7654个人中心的“我要申诉”功能。';
    	if(is_array($order_info)&&!empty($order_info)) {
    		$data = array(
    				'changetime'=>time(),
    				'dealstatus'=>118,
    				'note'=>$note
    		);
    		$result = \Dao\Union\Exchange::get_instance()->update($where,$data);
    		if($result){
    			\Union\Service\XiaoXi::get_instance()->site_message(array($user_info['id']),$a_note2);	//站内信
    			\Union\Service\XiaoXi::get_instance()->sms(array($user_info['phone']),$a_note);			//短信
    		}
    	}
    	return $result ? true : false;
    }
    
    /**
     * 检测3个月内是否有作弊/嫌疑标签
     * @param int $uid
     * @return boolean
     */
    public function is_cheat($uid,$time = ''){
    	$time = $time ? $time : TIMESTAMP;
    	$where = "status = 1 and uid = $uid and t_id in(1,2) and UNIX_TIMESTAMP(ctime) <= $time and ( UNIX_TIMESTAMP(ctime) >= UNIX_TIMESTAMP(DATE_SUB(FROM_UNIXTIME($time),INTERVAL 3 MONTH)))";
    	$result = \Dao\Union\User_Tags::get_instance()->getTags($where);
    	return $result ? $result[0]['cheat_soft_name'] : false;
    }
    
}
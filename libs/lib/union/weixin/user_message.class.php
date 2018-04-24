<?php
/**
 * Created by vl
 * Description : 处理用户发送信息
 * Date: 2016/3/8
 * Time: 21:10
 */
namespace Union\Weixin;

class user_message {

    /**
     * @var \Util\Weixin_sdk\Request
     */
    protected $_weixin_request = null;

    /**
     * @var \Util\Weixin_sdk\Response
     */
    protected $_weixin_response = null;

    /**
     * 当前微信open_id绑定的用户信息
     * @var array
     */
    protected $_user_info = [];

    /**
     * @param $weixin_request
     * @param $weixin_response
     * @param $user_info
     */
    public function user_message( $weixin_request, $weixin_response, $user_info ) {
        $this->_weixin_request = $weixin_request;
        $this->_weixin_response = $weixin_response;
        $this->_user_info = $user_info;
    }

    public function handler( $message ) {

    }
}

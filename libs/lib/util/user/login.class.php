<?php
/**
 * 用户登录管理
 * Created by PhpStorm.
 * User: vling
 * Date: 16/1/27
 * Time: 17:33
 */
namespace Util\User;
use Core\Object;
use \Util\Security;
use \Io\Cookie;

class Login extends Object {

    protected $_sec_key     = '7654kklsllkao(#kkssf'; //加密密钥
    protected $_sec_name    = 'wx_sec';
    protected $_user_info   = [];

    /**
     *
     * @param array $option
     * @return Login
     */
    public static function i( $option = [] ) {
        return parent::i( $option );
    }

    /**
     * 取登录用户信息
     * @return array
     */
    public function info_get() {
        if ( empty( $this->_user_info ) ) {
            $sec = Cookie::get( $this->_sec_name );
            $str = Security::decrypt( $sec, $this->_sec_key );
            $this->_user_info = json_decode( $str, true );
        }
        return $this->_user_info;
    }

    /**
     * 设置登录用户信息
     * @param mixed $info
     * @return boolean
     */
    public function info_set( $info ) {
        $this->_user_info = $info;
        $str = json_encode( $info );
        $sec = Security::encrypt( $str, $this->_sec_key );
        return Cookie::set( $this->_sec_name, $sec );
    }

    /**
     * 退出登录
     */
    public function logout() {
        $this->_user_info = null;
        return Cookie::set( $this->_sec_name, null);
    }
}
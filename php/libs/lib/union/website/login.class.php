<?php
namespace Union\WebSite;

/**
 *
 * Class Login
 * @package Union\WebSite
 */

class Login {
    /**
     * 用户表模型层
     * @var UserModel
     */
    protected $userModel;

    public function __construct(){
        $this->userModel  = \Dao\Union\User::get_instance();
    }
    
    /**
     * 用户名密码同步登录
     * @param $username
     * @param $password
     * @param $sign
     * @param $des
     */
    public function sync_login($username,$password,$sign,$des){
        if(!$username || !$password || !$sign || !is_object($des)){
            return false;
        }
        $password = $des->decrypt(base64_decode(trim(rawurldecode($password))),'7654');
        $data = explode('|',$password);
        $des_password = $data[0];
        
        $keys = '!QAZXSW@';
        $des_sign = md5($keys.$username.$des_password);
        if($des_sign != $sign){
            return false;
        }
        
        $password = md5($des_password);
        $user_info  = $this->userModel->get_user_info_by_name_password($username,$password,'id');
        if(!$user_info){
            return false;
        }
        $key = $this->_myxor("{$user_info['id']}|{$data[1]}");
        return $this->_sync_login($key);
    }
    
    /**
     * 同步登录
     * @param $key
     */
    private function _sync_login($key){
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        setcookie("uid",$key, null,"/");
        $_COOKIE['uid'] = $key;
        $string = "/*{$key}*/";
        return $string;
    }
    
    /**异或加密解密算法
     * @param $string
     * @param string $key
     * @return int|string
     */
    private function _myxor($string,$type=1, $key = '') {
        if('' == $string) return '';
        if(!$type) $string = base64_decode($string);
        if('' == $key) $key = md5("7654C");
        $len1 = strlen($string);
        $len2 = strlen($key);
        if($len1 > $len2) $key = str_repeat($key, ceil($len1 / $len2));
        if($type) {
            return base64_encode($string ^ $key);
        } else {
            return $string ^ $key;
        }
    }
    
}
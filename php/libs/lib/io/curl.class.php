<?php
namespace Io;
class Curl {

    protected static $_instance = null;

    protected $_curl_handler = null; //curl句柄

    protected $_config  = [
        'agent'             => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)',//http浏览器信息
        'timeout'           => 5,//超时间
        'referer'           => '',//
        'follow_redirect'  => 1,//是否跟踪301跳转
    ];

    /**
     * @return Curl
     */
    public static function get_instance($option = []){
        if (empty(self::$_instance)) {
            self::$_instance = new self( $option );
        }
        return self::$_instance;
    }

    public function __construct( $config = [] ) {
        if ( !empty( $config ) && is_array( $config ) ) {
            $this->_config = array_merge( $this->_config, $config );
        }
        $this->_curl_handler = curl_init();
        curl_setopt($this->_curl_handler, CURLOPT_TIMEOUT, $this->_config['timeout']);
        curl_setopt($this->_curl_handler, CURLOPT_USERAGENT, $this->_config['agent']);
        curl_setopt($this->_curl_handler, CURLOPT_REFERER, $this->_config['referer']);
        curl_setopt($this->_curl_handler, CURLOPT_FOLLOWLOCATION, $this->_config['follow_redirect']);
        curl_setopt($this->_curl_handler, CURLOPT_RETURNTRANSFER, 1);//返回结果


    }

    public function __destruct() {
        curl_close( $this->_curl_handler);
    }

    /**
     * curl 取远程地址数据
     * @param string $url
     * @param [] $params
     * @param [] $option
     * @return string
     */
    public function get( $url, $params = [], $option = []) {
        if (empty($url)) return '';
        curl_setopt( $this->_curl_handler, CURLOPT_URL, $url );
        curl_setopt( $this->_curl_handler, CURLOPT_POSTFIELDS, !empty($params) ? $params : [] );
        curl_setopt( $this->_curl_handler, CURLOPT_POST, 1 );
        $result = curl_exec($this->_curl_handler);
        return $result;
    }
}
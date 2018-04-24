<?php
namespace Union\Weixin;

/**
 * 微信跳出连接管理
 * Class Url
 * @package Union\Weixin
 */
class Url {

    protected static $_instance = null;

    /**
     * @return Url
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(\Config::get('weixin'));
        }
        return self::$_instance;
    }

    /**
     * 配置
     * @var array
     */
    protected $_cfg = [
        'app_secret' => 'asdgfadsgasfqwerfwear23%^$@#',//签名密钥
        'http_root'  => '',//连接域名
        'expire'     => '3600',//失效时长@todo
    ];

    /**
     * @param array $cfg
     */
    public function __construct($cfg) {
        if ( !empty($cfg) && is_array( $cfg) ) $this->_cfg = array_merge( $this->_cfg, $cfg );
        if ( empty($this->_cfg['http_root']) ) $this->_cfg['http_root'] = 'http://' . $_SERVER['SERVER_NAME'] . '/';
    }

    /**
     * 生成url
     * @param $arr_params
     * @return string
     */
    public function make( $arr_params ) {

        if (!is_array( $arr_params ))  $arr_params = [];
        $arr_params['ts'] = time();
        $arr_params['app_secret'] =  $this->_cfg['app_secret'];
        ksort($arr_params);//字符下标可能会乱序
        $str    = implode(',', $arr_params );
        unset($arr_params['app_secret']);
        $sign   = md5($str);
        $arr_params['sign'] = $sign;

        $query_str  = http_build_query( $arr_params );
        $result     = $this->_cfg['http_root'] . '?' . $query_str;
        return $result;

    }

    /**
     * 检查参数签名
     * @param $arr_params
     * @return boolean
     */
    public function check( $arr_params ) {

        $sign = $arr_params['sign'];
        unset( $arr_params['sign']);
        $arr_params['app_secret'] =  $this->_cfg['app_secret'];
        ksort($arr_params);
        $str    = implode(',', $arr_params );
        $_sign = md5($str);
        return $sign == $_sign;

    }
}
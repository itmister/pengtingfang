<?php
namespace Util\Weixin_sdk;

class Api {

    /**
     * 请求微信接口
     * @param $url
     * @param string $response_type
     * @return mixed|string
     */
    public static function get($url, $response_type = '') {
        $str = \Io\Http::get( $url );
        $str = trim( $str );
        switch ( $response_type ) {
            case 'json':
                return json_decode( $str, true);
                break;
            case 'xml':
                break;
            default :
                return $str;
                break;
        }
    }

    /**
     * 请求微信接口
     * @param $url
     * @param array $data
     * @return mixed|json
     */
    public static function post($url, $data) {
        $str = @file_get_contents($url, false, stream_context_create(array('http' => array('method' => 'POST','content' =>json_encode($data)))));
        return $str;
    }
}
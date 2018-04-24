<?php
namespace Util\Weixin_sdk;

class Token extends Api {

    public static function sync() {
        $cfg_weixin = \Config::get('weixin');
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$cfg_weixin['app_id']}&secret={$cfg_weixin['app_secret']}";
        $data = self::get( $url, 'json');
        if ( !empty($data) && !empty($data['access_token']) ) {
            \Io\File::output( PATH_LIB . '/conf/weixin_token.php', [ 'weixin_token_data' => ['token' => $data['access_token'], 'time' => time() ] ], false, null, '.php' );
        }
        return false;
    }

    public static function get_token() {
        $data = \Config::get('weixin_token_data', null, null, 'weixin_token');
        if (empty($data) || empty( $data['token'] ) ||  time() - intval( $data['time'] )  > 1800 ) {
            self::sync();
            $data = \Config::get('weixin_token_data', null, null, 'weixin_token');
        }
        return $data['token'];
    }
}
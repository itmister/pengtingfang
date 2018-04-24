<?php
/**
 * 获取weixin open_id
 * Created by PhpStorm.
 * User: vling
 * Date: 16/1/27
 * Time: 19:37
 */
namespace Util\Weixin_sdk;
class Open_id {

    /**
     * @param string $app_id 公众号appid
     * @param string $app_secret 公众号secret key
     * @param string $url_callback
     * @return string
     * @throws Exception
     */
    public static function get($app_id, $app_secret, $url_callback = '' ) {
        $code = trim($_GET['code']);
        if ( empty($code) ) {

            //取前url回传code
            if ( empty( $url_callback ) )  {
                // 注意 URL 一定要动态获取，不能 hardcode.
                $protocol       = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                $url_callback   = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            }

            $param = [
                'appid'         => $app_id,
                'redirect_uri'  => $url_callback ,
                'response_type' => 'code',
                'scope'         => 'snsapi_base',//应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息）
                'state'         => 'STATE' . '#wechat_redirect'
            ];
            $param_build = http_build_query( $param );
            $url_get_auth = "https://open.weixin.qq.com/connect/oauth2/authorize?" . $param_build;
            \Util\Debug::log( $url_get_auth );
            Header( 'Location:' . $url_get_auth );
        }
        else {
            $url_get_open_id = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$app_id}&secret={$app_secret}&code={$code}&grant_type=authorization_code";
            $data = file_get_contents( $url_get_open_id );
//            $data = \Io\Curl::get_instance()->get( $url_get_open_id );
            \Util\Debug::log( $url_get_open_id );
            \Util\Debug::log( $data );
            $arr = json_decode( $data, true );
            if ( empty($arr) || !empty($arr['errcode'])) {
                \Util\Debug::log("微信授权失败:" . $data );
                throw new \Exception("微信授权失败");
            }
            return $arr['openid'];
        }
    }
}
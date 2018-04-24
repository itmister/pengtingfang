<?php
/**
 * Created by PhpStorm.
 * User: vling
 * Date: 16/1/28
 * Time: 16:39
 */
namespace Util\Weixin_sdk\Api\Menu;

class Create {
    public function run($json_menu) {
        $token = \Util\Weixin_sdk\Token::get_token();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$token}";
        $ret = \Io\Curl::get_instance()->get( $url, $json_menu );
        \Io::dead( $ret );
        return $ret;
    }
}
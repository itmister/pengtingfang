<?php
/**
 * Created by PhpStorm.
 * User: vling
 * Date: 16/1/25
 * Time: 00:01
 */
namespace Util\Weixin_sdk;
class Jsapi_ticket extends Api{
    public static function sync() {
        $token = Token::get_token();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$token}&type=jsapi";
        $data = self::get( $url, 'json');
        if ( !empty($data) && !empty($data['ticket']) ) {
            \Io\File::output( PATH_LIB . '/conf/weixin_jspai_ticket.php', [ 'weixin_jspai_ticket_data' => ['jspai_ticket' => $data['ticket'], 'time' => time() ] ], false, null, '.php' );
        }
        return false;

    }

    public static function get_jsapi_ticket() {
        $data = \Config::get('weixin_jspai_ticket_data', null, null, 'weixin_jspai_ticket');
        if (empty($data) || empty($data['jspai_ticket'] || time() - $data['time'] > 3600 )) {
            self::sync();
            $data = \Config::get('weixin_jspai_ticket_data', null, null, 'weixin_jspai_ticket');
        }
        return $data['jspai_ticket'];
    }
}
<?php
/**
 * 微信请求处理类
 */
namespace Util\Weixin_sdk;

class Response {

    /**
     * @var Response
     */
    protected $_request = null;

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function __construct( $request ) {
        if ( is_a($request, 'Request')) {
            throw new \Exception('need request object');
        }
        $this->_request = $request;
    }

    /**
     * 回复普通文本信息
     * @param $content 文本内容
     * @return mixed
     */
    public function response( $content ) {

        $pattern = "
<xml>
<ToUserName><![CDATA[#toUser]]></ToUserName>
<FromUserName><![CDATA[#fromUser]]></FromUserName>
<CreateTime>#CreateTime</CreateTime>
<MsgType><![CDATA[#text]]></MsgType>
<Content><![CDATA[#Content]]></Content>
</xml>
        ";
        $arr_search = ['#toUser','#fromUser', '#CreateTime', '#text', '#Content'];
        $request    = $this->_request;
        $arr_replace = [
            $request->user_open_id,
            $request->developer_weixin_id,
            time(),
            //$request->msg_type,
            'text',
            $content
        ];
        $result = str_replace( $arr_search, $arr_replace, $pattern );
        return $result;

    }

    /**
     * 转发信息至客服
     * @return mixed
     */
    public function response_to_customer_service( ) {
        $pattern="
<xml>
     <ToUserName><![CDATA[#toUser]]></ToUserName>
     <FromUserName><![CDATA[#fromUser]]></FromUserName>
     <CreateTime>#CreateTime</CreateTime>
     <MsgType><![CDATA[transfer_customer_service]]></MsgType>
 </xml>";
        $request    = $this->_request;
        $arr_search = ['#toUser','#fromUser', '#CreateTime'];
        $arr_replace = [
            $request->user_open_id,
            $request->developer_weixin_id,
            time(),
            //$request->msg_type,
        ];
        $result = str_replace( $arr_search, $arr_replace, $pattern );
        return $result;
    }

    /**
     * 回复图文信息
     * @param array $items [ [title,description, picture, url] ... ]
     * @return string
     */
    public function image_text( $items = [] ) {
        $item_count = count( $items );

        $items_list = '';
        if ( !empty($items) ) foreach ( $items as $row ) {
            $items_list .= "
<item>
<Title><![CDATA[{$row['title']}]]></Title>
<Description><![CDATA[{$row['description']}]]></Description>
<PicUrl><![CDATA[{$row['picture']}]]></PicUrl>
<Url><![CDATA[{$row['url']}]]></Url>
</item>
            ";
        }

        $pattern="
<xml>
<ToUserName><![CDATA[#toUser]]></ToUserName>
<FromUserName><![CDATA[#fromUser]]></FromUserName>
<CreateTime>#CreateTime</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>#item_count</ArticleCount>
<Articles>
#item_list
</Articles>
</xml>
        ";

        $request    = $this->_request;
        $arr_search = ['#toUser','#fromUser', '#CreateTime', '#item_count', '#item_list'];
        $arr_replace = [
            $request->user_open_id,
            $request->developer_weixin_id,
            time(),
            $item_count,
            $items_list
        ];
        $result = str_replace( $arr_search, $arr_replace, $pattern );
        return $result;

    }
    /**
     * 回复图片消息
     *
     */
    public function images($media_id){
        if(!isset($media_id) || empty($media_id)){
            return "";
        }
        $pattern="
<xml>
<ToUserName><![CDATA[#toUser]]></ToUserName>
<FromUserName><![CDATA[#fromUser]]></FromUserName>
<CreateTime>#CreateTime</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
<MediaId><![CDATA[#MediaId]]></MediaId>
</Image>
</xml>
        ";
        $request    = $this->_request;
        $arr_search = ['#toUser','#fromUser', '#CreateTime','#MediaId'];
        $arr_replace = [
            $request->user_open_id,
            $request->developer_weixin_id,
            time(),
            $media_id,
        ];
        $result = str_replace( $arr_search, $arr_replace, $pattern );
        return $result;
    }
}
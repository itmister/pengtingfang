<?php
/**
 * 微信请求处理类
 */
namespace Util\Weixin_sdk;

class Request {

    /**
     * 用户关注公众号
     */
    const EVENT_SUBSCRIBE   = 'subscribe';

    /**
     * 用户取消关注公众号
     */
    const EVENT_UNSUBSCRIBE = 'unsubscribe';

    /**
     * 用户扫二维码
     */
    const EVNET_SCAN        = 'scan';


    /**
     * 点击菜单
     */
    const EVENT_MENU_CLICK = 'click';

    /**
     * 开发者微信id(ToUserName)
     * @var null
     */
    public $developer_weixin_id = '';

    /**
     * 用户openid(FromUserName)
     * @var string
     */
    public $user_open_id = '';

    /**
     * 信息内容
     * @var string
     */
    public $content = '';

    /**
     * 信息发生的时间戳(CreateTime)
     * @var int
     */
    public $dateline = 0;

    /**
     * 消息类型(MsgType)
     * @var string
     */
    public $msg_type = '';

    /**
     * 事件(Event)
     * @var string
     */
    public $event = '';

    /**
     * 事件key(EventKey)
     * @var string
     */
    public $event_key = '';



    public function __construct() {
        $post_str = file_get_contents('php://input');//$GLOBALS['HTTP_RAW_POST_DATA'];
        if ( !empty($post_str) ) {
            $post_str = str_replace( ['<![CDATA[', ']]>'], ['', ''], $post_str );
            $post_str = str_replace( ['<![CDATA[', ']]>'], ['', ''], $post_str );
            $arr = (array)simplexml_load_string( $post_str );
            \Util\Debug::log($post_str, "weixin_request");
            if ( !empty($arr) && !empty( $arr['FromUserName'] ) ) {
                $this->developer_weixin_id      =  $arr['ToUserName'];
                $this->user_open_id             =  $arr['FromUserName'];
                $this->dateline                 =  $arr['CreateTime'];
                $this->event                    =  $arr['Event'];
                $this->event_key                =  $arr['EventKey'];
                $this->msg_type                 =  $arr['MsgType'];
                $this->content                  =  !empty($arr['Content']) ? $arr['Content'] : '';
                $this->media_id                 =  !empty($arr['MediaId']) ? $arr['MediaId']: '';
                return true;
            }
        }

        throw new \Exception('weixin request parse error');
    }

    /**
     * 判断是否微信浏览器请求
     * @return boolean
     */
    public static function is_weixin_browser() {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }
    /**
     * 上传临时图片素材
     */
    public function https_request($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
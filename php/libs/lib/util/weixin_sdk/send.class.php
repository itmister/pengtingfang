<?php
namespace Util\Weixin_sdk;

class Send extends Api{
    //获取media_id
    public static function get_media_id($num){
        $token =  \Util\Weixin_sdk\Token::get_token();
        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token={$token}";
        $data = array(
            "type" =>"news",
            "offset"=>($num-1)>0?($num-1):0,
            "count"=>1
        );
        $res = self::post($url,$data);
        $d = json_decode($res,1);
        if(empty($d['item'][0]['media_id'])) return false;
        return array('media_id'=>$d['item'][0]['media_id'],'title'=>$d['item'][0]['content']['news_item'][0]['title']);
    }

    //发送图文消息
    /*
     * $open_id = array(
                "oUBNFs-X9a0EWvaTpmdfV6QGmTnk",
                "oUBNFs7AVYstX-18a7TNS7WLgJDM",
                "oUBNFsxreyrgh21u_nO979wuPqwY"
            ),
       $media_id = "AjzLesu_Ls_8bvA9mq1xFvA__nZN_0DIBmWepBlKZjQ";
     * */
    public static function send_mpnews($open_id,$media_id){
        $token =  \Util\Weixin_sdk\Token::get_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token={$token}";
        $data = array(
            "touser" => $open_id,
            "mpnews" => array(
                "media_id"=>$media_id
            ),
            "msgtype"=>"mpnews"
        );
        $res = self::post($url,$data);
        $d = json_decode($res,1);
        if(intval($d['errcode'])>0||$d['errcode']==-1) return false;
        return true;
    }

    //发送文本消息
    /*
     * $open_id = array(
                "oUBNFs-X9a0EWvaTpmdfV6QGmTnk",
                "oUBNFs7AVYstX-18a7TNS7WLgJDM",
                "oUBNFsxreyrgh21u_nO979wuPqwY"
            ),
       $content = "xxxx";
     * */
    public static function send_text($open_id,$content){
        $token =  \Util\Weixin_sdk\Token::get_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token={$token}";
        $data = array(
            "touser" => $open_id,
            "msgtype"=>"text",
            "text"=>array(
                "content"=>urlencode($content)
            )
        );
        $res = @file_get_contents($url, false, stream_context_create(array('http' => array('method' => 'POST','content' =>urldecode(json_encode($data))))));
        $d = json_decode($res,1);
        if(intval($d['errcode'])>0||$d['errcode']==-1) return false;
        return true;
    }

    //发送图文消息预览
    /*
     * $weixin_account = "xxxxxx"
       $media_id = "AjzLesu_Ls_8bvA9mq1xFvA__nZN_0DIBmWepBlKZjQ";
     * */
    public static function send_mpnews_preview($weixin_account,$media_id){
        $token =  \Util\Weixin_sdk\Token::get_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token={$token}";
        $data = array(
            "towxname" => $weixin_account,
            "mpnews" => array(
                "media_id"=>$media_id
            ),
            "msgtype"=>"mpnews"
        );
        $res = self::post($url,$data);
        $d = json_decode($res,1);
        if(intval($d['errcode'])>0||$d['errcode']==-1) return false;
        return true;
    }

    //发送文本消息预览
    /*
     * $weixin_account = "xxxxxx"
       $content = "xxxx";
     * */
    public static function send_text_preview($weixin_account,$content){
        $token =  \Util\Weixin_sdk\Token::get_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token={$token}";
        $data = array(
            "towxname" => $weixin_account,
            "msgtype"=>"text",
            "text"=>array(
                "content"=>urlencode($content)
            )
        );
        //$res = self::post($url,urldecode($data));
        $res = @file_get_contents($url, false, stream_context_create(array('http' => array('method' => 'POST','content' =>urldecode(json_encode($data))))));
        $d = json_decode($res,1);
        if(intval($d['errcode'])>0||$d['errcode']==-1) return false;
        return true;
    }

    public static function send_template($openid,$template_id,$link,$data){
        $token =  \Util\Weixin_sdk\Token::get_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}";
        $data = array(
            "touser" => $openid,
            "template_id"=>"$template_id",
            "url"=>$link,
            "data" => array(
                "result"=>array(
                    "value"=>$data['result'],
                    "color"=>"#ff3a3a",
                ),
                "withdrawMoney"=>array(
                    "value"=>$data['withdrawMoney'],
                    "color"=>"#ff3a3a",
                ),
                "withdrawTime"=>array(
                    "value"=>$data['withdrawTime'],
                    "color"=>"#173177",
                ),
                "cardInfo"=>array(
                    "value"=>$data['cardInfo'],
                    "color"=>"#ff3a3a",
                ),
                "arrivedTime"=>array(
                    "value"=>$data['arrivedTime'],
                    "color"=>"#173177",
                ),
                "remark"=>array(
                    "value"=>$data['remark'],
                    "color"=>"#173177",
                )
            )
        );
        //$res = self::post($url,urldecode($data));
        $res = @file_get_contents($url, false, stream_context_create(array('http' => array('method' => 'POST','content' =>urldecode(json_encode($data))))));
        $d = json_decode($res,1);
        return $d;
//        if(intval($d['errcode'])>0||$d['errcode']==-1) return false;
//        return true;
    }
}
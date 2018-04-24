<?php
namespace Union\Weixin;
use Util\Weixin_sdk\Request;

/**
 * 响应微信菜单回调
 * Class Menu
 * @package Union\Weixin
 */
class Menu {

    /**
     * @var \Util\Weixin_sdk\Request
     */
    protected $_weixin_request = null;

    /**
     * @var \Util\Weixin_sdk\Response
     */
    protected $_weixin_response = null;

    /**
     * 当前微信open_id绑定的用户信息
     * @var array
     */
    protected $_user_info = [];

    /**
     * @param \Util\Weixin_sdk\Request $weixin_request
     * @param array $user_info 当前微信open_id绑定的用户信息
     */
    public function __construct( $weixin_request, $weixin_response, $user_info ) {
        $this->_weixin_request = $weixin_request;
        $this->_weixin_response = $weixin_response;
        $this->_user_info = $user_info;
    }

    /**
     * 签到
     */
    public function sign() {
//        \Util\Debug::log($this->_user_info, '微信签到检查用户是否已经绑定');
        $view       = \View::i();
        $url_bind   = $view->url(['m' => 'weixin', 'c' => 'user', 'a' => 'login', 'from' => 'wx']);
        $url_income = $view->url(['m' => 'weixin', 'c' => 'income', 'a' => 'index', 'from' => 'wx']);
        if (empty($this->_user_info['uid'])) {
            //未绑定
            $msg = "您还未绑定7654账号!现在<a href='{$url_bind}'>绑定</a>绑定后您可以随时随地<a href='{$url_income}' >查收入</a>";
            return $msg;
        }

        $uid    = $this->_user_info['uid'];
        $sign   = \union\user\sign::i();
        if ($sign->is_singed($uid)) {
            //已签到
            $msg = "今天已经签过了哦！明天再来吧！<a href='{$url_income}' >查收入</a>";
        } else {
            $msg = "签到成功！6 积分已飞进您的账户哦！<a href='{$url_income}' >查收入</a>";
            if (!$sign->exe($uid, $this->_user_info['username'], 2)) $msg = "签到异常，请稍候再试";
        }
//        \Util\Debug::log($msg);
        $msg = $this->_weixin_response->response( $msg );
        return $msg;
    }

    /**
     * 用户关注微信号
     */
    public function subscribe() {

        //$act_hongbao = new \Union\Weixin\Act_hongbao();
//        $act_hongbao = new \Union\Weixin\Act_qhongbao();
//        $ret = $act_hongbao->event_user_subscribe( $this->_weixin_response );
//        if ( !empty($ret) ) return $ret;

        $view = \View::i();
        $url_bind = $view->url(['m' => 'weixin', 'c' => 'act_new_partner', 'a' => 'index', 'from' => 'wx']);
        $msg = "感谢您关注7654技术员联盟，点击领取<a href='{$url_bind}'>10元新人福利</a>！";
        $msg = $this->_weixin_response->response( $msg );
        return $msg;
    }

    public function act_subscribe(){
        $items = [
            [
                'title'            => '520浪漫礼包，只为专注的你！',
                'description'     => '专属你的浪漫礼包~',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz/k6dHuLOhvAy5jn7qNLnGVxfgBOKHCe6w7STf8JQngfCLxZ3aiaF9lbHXYHpFOPibMUWH7dPTCvrKnNhhFiakDYU4g/0?wx_fmt=jpeg',
                'url'              => 'https://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=502928605&idx=1&sn=3b98b37a5d8f951b437769b88acf6613&scene=0&previewkey=3OmZ%2B7bOFY3uGLCdOss2%2BcNS9bJajjJKzz%2F0By7ITJA%3D&pass_ticket=0kpQF0%2BqsIxOZAtNvuXGg1wtPL5FzJucPcchjyDc9sB6QRXxuvjfejHfk0NLK40w',
            ]
        ];
        return $this->_weixin_response->image_text( $items );
    }

    /**
     * 微信分享活动
     */
    public function act_share(){
        $items = [
            [
                'title'            => '无限红包+10%积分提成，不心动没道理！',
                'description'     => '肯定是夏天到了，洪荒之力再也抑制不住了……',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz/k6dHuLOhvAyltBOZuic6iaia5BqibbNgWOqrxpLDXcAc1muRHUXnibulWt9wRZQBFWJfAG2WtQC6aSDQZfVX2svbooA/0?wx_fmt=jpeg',
                'url'              => 'http://mp.weixin.qq.com/s__biz=MzAwNzY0ODU5Mg==&mid=2650412288&idx=1&sn=963f183159bea3a124588075611574ea&scene=0#wechat_redirect',
            ]
        ];

        return $this->_weixin_response->image_text( $items );
    }
    /**
     * 微信抽奖
     */
    public function weixin_luck(){
        $items = [
            [
                'title'            => '福利｜天气一天比一天热、抽个大奖清凉一夏吧',
                'description'     => '天气这么热，你热，TA也热',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz/k6dHuLOhvAz56R7UBq7Yn6u3UC4JGtIhMJbKXau8zJYrxBgSFejHicyxAVibCOibXBlp5ic4pEmNBJlZ6miadbnE8vA/0?wx_fmt=jpeg',
                'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=2650412305&idx=1&sn=8e7bc54bf14843283b06482011d9071d&scene=0#wechat_redirect',
            ],
        ];
        return $this->_weixin_response->image_text( $items );
    }
  
  
    /**
     * 微信快压
     */
    public function weixin_kzip(){
        $items = [
            [
                'title'            => '快压周周赛，最高可获80元现金红包！',
                'description'     => '领红包给妹子买冰淇淋降降温哦',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz/k6dHuLOhvAwjLwXONTxUwtXicZQm5Q4765Yz2vsC5Le82Ym8BiaKWcRyanrOLl8VD594Ot3yfCRQOflAoqGxiaHbA/0?wx_fmt=jpeg',
                'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=2650412304&idx=1&sn=b928cecc12699162c2875f5a78a0c265&scene=0#wechat_redirect',
            ],
        ];
        return $this->_weixin_response->image_text( $items );
    }

    /**
     * 微信夺宝
     */
    public function weixin_duobao(){
        $items = [
            [
                'title'            => '1000积分，换你所想，就是这么任性！',
                'description'     => '长得好看的人都点了！！\(≧▽≦)/',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz_jpg/k6dHuLOhvAwQiaXhR0KPSzMBjMnibRmkYNJsKDPArdTJb8gIzsicDBeTA6fbnAq7uKcOINFGAK7wkM5ZsZ4cKfibsA/0?wx_fmt=jpeg',
                'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=2650412315&idx=1&sn=291ac92e7bece09f82a150d109a3768e&scene=0#wechat_redirect',
            ],
        ];
        return $this->_weixin_response->image_text( $items );
    }
    /**
     * 微信大转盘
     */
    public function weixin_dial(){
        $items = [
            [
                'title'            => 'Q管单包涨价2.2元，100%送话费、送流量！',
                'description'     => '[100%中奖]先定个小目标，比如每天领60元话费~',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz_png/k6dHuLOhvAwndMdFeAh6QYmAjonBDAUiaTq0YzEYoxec0iaicUPKsklfNULbk28E5xSOOWDz3WbPmmlCiaibmp0U63g/0?wx_fmt=png',
                'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=2650412323&idx=1&sn=a0bf6903665925d65214f8e9a96a7bb2&scene=0#wechat_redirect',
            ],
            [
            'title'            => '我用1000积分换了小米电视，那你呢？',
            'description'     => '1000积分=iPhone6s',
            'picture'          => 'https://mmbiz.qlogo.cn/mmbiz_jpg/k6dHuLOhvAwndMdFeAh6QYmAjonBDAUiaK9N3rJZd9UFJSboT2Y7hC3cZLwEqujSUssicCEyxIASuO4lzGto7GUA/0?wx_fmt=jpeg',
            'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=2650412328&idx=2&sn=e2accc2c98b1b7abfa1d9e275c69448b&scene=0#wechat_redirect',
            ],
        ];
        return $this->_weixin_response->image_text( $items );
    }

    /**
     * Q管天天领
     */
    public function weixin_qq(){
        $items = [
            [
                'title'            => '为祖国麻麻庆生，先领了这50元',
                'description'     => '就是这么壕~',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz_jpg/k6dHuLOhvAyX7FR2cFNtkSFfOwOibTcMsuKSpk4ROGvs8ZGNaTJqT2gsA5dSfslIygJJ1eRGaT79fEecDAaBANQ/0?wx_fmt=jpeg',
                'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=2650412339&idx=1&sn=45b43d7e7ef38888bef9cecc97ceb471&chksm=8374510cb403d81abc290795eae632f0ac68e988f929f4e2e083ebd535e02fecd718eeadbea6&scene=0#wechat_redirect',
            ],
        ];
        return $this->_weixin_response->image_text( $items );
    }
    /**
     * 五月端午Q管
     */
    public function act_dw(){
        $items = [
            [
                'title'           => '万水千山“粽”是情，Q管红包拿不停~~~',
                'description'     => '预祝粽子节快乐',
                'picture'         => 'https://mmbiz.qlogo.cn/mmbiz_png/k6dHuLOhvAyr2icMeZLyDdldA8vyAprfhqSib7NLjUmcIluv1k8fTia8hwxdvKhxeWJKRaAI8QzTfbRwfl98T6Dwg/0?wx_fmt=png',
                'url'             => 'http://mp.weixin.qq.com/s/qIr2Z7LWkkmD6OE5sZCUyw',
            ],
        ];
        return $this->_weixin_response->image_text( $items );
    }

    /**
     * 扫描二维码进入已经关注
     */
    public function scan() {
        $view = \View::i();
        $url_income = $view->url(['m' => 'weixin', 'c' => 'income', 'a' => 'index', 'from' => 'wx']);
        $url_bind = $view->url(['m' => 'weixin', 'c' => 'user', 'a' => 'login', 'from' => 'wx']);
        if (empty($this->_user_info['uid'])) {
            //未绑定
            $msg = "感谢您关注7654技术员联盟!\n 现在<a href='{$url_bind}'>绑定</a>\n绑定后您可以随时随地\n<a href='{$url_income}' >查收入</a>";
        }
        else {
            $msg = "感谢您关注7654技术员联盟！\n<a href='{$url_income}' >查收入</a>";
        }
        $msg = $this->_weixin_response->response( $msg );
        return $msg;

    }
    /**
     * 分享我的二维码
     */
    public function images(){
        //https://api.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type=TYPE
        $view       = \View::i();
        $url_bind   = $view->url(['m' => 'weixin', 'c' => 'user', 'a' => 'login', 'from' => 'wx']);
        if (empty($this->_user_info['uid'])) {
            //未绑定
            $msg = "您还未绑定7654账号!现在<a href='{$url_bind}'>绑定</a>";
            $msg = $this->_weixin_response->response( $msg );
            return $msg;
        }else{
            $exit_images = '/app/www/7654portal/Uploads/code/'.$this->_user_info['uid'].'.png';
            $exit_save_images = "/app/www/7654portal/Uploads/code/".$this->_user_info['uid']."_new.png";
            if(!fopen($exit_save_images,'r')){
                $idcode = \Dao\Union\User::get_instance()->get_one('idcode','id ='.$this->_user_info['uid']);
                //邀请url
                $url = 'http://m.7654.com/?'.$idcode;
                define('QR_LOGO', './Public/web/img/7654.png');
                \Util\Tool::generate_qrcode($url,$exit_images,2,6,3);
                if(fopen($exit_images,'r')){
                    $this->numimage($exit_images,$exit_save_images);
                }else{
                    $msg = "暂无法生成二维码";
                    return $this->_weixin_response->response( $msg );
                }
            }
            $type = "image";
            $filedata = array("media" => "@".$exit_save_images);
            $token =  \Util\Weixin_sdk\Token::get_token();
            $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$token}&type={$type}";
            $request = new Request();
            $result = $request->https_request($url,$filedata);
            $arr_result = json_decode($result,true);
//            \Util\Debug::log('微信调试'.$arr_result);
            $media_id = $arr_result['media_id'];
            return $this->_weixin_response->images( $media_id );

        }
    }

    public function numimage($exit_images,$exit_save_images)
     {
         $imagefile = $exit_images;
//待添加文字
        $str = "(1）分享二维码,成功邀请1人注册
 即可获得1次抽奖机会，上不封顶!
(2）邀请的新人关注7654微信号
（gaoxin7654）可领取10元红包。

  邀请人数无上限，100%中奖！";

    //由于掺杂汉字,原生函数无法统计真实字符数，造成水印文字无法居中

    //所以自己写了个函数统计，如果有mbstring库支持，换用mb_strlen也可以

        $len = $this->str_len($str);
        //如果源程序基于utf-8编码，不需要做字符转换，删除此行

        //$str = iconv('gb2312','utf-8',$str);

        //获取原图大小

        $size = getimagesize($imagefile);

        //底边矩形高度

        $bottom_height = 140;

        //字体大小

        $font_size = 10;

        $im = imagecreatetruecolor($size[0], $size[1]+$bottom_height);

        //底边矩形背景色，修改最后三个RGB参数改变颜色

        $bgcolor = imagecolorallocate($im,255,255,255);

        //字体颜色

        $ftcolor = imagecolorallocate($im,0,0,0);

        imagefill($im,0,0,$bgcolor);

        //默认从jpeg创建，如从其他图片创建，可根据扩展名选择函数

        $jpeg = imagecreatefrompng($imagefile);

        imagecopy($im,$jpeg,0,0,0,0,$size[0],$size[1]);

        $start_x = ($size[0]-$len*$font_size)/2;

        $start_x = ($start_x>0?$start_x:0);

        $start_y = $size[1]+$font_size+($bottom_height-$font_size)/2 - 50;

        //C:/windows/fonts/SIMHEI.TTF为ttf字库文件，此处为黑体

        imagettftext($im,$font_size,0,$start_x,$start_y,$ftcolor,"/usr/share/fonts/simhei.ttf",$str);

        header("Content-type: image/png");

        imagepng($im,$exit_save_images);

        imageclose($im);

        imageclose($jpeg);
     }

    public function str_len($str){

        $length=0;

        for($i=0;$i<strlen($str);$i++){

            if(ord($str[$i])>0x80)

                $i++;

            $length++;

        }

        return $length;

    }

    /**
     * 活动预告
     */
    public function activity_notice() {

        $items = [
            [
                'title'            => '7654现金红包，三月来袭 ',
                'description'     => '联盟首创真正的现金红包，即将来袭',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz/k6dHuLOhvAwFUOnKoYTK6jPKvuOy7C8NPkSmNy2zIqTuDPFump70Gbia6Pf3KTC6WHfDxWP2aB0ic5wScicZficgDQ/0?wx_fmt=png',
                'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=402564395&idx=1&sn=6eaa76d5fddbf5d791d286e800c9b901&scene=0&previewkey=yK%2F9ma5m%2F0bDjBTVfNYsbsNS9bJajjJKzz%2F0By7ITJA%3D#wechat_redirect',
            ]
        ];
//        $ext = \Config::get( 'weixin_image_text_common', null, [], 'weixin' );
//        $items = array_merge( $items, $ext );
        return $this->_weixin_response->image_text( $items );

    }

    /**
     * 帮助中心
     * @return string
     */
    public function help() {
        $items = [
            [
                'title'            => '7654帮助中心',
                'description'     => '7654推广中常见问题帮助',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz/k6dHuLOhvAyaUzialhBX4vMBdIbzsTydDzHOwkrZSSfgvIIuex2rIMIaq2IohGbpDwIhZrmF7gCFC9g59GO6onA/0?wx_fmt=jpeg',
                'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=402566446&idx=1&sn=48d1e11e4fe33070aced67cab6e4a447&scene=0&previewkey=yK%2F9ma5m%2F0bDjBTVfNYsbsNS9bJajjJKzz%2F0By7ITJA%3D#wechat_redirect',
            ]
        ];
        return $this->_weixin_response->image_text( $items );
    }

    /**
     * 赚钱指南
     */
    public function earn_money() {
        $items = [
            [
                'title'            => '赚钱指南',
                'description'     => '7654技术联盟新手怎么推广赚钱？',
                'picture'          => 'https://mmbiz.qlogo.cn/mmbiz/k6dHuLOhvAyaUzialhBX4vMBdIbzsTydDFfPK2ibZOJODMRIk3PmffIF7dHuS96Fq2aXyBibz6TdzWQiafsWoRMlfQ/0?wx_fmt=jpeg',
                'url'              => 'http://mp.weixin.qq.com/s?__biz=MzAwNzY0ODU5Mg==&mid=402562592&idx=1&sn=a4f646fa28a07b0555cf7ada34295519&scene=0&previewkey=yK%2F9ma5m%2F0bDjBTVfNYsbsNS9bJajjJKzz%2F0By7ITJA%3D#wechat_redirect',
            ]
        ];
        return $this->_weixin_response->image_text( $items );
    }

    public function url( $param ) {
        return Url::get_instance()->make($param);
    }
}
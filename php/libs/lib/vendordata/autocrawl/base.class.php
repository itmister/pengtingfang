<?php
namespace VendorData\AutoCrawl;
use \Io\Http;
abstract class Base {
    private static $_instace = [];
    protected $_errno = 1;
    protected $_error = '';

    protected $_login_url = '';
    protected $_login_params = [];
    protected $_get_data_url = '';
    protected $_captcha_path = '';
    protected $_cookiejar_file = "";

    public static function get_instance($class = __CLASS__){
        if(isset(self::$_instace[$class])){
            return self::$_instace[$class];
        }else {
            self::$_instace[$class]=new $class();
        }
        return self::$_instace[$class];
    }


    public function __destruct(){
        //删除cookie 文件
        @unlink($this->_cookiejar_file);
        if ($this->_captcha_path){
            @unlink($this->_captcha_path);
        }
    }

    /**
     * 接口返回false时，通过本接口获取详细的错误信息
     * @return array('errno'=>**, 'error'=>**)
     */
    public function getErrorInfo() {
        return array('errno'=>$this->_errno, 'error'=>$this->_error);
    }

    /**
     * 将其他接口的错误信息传递到当前组件
     * @param array $err getErrorInfo()返回的结果
     */
    public function setErrorInfo($err) {
        $this->_errno = $err['errno'];
        $this->_error = $err['error'];
    }

    /**
     *
     * @param unknown $error_code
     * @param unknown $error_message
     */
    protected function coverError($error_code,$error_message){
        $this->_errno = $error_code;
        $this->_error = $error_message;
    }


    /**
     * 生成唯一的cookiejar
     * @param $prefix
     * @return string
     */
    protected function make_cookie_jar($prefix ){
        return  tempnam("/tmp",$prefix);
    }

    /**
     * 模拟登录
     * @param $url
     * @param $method
     * @param $params
     * @param $cookiejar
     * @return string
     */
    protected function login($url,$method, $params,$cookiejar,$option=[]) {
        $option['cookiejar'] = $cookiejar;
        return Http::http($url,$method,$params,$option);
    }

    /**
     * 登录成功后获取数据
     * @param $url
     * @param $cookiejar
     * @return string
     */
    protected function get_content($url, $params,$cookiejar,$option=[]) {
        $option['cookiejar'] = $cookiejar;
        return Http::get($url,$params,$option);
    }

    /**
     * 下载文件
     * @param string $url
     * @param string $save_path 保存到本地的目录
     * @return string
     */
    function get_remote_file($url = "",$save_path = "",$params,$cookiejar){
        $option['file'] = $save_path;
        $option['cookiejar'] = $cookiejar;
        return Http::get($url,$params,$option);
    }

    /**
     * 登录成功后获取数据
     * @param $url
     * @param $params
     * @param $cookiejar
     * @return string
     */
    function post_content($url, $params,$cookiejar,$option=[]) {
        $option['cookiejar'] = $cookiejar;
        return Http::post($url,$params,$option);
    }

    /**
     * @param string $url
     * @param $cookiejar
     * @param string $pic_save_path
     * @return string
     */
    function get_captcha($url = "",$cookiejar,$pic_save_path = ""){
        $option['cookiejar'] = $cookiejar;
        $option['file'] = $pic_save_path;
        return Http::get($url,'',$option);
    }


    /**
     * 自动识别验证码
     * @param $file
     * @param string $codeType  1060 6位英文+数字  1040 4位英文+数字
     * @return string
     */
    function identification_captcha($file,$code_type = '1060'){
        $cfile = curl_file_create($file);
        $data=array(
            'type'=>'recognize',
            'softID'=>'3',
            'softKey'=>'623527b90698a47ec626043dac04a0f1',
            //'userName'=>'gxzm@021.com',
            'userName'=>'gx@021.com',
            //'passWord'=>'zhanmeng123',
            'passWord'=>'Q!GaoXin88A',
            'imagePath'=>$cfile,
            'codeType'=>$code_type,
            'timeout'=>'60',
            'remark'=>'',
            'log'=>'0',
            'upload'=>'开始识别'
        );
        $file_size = filesize($file);
        $reponse = Http::post('http://ff.zhima365.com/ZMDemo_PHP/Demo.php',$data,['file_size'=>$file_size]);
        if ($reponse){
            $_code = explode("|",trim($reponse));
            $code = $_code[1];
            if ($code){
                return $code;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
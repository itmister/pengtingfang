<?php
/**
 *
 */
namespace Util;
class Curl_up {
    /**
     * 上传文件最大大小
     * @var int
     */
    private static $maxsize = 2097152;
    /**
     * 上传文件扩展名
     * @var string
     */
    private static $extension = "jpg,png,jpeg";
    /**
     * 自动获取上传文件的信息，目前只能处理单个文件
     * @return array
     */
    private  static function fileinfo(){
        $fileinfo = array();
        foreach($_FILES as $v) {
            $fileinfo = $v;
            break;
        }
        $filename = $fileinfo['name'];
        return array_merge($fileinfo,pathinfo($filename));
    }

    /**
     * 自动重命名上传的临时文件
     * @return mixed|string
     */
    private  static function re_tmp_name($ccg_name = false){
        $fileinfo = self::fileinfo();
        if($ccg_name == false){
            $ccg_name = str_replace(array('{','}','-'),'',microtime(true));
        }
        $nt_path = str_replace(basename($fileinfo['tmp_name']),$ccg_name.".{$fileinfo['extension']}",$fileinfo['tmp_name']);
        if(rename($fileinfo['tmp_name'],$nt_path)) {
            return $nt_path;
        } else {
            return '';
        }
    }

    /**
     * curl的post文件上传方法
     * @param $url
     * @param array $data
     * @return mixed|string
     */
    public static function post($url,$data=array()){
        $fileinfo = self::fileinfo();
        if(!in_array(strtolower($fileinfo['extension']),explode(",",self::$extension))) {
            return " {$fileinfo['extension']} extension is error!";
        }
        if($fileinfo['size']>self::$maxsize) return " {$fileinfo['size']} over max !";
        if($data['rename']) $rename = $data['rename'];//算定义生命名
        $re_tmp_name = self::re_tmp_name($rename);
        $post_data = array(
            "img" => new \CURLFile($re_tmp_name)
        );
        if(is_array($data)&&!empty($data)) $post_data = array_merge($post_data,$data);
        return self::c_post($url,$post_data);
    }

    /**
     * curl上传文件方法
     * @param $url
     * @param $post_data
     * @return mixed|string
     */
    private static function c_post($url,$post_data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3); //建立连接超时
        curl_setopt($curl, CURLOPT_TIMEOUT, 300); //最大持续连接时间
        $result = curl_exec($curl);
        $error = curl_error($curl);
        if($result) {
            unlink($post_data['img']);
            return $result;
        }
        return $error;
    }

    /**
     * 批量处理文件在本地位置信息
     * @return array
     */
    public static function batch_files_path($url){
        $data = array();
        foreach($_FILES as $k=>$v) {
            $v['form_name'] = $k;
            $data[] = array_merge($v,pathinfo($v['name']));
        }
        foreach($data as $_k=>$_v){
            $nt_path = str_replace(basename($_v['tmp_name']),str_replace(array('{','}','-'),'',microtime(true)).".{$_v['extension']}",$_v['tmp_name']);
            if(rename($_v['tmp_name'],$nt_path)) {
                $data[$_k]['link'] = self::c_post($url,array('img'=>'@'.$nt_path));
            } else {
                unset($data[$_k]);
            }
        }
        return $data;
    }
    
    /**
     * 上传
     */
    public static function upload($url = '',$params = array()){
        //defined( 'MFS_URL' ) or define('MFS_URL','http://test.mfsserver.shgaoxin.net/');
        if(!$url){
            define('MFS_URL','http://mfsserver.shgaoxin.net/');
        }else{
            define('MFS_URL',$url);
        }
        
        $mfs = self::post(MFS_URL,$params);
        $arr = array();
        if(!preg_match('/^http:\/\/.*$/',$mfs)) {
            $arr['error'] = $mfs;
        } else {
            $arr['error'] = '';
            $arr['success'] = true;
            $arr['file'] = $mfs;
        }
        exit(json_encode($arr));
    }
}
		?>
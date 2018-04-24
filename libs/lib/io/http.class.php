<?php
namespace Io;
/**
 * Class Http
 * @package Io
 */
class Http {
    static $errno = 0; // 错误码
    static $errro = ''; // 错误信息
    public static $url = '';

    /**
     * Make an HTTP request
     * @param string $url: 请求url
     * @param string $method: 请求方法，目前只有"GET", "POST"
     * @param mix $data_fields: 查询数据
     * @param array $option: 附加选项
     * @return string HTTP请求的响应
     */
    public static function http($url, $method = 'GET', $data_fields = array(), $option = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);
        if (!empty($option['header'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $option['header']);
        }
        if (!empty($option['useragent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $option['useragent']);
        }
        if (!empty($option['referer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $option['referer']);
        }
        if (isset($option['cookiejar']) && file_exists($option['cookiejar'])) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $option['cookiejar']);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $option['cookiejar']);
        }
        if (!empty($option['cookie'])) {
            curl_setopt($ch, CURLOPT_COOKIE, $option['cookie']);
        }
        if (!empty($option['proxy'])) {
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
            curl_setopt($ch, CURLOPT_PROXY, $option['proxy']);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }
        if (!empty($option['file'])){
            $fp = fopen($option['file'],'wb');
            curl_setopt($ch,CURLOPT_FILE,$fp);
        }
        if (!empty($option['file_size'])){
            curl_setopt($ch,CURLOPT_INFILESIZE,$option['file_size']);
        }
        if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        if (!empty($option['ip'])){
            $ip = mt_rand(0, 255).'.'.mt_rand(0, 255).'.'.mt_rand(0, 255).'.'.mt_rand(0, 255);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Client-Ip: '.$ip,'X-Forwarded-For: '.$ip,'REMOTE_ADDR'));
        }
        
        // 设置查询数据
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if (!empty($data_fields)) {
                    if (!empty($option['file_size'])){
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
                    }else{
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_fields));
                    }
                }
                break;
            case 'GET':
            default:
                // 如果为GET方法，将$data_fields转换成查询字符串后附加到$url后面
                if (!empty($data_fields)) {
                    $join_char = strpos($url, '?') === false ? '?' : '&';
                    $url .= $join_char . http_build_query($data_fields);
                }
                break;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        if (parse_url($url, PHP_URL_SCHEME) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        }


        $_time_start = microtime(true);
       // dump(curl_getinfo($ch));
        $response = curl_exec($ch);
        if ($method == 'POST') {
            $action = "POST $url with " . http_build_query($data_fields);
        } else {
            $action = "GET $url";
        }
        self::$url = $action;
/*        var_dump($option);
        echo $action;*/
        $http_info = curl_getinfo($ch);
        $log_attrs = array('total_time', 'namelookup_time', 'connect_time', 'pretransfer_time', 'starttransfer_time','content_type');
        $log_info = array();
        foreach ($log_attrs as $key) {
            $log_info[$key] = $http_info[$key];
        }
      //  dump(curl_getinfo($ch));
        if (self::$errno = curl_errno($ch)) {
            self::$errro = curl_error($ch);
            $log_str = "Error at $action (errno: ".self::$errno.", error:".self::$errro.")";
            //TASKLibLogger::rpc($log_str.' #httpinfo('.json_encode($log_info).')', microtime(true) - $_time_start, '', '', TASK_LOG_RPC_TYPE_CURL,true);
            return false;
        }
        if (!empty($option['file'])){ //下载文件时返回的不是文件
            list($mine_type,$charset) =  explode(";", $http_info['content_type']);
            if ($mine_type == 'text/html'){
                return false;
            }
        }
       // TASKLibLogger::rpc($action.' #httpinfo('.json_encode($log_info).')', microtime(true) - $_time_start, '', '', TASK_LOG_RPC_TYPE_CURL,true);

        $last_url = $http_info['url'];
        $http_code = $http_info['http_code'];
        $url_log = $url!=$last_url ? ", url:$last_url" : '';
        if ($http_code != 200) {
            $log_str = "Error at HTTP_STATUS $url (http_code:$http_code {$url_log})";
            //TASKLibLogger::rpc($log_str, microtime(true) - $_time_start, '', '', TASK_LOG_RPC_TYPE_CURL,true);
            //echo $log_str;
        }
        curl_close($ch);
        unset($ch);
        return $response;
    }


    /**
     * @param $url
     * @param array $data_fields
     * @param array $option
     * @return string
     */
    public static function get($url,$data_fields = array(), $option = array()){
        return self::http($url, 'GET', $data_fields, $option);
    }

    /**
     * @param $url
     * @param array $data_fields
     * @param array $option
     * @return string
     */
    public static function post($url,$data_fields = array(), $option = array()){
        return self::http($url, 'POST', $data_fields, $option);
    }
}
<?php
/**
 * ajax 统一返回接口
 */
namespace Util;
class Ajax_Response {
    /**
     * jsonp参数，如果为空字符串则是json格式
     * 通过这个静态属性可以全局统一设置返回格式
     */
    public static $jsonp = null;
    /**
     * 将Ajax请求的返回值进行统一编码
     * @param int $errno 错误代码，0表示没有错误
     * @param string $message 错误描述
     * @param mix $data ajax返回的数据
     * @param string $jsonp jsonp的回调函数名称，如果为空则返回一个json格式数据，否则返回一个jsonp的数据调用
     * @param boolean $output 输出还是返回，默认为输出
     * @return mixed 如果$output为false，则返回数据
     */
    public static function convert($errno = 0, $message = '', $data = null, $jsonp = '', $output = true ) {
        $response = array (
            'code' => $errno,
            'message' => $message,
            'data' => $data
        );
        $response = json_encode($response);
        if (isset(self::$jsonp)) {
            if (self::$jsonp) {
                $response = self::$jsonp . "($response);";
            }
        } else if ($jsonp) {
            $response = "$jsonp($response);";
        }
        if ($output) {
            echo $response;
            return true;
        } else {
            return $response;
        }
    }

    /**
     * 等同于convert(0, '', $data, $jsonp);
     * @param data: ajax返回的数据
     * @param string $jsonp jsonp的回调函数名称，如果为空则返回一个json格式数据，否则返回一个jsonp的数据调用
     */
    public static function convertData($data, $message = '',$jsonp = '') {
        self::convert(0, $message, $data, $jsonp,true);
    }

    /**
     * 等同于convertError($errno, $message, null, $jsonp);
     * @param errno: 错误代码，0表示没有错误
     * @param message: 错误描述
     * @param string $jsonp jsonp的回调函数名称，如果为空则返回一个json格式数据，否则返回一个jsonp的数据调用
     */
    public static function convertError($errno, $message, $jsonp = '') {
        self::convert($errno, $message, null, $jsonp);
    }

    /**
     * 检测ajax请求
     * 通过前端js框架在查询参数或者HTTP Header加入特定键值来确定是否是ajax请求
     */
    public static function isAjaxRequest() {
        if (isset($_REQUEST[_jsonp])) return true;
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])?$_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest' : false;
    }
}
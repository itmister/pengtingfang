<?php
class Io {

    /**
     * 格式化输出变量并退出
     * @param $var
     */
    public static function dead( $var ) {
        if (PHP_SAPI == 'cli' ) {
            print_r($var);
            echo "\n";
            die();
        }
        header("content-Type: text/html; charset=utf-8");
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        die();
    }

    public static function output( $var) {
        print_r($var);
        echo PHP_SAPI == 'cli' ? "\n" : '<br />';
    }

    /**
     * 输出json文本
     * @param $var
     */
    public static function json( $var ) {
        if ('cli' !== PHP_SAPI ) header("content-Type: application/json; charset=utf-8");
        echo json_encode( $var );
    }

    /**
     *
     * 输出调试信息至firebug
     * @param $var
     */
    public static function fb( $var ) {
        \Util\Fire_PHP\fb::log( $var );
    }

    /**
     * 取http外部文件
     * @param string $url
     * @param [] $params
     * @param [] $opt 选项
     * @return string
     */
    public static  function get_http( $url, $params = [], $opt = [] ) {
        $string = \Io\Curl::get_instance()->get($url, $params, $opt );
        return $string;
    }

}
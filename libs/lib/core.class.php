<?php
/**
 * lib核心类
 */
namespace Lib;

class Core {
    /**
     * 自动加载
     * @param $class_name
     */
    public static function autoload( $class ) {
        $class_name = strtolower( $class );
        $path_file = str_replace('\\', '/', $class_name) . '.class.php';

        $path_list = [];
        if (defined('PATH_APP_LIB')) $path_list[] = PATH_APP_LIB;
        if (defined('PATH_LIB')) $path_list[] = PATH_LIB;
        if (defined('PATH_CONTROLLER')) $path_list[] = PATH_CONTROLLER;
        if (defined('APP_PATH')) $path_list[] = APP_PATH;
        if (defined('PATH_APP')) $path_list[] = PATH_APP;

        foreach ( $path_list as $path ) {
            $file = $path . $path_file;
            if (is_file($file )) {
                require_once $file;
                return;
            }
        }
        if ( function_exists('__autoload')) __autoload( $class );//兼容其它框架的__autoload

    }

    /**
     * 格式化输出变量并退出
     * @param $var
     */
    public static function dead( $var ) {
        if (PHP_SAPI == 'cli' ) {
            print_r($var);
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
     *
     * 输出调试信息至firebug
     * @param $var
     */
    public static function fb( $var ) {
        \Util\Fire_PHP\fb::log( $var );
    }

    /**
     * 程序出错,中断操作
     * @param $code
     * @param bool $info
     */
    public static function error( $code, $info = false ) {
        self::dead( $code );
    }

    /**
     * 开始
     */
    public static function start() {
        require_once PATH_LIB . 'dao.class.php';
        require_once PATH_LIB . 'mongo.class.php';
        require_once PATH_LIB . 'io.class.php';
        spl_autoload_register('\Lib\Core::autoload');

        if ( defined('THINK_VER') ) {
            //引入ThinkPHP
            define('THINK_PATH', PATH_LIB . '../think_src/' . THINK_VER . '/');
            require THINK_PATH . '/start.php';//引入ThinkPHP
        }
        else {
            if (!defined('IS_CGI')) define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
            if (!defined('IS_WIN')) define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
            if (!defined('IS_CLI')) define('IS_CLI',PHP_SAPI=='cli'? 1 :  0);

            if( defined('APP_PATH') &&
                ( !defined('NO_CONTROLLER') || !NO_CONTROLLER ) ) {
                $ret = \Core\Router::dispatch();
//                $ret = false;
                if (false === $ret && defined('OTHER_FRAME_INTERFACE') ) require OTHER_FRAME_INTERFACE;
            }
        }
    }

    /**
     *
     * 配置取与设置
     * @param string|array $name 配置变量
     * @param mixed $value 配置值
     * @param mixed $default 默认值
     * @param string $file_reload 重新加载配置文件
     * @return mixed
     */
    public static function config( $name = null, $value = null, $default = null, $file_reload = '' ) {

        static $_config = array();
        if ( !empty($file_reload) ) {
            //重新加载配置文件

            if ( defined('PATH_APP_CONF') && is_file( PATH_APP_CONF . $file_reload . '.php' ) ) {
                $_config = array_merge( $_config, array_change_key_case( include  PATH_APP_CONF . $file_reload . '.php'  ) );
            }
        }
        if (empty($_config)) {
            $file_cfg = PATH_LIB . 'conf/' . gethostname() . '.php';
            if (!is_file( $file_cfg)) $file_cfg = PATH_LIB . '/conf/default.php';
            if (is_file( $file_cfg )) {
                $cfg = include $file_cfg;
                $_config = array_merge( $_config, array_change_key_case( $cfg ) );
            }

            //加载项目配置文件
            if ( defined('PATH_APP_CONF') && is_file( PATH_APP_CONF . 'config.php' ) )
                $_config = array_merge( $_config, array_change_key_case( include  PATH_APP_CONF . 'config.php' ) );
        }

        if ( empty($name) ) return null;
        if ( is_string($name) ) {
            $name = strtolower($name);
            if ( is_null($value) ){
                if ( isset($_config[$name]) ) return $_config[$name];
                if (function_exists('C')) return C( $name, $value, $default );
                return $default;
            }
            $_config[$name] = $value;
        }
        if ( is_array( $name ) ) $_config = array_merge( $_config, array_change_key_case( $name ) );
        return true;

    }
}
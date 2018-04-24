<?php
namespace Core;

/**
 * 路由
 * Class Router
 * @package Core
 */

class Router {

    public static function dispatch() {
        defined('PATH_CONTROLLER') OR define('PATH_CONTROLLER', APP_PATH . 'controller/' );

        //$is_hook_url = false;
        $uri_split = '/';
        if ( IS_CLI ) {
            $uri = !empty($_SERVER['argv'][1]) ?  $_SERVER['argv'][1]  : '';

        }
        else {
            $uri  = !empty($_GET['s']) ? $_GET['s'] : '';
            if (empty($uri) && !empty( $_SERVER['REQUEST_URI'])) $uri = trim( $_SERVER['REQUEST_URI'] );
            if ( !empty($uri) ) {
                $arr = explode('?', $uri);
                $uri = $arr[0];
            }

            if ( defined('HOOK_CONTROLLER') && HOOK_CONTROLLER ) {
                $hook_url_router = \Config::get('hook_url_router');
                if (!empty($hook_url_router) && isset($hook_url_router[$uri])){
                    $uri = $hook_url_router[$uri];
                }
            }

        }

        if (!empty($uri)) $uri = substr($uri, 0, 1) == $uri_split ?  substr($uri,1) : $uri;
        $arr                = !empty($uri) ? explode( $uri_split, $uri ) : array();
        if ( !empty($_REQUEST['m']) ) {
            $arr[0] = trim ( $_REQUEST['m'] );
            $arr[1] = trim( !empty($_REQUEST['c']) ? $_REQUEST['c'] : '' );
            $arr[2] = trim( !empty($_REQUEST['a']) ? $_REQUEST['a'] : '' );
        }

        $module_name        = strtolower( empty( $arr[0]) ? 'index' : $arr[0] );
        $controller_name    = strtolower( empty( $arr[1]) ? 'index' : $arr[1] );
        $action_name        = strtolower( empty( $arr[2]) ? 'index' : $arr[2] );
        try {
            $arr_params         = array();
            $len                = count($arr);
            for ($i = 3; $i<= $len; $i+= 2) {
                if (!isset($arr[$i])) break;
                $param_name = trim($arr[$i]);
                $arr_params[$param_name] = isset($arr[$i + 1]) ? $arr[$i + 1] : '';
            }

            //参数保存至Params
            $obj_params = \Util\Params::get_instance();
            foreach ($arr_params as $key => $value ) $obj_params->set($key, $value);

            if (defined('error_log_split') && error_log_split )
                ini_set('error_log', str_replace('php_error.log', "{$module_name}_{$controller_name}_{$action_name}_error.log", ini_get('error_log')));

            $controller_class_name = $module_name . '\\controller\\' . $controller_name . '_controller';
            if (!class_exists($controller_class_name)) throw new \Exception( 'controller not exist:' . $controller_class_name , -11);

            $obj_controller = new $controller_class_name( $module_name, $controller_name, $action_name );
            if ( !method_exists( $obj_controller, $action_name ) ) throw new \Exception( 'action not exist:' . $action_name, -12 );
            $method         =   new \ReflectionMethod( $obj_controller, $action_name );
            $method_params  = $method->getParameters();
            $arr_argv       = array();
            foreach ($method_params as $obj_param_name) {
                $name = $obj_param_name->getName();
                $arr_argv[] =  isset($arr_params[$name]) ? $arr_params[$name] : '';
            }

            define('MODULE_NAME', $module_name );
            define('CONTROLLER_NAME', $controller_name );
            define('ACTION_NAME', $action_name );

            $method->invokeArgs( $obj_controller, $arr_argv );
            return true;
        }
        catch (\Exception $e) {
            $code = $e->getCode();
            if ($code == -11 || $code == -12 ) if ( defined('OTHER_FRAME_INTERFACE') && !empty(OTHER_FRAME_INTERFACE) ) return false;
            \Io::json(array(
                'msg'       => $e->getMessage(),
                'code'      => $e->getCode(),
                'memory'    => memory_get_peak_usage(),
            ));
            die();
        }
    }


    /**
     * 生成连接
     * @param string $url
     * @param $param
     * @return string
     */
    public static function url($url = '', $param) {
        if (empty($url) && !empty($param['s'])) $url = $param['s'];
        $url .= '?';
        foreach ($param as $key => $value ) if ( $key != 's' ) $_params[] = urlencode($key) . '=' . urlencode($value);
        $url .= implode('&', $_params);
        return $url;
    }
}
<?php
/**
 * 视图类
 */

class View {

    protected static $_instance = null;

    protected $_vars = [];

    /**
     * @return \View
     */
    public static function i(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 注入模板变量
     * @param $var_name
     * @param $value
     */
    public function assign( $var_name, $value = null ) {
        if (empty($var_name)) return false;
        if ( is_array( $var_name ) )  return $this->_vars = array_merge( $this->_vars, $var_name );
        $this->_vars[$var_name] = $value;
    }

    /**
     * @param string $tpl
     * @param string $prefix
     */
    public function fetch( $tpl = '', $prefix = '.php', $path_template = '' ) {
        if ( empty($path_template) ) $path_template = PATH_TEMPLATE;
        if ( substr( $path_template, -1, 1 ) != DIRECTORY_SEPARATOR ) $path_template .= DIRECTORY_SEPARATOR;
        extract( $this->_vars );
        if (empty($tpl)) {
            $tpl_file = $path_template . MODULE_NAME .'/' . CONTROLLER_NAME . '/' . ACTION_NAME . $prefix;
        }
        else {
            $tpl_file = $path_template . $tpl . $prefix;
        }
        if (is_file($tpl_file)) return include $tpl_file;
        throw new \Exception('tpl not exist.' . $tpl_file, '100101');
    }

    /**
     * 输出html模板
     * @param string $tpl
     * @param string $prefix
     * @param string $path_template
     * @return mixed
     * @throws Exception
     */
    public function display( $tpl = '', $prefix = '.php', $path_template = '' ) {
        static $headered = false;
        if (!ini_get('short_open_tag')) throw new Exception('view need short tag');
        if (!$headered) {
            header('Content-Type:text/html; charset=utf-8');
            header('X-Powered-By:Lib');
            $headered = true;
        }
        $this->fetch( $tpl, $prefix, $path_template );
    }

    /**
     * 输出模板变量
     */
    public function var_output() {
        var_dump( $this->_vars );
    }

    public function url( $opt, $request_scheme = 'http' ) {
        if ( empty($request_scheme) ) $request_scheme = !empty($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
        $uri = !defined('DOCUMENT_URI') ? ( $request_scheme . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['DOCUMENT_URI'] ) : DOCUMENT_URI;
        return $uri . '?' . http_build_query( $opt );
    }

    /**
     * 格式化
     * @param $number
     * @param string $null_str 如果空或非数字显示的字符, 默认 ---
     * @param integer $decimals 小数字点位数，默认0
     * @return string
     */
    public function number_format( $number, $null_str = '---', $decimals = 0 ) {
        if ( empty($number) || !is_numeric( $number) ) return $null_str;
        return number_format($number, $decimals );
    }
}
<?php
namespace Core;
/**
 * 控制器基类
 * Class Controller
 * @package Core
 */

class Controller {

    /**
     * @var \Util\Params
     */
    protected $_params = null;

    /**
     * 当前模块名
     * @var string
     */
    protected $_module = null;

    /**
     * 当前控制器名
     * @var string
     */
    protected $_controller = null;

    /**
     * 当前方法名
     * @var string
     */
    protected $_action = null;

    public function __construct( $cur_module = null, $cur_controller = null , $cur_action = null ) {
        $this->_params = \Util\Params::get_instance();
        if ( !empty($cur_module)) $this->_module = $cur_module;
        if ( !empty($cur_controller)) $this->_controller = $cur_controller;
        if ( !empty($cur_action)) $this->_action = $cur_action;
    }

    /**
     * @return \Util\Params
     */
    public function params() {
        if (empty($this->_params))  $this->_params = \Util\Params::get_instance();
        return $this->_params;
    }

    protected function _error( $message = '' , $code = 1, $data = []) {
        $this->_return_json($code, $message, $data);
    }

    protected function _success( $data = [], $message = '', $jsonp = '' ) {
        return $this->_return_json(0, $message , $data, $jsonp);
    }

    /**
     * @param int $code
     * @param string $message
     * @param null $data
     */
    protected  function _return_json( $code = 0, $message = '', $data = null, $jsonp = '' ) {
//        ob_clean();
//        header('Content-type: text/json');
        $callback = !empty($jsonp) ? $this->params()->string( $jsonp ) : '';
        if ( !empty($callback) ) echo $callback, '(';
        \Io::json([
            'code'      => $code,
            'message'  => $message,
            'data'      => $data
        ]);
        if ( !empty($callback) ) echo ');';
        die();
    }

    /**
     * 模板变量
     * @param $var_name
     * @param $value
     */
    protected  function _assign($var_name, $value = null) {
        return \View::i()->assign( $var_name, $value );
    }

    /**
     * 显示模板
     * @param string $tpl
     * @param string $prefix
     * @throws \Exception
     */
    protected function _display( $tpl = '' ) {
//        \View::i()->assign('tpl_content', $tpl);
//        \View::i()->display('common/layout');
        $tpl = $this->_tpl( $tpl );
        return \View::i()->display( $tpl );
    }

    /**
     * 渲染模板并取结果
     * @param string $tpl
     * @return string
     * @throws \Exception
     */
    protected function _fetch( $tpl = '' ) {
        @ob_start();
        $tpl = $this->_tpl( $tpl );
        \View::i()->fetch( $tpl );
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }

    protected function _tpl( $tpl = '' ) {
        if (!empty($tpl)) return $tpl;
        if (!empty($this->_module) && !empty($this->_controller) && !empty($this->_action))
            $tpl = "{$this->_module}/{$this->_controller}/{$this->_action}";
        return $tpl;
    }

    /**
     * http 跳转
     * @param $url
     */
    protected function _redirect( $url ) {
//        Header( "HTTP/1.1 301 Moved Permanently" );
        Header( 'Location:' . $url );
    }

    /**
     * 当前uri
     * @return string
     */
    protected function _url_current( $http = 'http' ) {
        return \View::i()->url(['m' => $this->_module, 'c' => $this->_controller, 'a' => $this->_action], $http);
    }
}
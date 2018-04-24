<?php
namespace Core;
/**
 * 命令行下计划任务计划基类
 * Class Cli_controller
 * @package Core
 */
class Cli_controller extends \Core\Controller{

    protected $_time_start = 0;

    public function __construct() {
        parent::__construct();

        if ( !defined('IS_CLI') || !IS_CLI) {
            \Lib\Core::error('cli only');
        }
        set_time_limit(0);
        $this->_time_start = microtime( true );

    }

    /**
     * 输出日志
     * @param string $message
     */
    protected function _log( $message = '' ) {

        if ( defined('PATH_RUNTIME') ) {
            $time = date('Y-m-d H:i:s');
            $path = PATH_RUNTIME . 'crontab_log/' . MODULE_NAME . '.' . CONTROLLER_NAME . '.' . ACTION_NAME . '.log';
            \Io\File::output( $path, "{$time}\t{$message}" . PHP_EOL, true, FILE_APPEND );
        }

    }

    /**
     * 出错中断执行
     * @param string $message
     * @param int $code
     * @param array $data
     */
    protected function _error( $message = '' , $code = -1, $data = [] ) {
        $this->_done( $code, $message, $data );
    }

    /*
     * 成功执行完毕
     */
    protected function _success( $data = [], $message = '', $jsonp = '' ) {
        $this->_done( 0, $message, $data );
    }

    protected function _done( $code, $message = '' , $data = []) {
        $result = [
            'code'          => $code,
            'message'       => $message,
            'memory_used'  => memory_get_peak_usage(),
            'time_start'    => $this->_time_start,
            'time_exe'      => microtime( true ) - $this->_time_start,
            'data'          => $data
        ];
        die( \Io::json( $result ) );
    }
}
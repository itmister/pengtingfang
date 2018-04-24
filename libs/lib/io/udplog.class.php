<?php
namespace Io;

// Log优先级定义
define('LOG_PRI_EMERG', 1);     /* System is unusable */
define('LOG_PRI_ALERT', 2);     /* Immediate action required */
define('LOG_PRI_CRITICAL', 4);     /* Critical conditions */
define('LOG_PRI_ERROR', 8);     /* Error conditions */
define('LOG_PRI_WARNING', 16);     /* Warning conditions */
define('LOG_PRI_NOTICE', 32);     /* Normal but significant */
define('LOG_PRI_INFO', 64);     /* Informational */
define('LOG_PRI_DEBUG', 128);     /* Debug-level messages */
define('LOG_PRI_RPC_DEBUG', 256);     /* Debug-level messages */
define('LOG_PRI_ALL', 0xffffffff);    /* All messages */
define('LOG_PRI_NONE', 0x00000000);    /* No message */

// Log类型定义
define('LOG_RPC_TYPE_CURL', 'curl');
define('LOG_RPC_TYPE_MEMCACHE', 'memcache');
define('LOG_RPC_TYPE_REDIS', 'redis');
define('LOG_RPC_TYPE_DB', 'db');
define('LOG_RPC_TYPE_ORACLE', 'oracle');
define('LOG_RPC_TYPE_MYSQL', 'mysql');
define('LOG_RPC_TYPE_SYSTEM','system');


/**
 * 日志记录类
 * <code>
 *   UdpLog::setMask(LOG_PRI_ALL);
 *   UdpLog::attach(new StdoutLogOb());
 *   // optional: StdoutLogOb(), JsonpLogOb(),
 *   UdpLog::log('i am a log');
 *   UdpLog::debug('i am a debug log');
 * </code>
 */

class UdpLog {
    static private $_listeners = array();
    static private $_priority = LOG_PRI_INFO;
    static private $_mask = LOG_PRI_ALL;

    /**
     * 设置掩码
     * @param int $mask 掩码
     */
    static public function setMask($mask) {
        self::$_mask = $mask;
        return self::$_mask;
    }

    /**
     * 判断$pri是否可用
     * @param  int $pri 优先级
     * @return boolean
     */
    static public function isMasked($pri) {
        return $pri & self::$_mask;
    }

    /**
     * 添加一个观察者
     * @param  object $observer 观察者对象
     * @return boolean
     */
    static public function attach(&$observer) {
        if (!is_a($observer, '\Io\LogObServer\LogObserver')) {
            return false;
        }
        self::$_listeners[$observer->getId()] = &$observer;
        return true;
    }

    /**
     * 去除一个观察者
     * @param  object $observer 观察者对象
     * @return boolean
     */
    static public function detach($observer) {
        if (!is_a($observer, '\Io\LogObServer\LogObserver') ||
            !isset(self::$_listeners[$observer->getId()])) {
            return false;
        }
        unset(self::$_listeners[$observer->getId()]);
        return true;
    }

    /**
     * 格式化消息数组或对象为字符串
     * @param mixed $message 消息，多类型（字符、数组、对象）
     * @todo 多维数组的支持
     */
    static private function _formatToString($message)
    {
        if (is_object($message)) {
            if (method_exists($message, 'getmessage')) {
                $message = $message->getMessage();
            } else if (method_exists($message, 'tostring')) {
                $message = $message->toString();
            } else if (method_exists($message, '__tostring')) {
                if (version_compare(PHP_VERSION, '5.0.0', 'ge')) {
                    $message = (string)$message;
                } else {
                    $message = $message->__toString();
                }
            } else {
                $message = var_export($message, true);
            }
        } else if (is_array($message)) {
            if (isset($message['message'])) {
                if (is_scalar($message['message'])) {
                    $message = $message['message'];
                } else {
                    $message = var_export($message['message'], true);
                }
            } else {
                $message = var_export($message, true);
            }
        } else if (is_bool($message) || $message === NULL) {
            $message = var_export($message, true);
        }
        return $message;
    }

    static public function log($msg, $priority = LOG_PRI_DEBUG, $params=array()) {
        $db = debug_backtrace();
        $c_db = array();
        if( is_array($db) ) {
            if( count($db) == 1 ) {
                $c_db['line'] = $db[0]['line'];
                $c_db['file'] = $db[0]['file'];
                $c_db['function'] = null;
                $c_db['args'] = null;
            }
            else {
                if( $priority != LOG_PRI_RPC_DEBUG ) {
                    $c_db['line'] = $db[1]['line'];
                    $c_db['file'] = $db[1]['file'];
                    $c_db['function'] = $db[1]['function'];
                    $c_db['args'] = $db[1]['args'];
                } else {
                    //@todo 需要针对不同框架需要做适当调整
                    for($i=0; $i<count($db); $i++) {
                       // if( stristr($db[$i]['function'], 'action') && (stristr($db[$i]['class'], 'controller') )) ) { //for yii
                        if (isset($db[$i]['class'])){
                            if( stristr($db[$i]['class'], 'controller') || stristr($db[$i]['class'], 'Action') ) { //for thinkphp
                                $c_db['line'] = ':'.$db[$i]['function'].':'.$db[$i-1]['line'].' @'.session_id();
                                //$c_db['file'] = $db[$i-1]['file'];
                                $c_db['file'] = $db[$i]['class'];
                                $c_db['function'] = $db[$i]['function'];
                                $c_db['args'] = $db[$i]['args'];
                                break;
                            }
                        }
                    }
                }
            }
        }
        $msg = self::_formatToString($msg);
        self::_announce($msg, $c_db, $priority, $params);
    }

    /**
     * 将消息发送给所有附属的观察者
     * @param  mixed $msg 消息，多类型（字符、数组、对象）
     * @param  array $db debug backtrace
     * @param  int $pri 优先级
     * @param  array params 附加参数
     */
    static private function _announce($msg, $db, $pri, $params=array()) {
        foreach (self::$_listeners as $id => $listener) {
            if ( self::isMasked($pri) && self::$_listeners[$id]->isMasked($pri) ) {
                self::$_listeners[$id]->notify($msg, $db, $pri, $params);
            }
        }
    }

    static public function emergency($message) {
        return self::log($message, LOG_PRI_EMERG);
    }

    static public function alert($message) {
        return self::log($message, LOG_PRI_ALERT);
    }

    /**
     * 严重错误，一般需要上报(比如mysql连不上)
     */
    static public function critical($message) {
        return self::log($message, LOG_PRI_CRITICAL);
    }

    /**
     * 一般错误，且无法继续操作
     */
    static public function error($message) {
        return self::log($message, LOG_PRI_ERROR);
    }

    /**
     * 警告错误，但仍可继续操作（比如取exif信息失败）
     */
    static public function warning($message) {
        return self::log($message, LOG_PRI_WARNING);
    }

    static public function notice($message) {
        return self::log($message, LOG_PRI_NOTICE);
    }

    static public function info($message) {
        return self::log($message, LOG_PRI_INFO);
    }

    /**
     * 调试信息，建议在一些关键步骤把一些关键信息打出来，供以后开发调试，上线时关闭
     */
    static public function debug($message) {
        return self::log($message, LOG_PRI_DEBUG);
    }

    static public function getDB($db,$table,$sql) {
        $s = $db . '.' . $table;
        if(preg_match('/select/i',$sql)) {
            $s .= '.select';
        } else if (preg_match('/update/i',$sql)) {
            $s .= '.update';
        } else if (preg_match('/insert/i',$sql)) {
            $s .= '.insert';
        }  else if (preg_match('/connect/i',$sql)) {
            $s .= '.connect';
        }
        return $s;
    }

    /**
     * 记录所有远程调用的消耗时间
     * @param string|array $info 需要记录的信息（比如SQL、read/write、统一XX的API name等）
     * @param string $consumed_time 远程调用消耗的时间
     * @param macro(string) $type  远程调用的类型，见宏
     * @param boolean $direct_write 是否直接写日志，忽略采样率和阈值
     */
    static public function rpc($info, $consumed_time, $type, $direct_write = FALSE) {
        if( is_array( $info ) ) $info = json_encode( $info );
        $consumed_time = sprintf('%.4f', $consumed_time);
        if( $consumed_time < 0.01 )
            $consumed_str = "\033[0;32;40m<$consumed_time>\033[0m";
        elseif( $consumed_time < 0.1 )
            $consumed_str = "\033[0;36;40m<$consumed_time>\033[0m";
        elseif( $consumed_time < 0.5 )
            $consumed_str = "\033[0;33;40m<$consumed_time>\033[0m";
        else
            $consumed_str = "\033[0;31;40m<$consumed_time>\033[0m";
        $message = "\033[0;35;40m".\Io\LogObServer\ThreadId::getThreadId()."\033[0m > \33[4m[$type]\033[0m $consumed_str $info";
        $message = str_replace('Error', "\033[0;31;40mError\033[0m", $message);
        return self::log($message, LOG_PRI_RPC_DEBUG, array('consumed_time'=> $consumed_time, 'direct_write'=>$direct_write));
    }

    /**
     * 数据库日志记录
     * @param  string|array  $info   要记录的信息
     * @param  float $second 耗时
     * @return unknow
     */
    static public function dblog( $info, $second = 0 ){
        return self::rpc( $info, $second,  LOG_RPC_TYPE_DB, true );
    }
    static public function orclelog( $info, $second = 0 ){
        return self::rpc( $info, $second,  LOG_RPC_TYPE_ORACLE, true );
    }
    static public function mysqllog( $info, $second = 0 ){
        return self::rpc( $info, $second,  LOG_RPC_TYPE_MYSQL, true );
    }

    /**
     * udp 程序日志
     * @param $info
     * @param int $second
     */
    static public function uLog($info, $second = 0 ){
        return self::rpc( $info, $second, LOG_RPC_TYPE_SYSTEM, true );
    }


    /**
     * 系统记录
     * @param  string|array  $info   要记录的信息
     * @param  float $second 耗时
     * @return unknow
     */
    static public function syslog( $info, $second = 0 ){
        return self::rpc( $info, $second, LOG_RPC_TYPE_SYSTEM, true );
    }
}
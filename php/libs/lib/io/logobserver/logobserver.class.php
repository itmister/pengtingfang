<?php
namespace Io\LogObServer;
/**
 * 日志观察者抽象类
 */
abstract class LogObserver {
    protected $_id = 0;
    protected $_mask = LOG_PRI_ALL;

    /**
     * 设置掩码
     * @param int $mask 掩码
     */
    public function setMask($mask) {
        $this->_mask = $mask;
        return $this->_mask;
    }

    /**
     * 判断$pri是否可用
     * @param  int $pri 优先级
     * @return boolean
     */
    public function isMasked($pri) {
        return $pri & $this->_mask;
    }

    /**
     * 生成一个随即ID并返回
     * @return string ID
     */
    public function getId(){
        if( !$this->_id )
            $this->_id = md5(uniqid(rand(), true));
        return $this->_id;
    }

    /**
     * 优先级代码转换成文本
     * @param int $priority 优先级代码
     * @return string 文本
     */
    protected function _priorityToString($priority){
        $levels = array(
            LOG_PRI_EMERG   => 'emergency',
            LOG_PRI_ALERT   => 'alert',
            LOG_PRI_CRITICAL    => 'critical',
            LOG_PRI_ERROR     => 'error',
            LOG_PRI_WARNING => 'warning',
            LOG_PRI_NOTICE  => 'notice',
            LOG_PRI_INFO    => 'info',
            LOG_PRI_DEBUG   => 'debug',
            LOG_PRI_RPC_DEBUG => 'rpc'
        );

        return $levels[$priority];
    }

    /**
     * 优先级文本转换成代码
     * @param  string $name 优先级文本
     * @return int 代码
     */
    protected function _stringToPriority($name) {
        $levels = array(
            'emergency' => LOG_PRI_EMERG,
            'alert'     => LOG_PRI_ALERT,
            'critical'  => LOG_PRI_CRITICAL,
            'error'     => LOG_PRI_ERROR,
            'warning'   => LOG_PRI_WARNING,
            'notice'    => LOG_PRI_NOTICE,
            'info'      => LOG_PRI_INFO,
            'debug'     => LOG_PRI_DEBUG,
            'rpc'       => LOG_PRI_RPC_DEBUG
        );

        return $levels[strtolower($name)];
    }

    /**
     * 记录日志
     * @params string $msg 日志字符串
     * @param  array $db debug backtrace
     * @param  int $pri 优先级
     * @param  array params 附加参数
     */
    abstract function notify($msg, $db, $pri, $params);
}
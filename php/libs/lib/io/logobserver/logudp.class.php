<?php
namespace Io\LogObServer;
/**
 * Udp Observer
 * 通过UDP链接将消息发送到指定日志服务器
 */
class LogUdp extends LogObserver {
    private $_ip = '127.0.0.1';
    private $_port = '30001';
    private $_rate = 1;
    private $_threshold = 0.1;

    public function __construct($ip, $port) {
        $this->_ip = $ip;
        $this->_port = $port;
    }

    /**
     * 由于UDP日志量会很大，这里可以设置它的采样频率
     * @param int $rate [0.0001-1]
     * @param float $time_threshold 采样的阈值，即请求时间大于该值的才进行采样
     */
    public function setRate($rate = 1, $time_threshold = 0.1){
        $rate = intval($rate*10000);
        if($rate < 0) $rate = 0;
        if($rate > 10000) $rate = 10000;
        $this->_rate = $rate;

        $this->_threshold = $time_threshold;
    }

    /**
     * 记录日志
     * 会根据采样率和$params['consumed_time']是否超过时间阈值判断是否记录日志
     * @params string $msg 日志字符串
     * @param  array $db debug backtrace
     * @param  int $pri 优先级
     * @param  array params 附加参数
     */
    public function notify($msg, $db, $pri, $params) {
        $tm = floatval($params['consumed_time']);
        // 无错误 && 不满足采样率 && 时间未达到阈值
        if(empty($params['direct_write']) && !strstr($msg, 'Error') && mt_rand(0, 9999) > $this->_rate && $tm < $this->_threshold )
            return;

        $pri_str = $this->_priorityToString($pri);
        if ($db['file'] && $db['line'] ) {
            //$content = "[$pri_str]$msg ({$db['file']}:{$db['line']})";
            $content = "$msg ({$db['file']}:{$db['line']})";
        } else {
            //$content = "[$pri_str]$msg ";
            $content = "$msg ";
        }

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            return false;
        }
        socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => 0, "usec" => 100000));
        $result = socket_connect($socket, $this->_ip, $this->_port);
        if ($result && is_resource($socket)) {
            socket_write($socket, $content, strlen($content));
        }
        socket_close($socket);
    }
}
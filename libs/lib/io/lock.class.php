<?php
namespace io;

class Lock{
    private static $_instance = null;
    const REDIS_KEY = "user-process-lock:uid:";

    public static function get_instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * 用户进程锁（不允许一个用户并发执行抽奖逻辑）
     * 如果当前锁不存在或者已经失效 返回true 存在返回false
     * @param $uid    用户id
     * @param $timeout 防止php-fpm 进程异常退出没有释放锁，加了锁的有效时间
     * @return bool|int  已经获得锁为 true  没有获得锁为false
     */
    public function get_user_process_lock($uid,$timeout = 10){
        $redis = $this->_redis();
        $ret = $redis->setnx(self::REDIS_KEY.$uid,time()+$timeout);
        if (!$ret) {
            $time = $redis->get(self::REDIS_KEY.$uid);
            if (time() < $time){
                return false;
            }else{
                if ($this->release_user_process_lock($uid)){
                    $ret = $redis->setnx(self::REDIS_KEY.$uid,time()+$timeout); //重新设置
                }
            }
        }
        return $ret;
    }

    /**
     * 删除锁
     * @param $uid
     * @return int
     */
    public  function release_user_process_lock($uid){
        $redis = $this->_redis();
        return  $redis->del(self::REDIS_KEY.$uid);
    }

    /**
     * @return Redis
     */
    private function _redis(){
        static $object;
        if ($object) return $object;
        $object = new \Redis();
        $redis_config = \Lib\Core::config("redis");
        $object->connect($redis_config['host'],$redis_config['port']);
        return $object;
    }
}
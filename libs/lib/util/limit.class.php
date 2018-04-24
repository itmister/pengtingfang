<?php
/**
 * @desc 文件
 */
namespace Util;

use \Io\Redis;
class Limit {

    /**
     * @param $redis
     * @param $ip
     * @param $limit
     * @param $time_span
     * @param string $pre_key
     * @return bool
     */
    public static function ip_access_frequency_limit($redis,$ip,$limit,$time_span,$pre_key= 'client_ip_'){
        if($ip){
            $key =  $pre_key.$ip;
            $now = time();
            //获取这个ip的长度
            $length =   $redis->lLen($key);
            if ( $length < $limit ){ //小于限制
                $redis ->lPush($key,$now);
            }else{ // 大于等于限制
                //拿到最早的记录
                $time = $redis->lIndex($key,-1);
                if ($now - $time < $time_span){//超过限制了
                    return false;
                }else{//剔除最早记录
                    $redis->lPush($key,$now);//增加最新记录
                    $redis->lTrim($key,0,$limit -1);//保留最新MAX_LEN 条记录
                }
            }
            return true;
        }
        return false;
    }

}
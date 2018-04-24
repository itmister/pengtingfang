<?php
/**
 * 软件安装量
 */
namespace Dao\Union;
use \Dao;
class Rt_7654_log_install extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Credit_wait_confirm
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    /*
     * 某一天的某个技术员某个软件的安装量
     */
    public function get_user_install_count($uid,$ymd,$softID){
        //M('rt_7654_log_install')->where("uid={$uid} and dateline={$item['ymd']} and softID='{$item['name']}'")->sum('onlyInstall');
        $sql = "select sum(onlyInstall) as onlyInstall
                from {$this->_realTableName}
                where uid={$uid} and dateline={$ymd} and softID='{$softID}'";
        return $this->query($sql);
    }
    /*
     * 某一段时间内的某个技术员某个软件的安装量
     */
    public function get_user_install_count_all($uid,$start,$end,$softID){
        $sql = "select sum(onlyInstall) as onlyInstall
                from {$this->_realTableName}
                where uid={$uid} and dateline>={$start} and dateline<={$end} and softID='{$softID}'";
        return $this->query($sql);
    }
}

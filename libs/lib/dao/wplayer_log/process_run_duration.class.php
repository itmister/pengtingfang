<?php
namespace Dao\Wplayer_log;
use \Dao;
class Process_run_duration extends Wplayer_log {
    /**
     * @return Process_run_duration
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_average_time($ymd){
        $sql = "SELECT sum(duration_time)/count(DISTINCT uid) as active_avgtime FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return $data;
    }


}

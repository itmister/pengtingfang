<?php
namespace Dao\Huayangnianhua_log;
use \Dao;
class Process_run_duration extends Huayangnianhua_log {
    /**
     * @return Dao\Huayangnianhua_log\Process_run_duration
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

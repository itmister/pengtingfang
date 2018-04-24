<?php
namespace Dao\Huayangnianhua_log;
use \Dao;
class Notepaper_run extends Huayangnianhua_log {
    /**
     * @return Dao\Huayangnianhua_log\Process_run_duration
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    /**
     * @return array
     */
    public function get_average_time($ymd){
        $sql = "SELECT sum(duration_time)/count(DISTINCT UID) as notepaper_avgtime FROM `{$this->_realTableName}{$ymd}`";
        $data = current($this->query($sql));
        return $data;
    }


}

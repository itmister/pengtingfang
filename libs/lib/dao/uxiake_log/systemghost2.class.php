<?php
namespace Dao\Uxiake_log;
use \Dao;
class Systemghost2 extends Uxiake_log {

    /**
     * @return Dao\Uxiake_log\Systemghost2
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    public function get_system_data($ymd){
        $sql = "SELECT COUNT(`UUID`) AS num FROM `{$this->_realTableName}{$ymd}`";
        $query = current($this->query($sql));
        return (int)$query['num'];
    }
}

<?php
namespace Dao\Qqpcmgr_log;
use \Dao;
class Jingpin extends Qqpcmgr_log {

    /**
     * @return Dao\Qqpcmgr_log\Jingpin
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_all_jingpin_security($ymd,$softId){
        $sql = "SELECT count(DISTINCT UID) as InstallCount FROM `{$this->_realTableName}{$ymd}` WHERE is_install=1 and LOWER(software) LIKE '%{$softId}%';";
        return $this->query($sql);
    }

    public function get_all_jingpin_competition($ymd,$softId){
        $sql = "SELECT count(DISTINCT UID) as InstallCount FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(software) LIKE '%{$softId}%';";
        return $this->query($sql);
    }

}

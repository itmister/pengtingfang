<?php
namespace Dao\Wallpaper_log;
use \Dao;
class Jingpin extends Wallpaper_log {

    protected static $_instance = null;
    /**
     * @return Dao\Wallpaper_log\Jingpin
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_all_jingpin_security($ymd,$softId){
        $sql = "SELECT count(DISTINCT UID) as InstallCount FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(software) LIKE '%{$softId}%';";
        return $this->query($sql);
    }

    public function get_all_jingpin_competition($ymd,$softId){
        $sql = "SELECT count(DISTINCT UID) as InstallCount FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(software) LIKE '%{$softId}%';";
        return $this->query($sql);
    }

}

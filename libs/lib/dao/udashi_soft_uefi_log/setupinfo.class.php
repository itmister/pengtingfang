<?php
namespace Dao\Udashi_soft_uefi_log;
use \Dao;
class Setupinfo extends Udashi_soft_uefi_log {

    protected static $_instance = null;
    /**
     * @return Setupinfo
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function get_install_online($ymd,$type,$as){
        $sql = "SELECT {$ymd} AS ymd,Version as ver,COUNT(distinct UID) AS {$as} FROM `{$this->_realTableName}{$ymd}` where flag='{$type}' GROUP BY Version";
        return $this->query($sql);
    }
}

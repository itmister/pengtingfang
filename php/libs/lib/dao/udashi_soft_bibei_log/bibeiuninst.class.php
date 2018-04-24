<?php
namespace Dao\Udashi_soft_bibei_log;
use \Dao;
class Bibeiuninst extends Udashi_soft_bibei_log {

    protected static $_instance = null;
    /**
     * @return Bibeiuninst
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_bibei_ver_count($ymd){
        $sql = "SELECT version as ver,{$ymd} as ymd,count(distinct UID) as `uninstall`,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` group by version";
        return $this->query($sql);
    }
}

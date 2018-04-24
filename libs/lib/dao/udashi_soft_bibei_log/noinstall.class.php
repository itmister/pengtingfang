<?php
namespace Dao\Udashi_soft_bibei_log;
use \Dao;
class Noinstall extends Udashi_soft_bibei_log {

    protected static $_instance = null;
    /**
     * @return Noinstall
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_bibei_ver_count($ymd){
        $sql = "SELECT version as ver,{$ymd} as ymd,count(distinct UID) as `close`,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` group by version";
        return $this->query($sql);
    }
}

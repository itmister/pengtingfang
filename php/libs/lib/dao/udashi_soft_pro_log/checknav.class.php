<?php
namespace Dao\Udashi_soft_pro_log;
use \Dao;
class Checknav extends Udashi_soft_pro_log {

    protected static $_instance = null;
    /**
     * @return Checknav
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function select_noselect($ymd){
        $sql = " SELECT {$ymd} as ymd,Version as ver,count(distinct UID) as checknav from `{$this->_realTableName}{$ymd}` where flag='0' group by Version";
        return $this->query($sql);
    }

}

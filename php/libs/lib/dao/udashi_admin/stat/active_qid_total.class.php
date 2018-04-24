<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Active_qid_total extends \Dao\Udashi_admin\Udashi_admin {

    protected static $_instance = null;
    /**
     * @return Active_qid_total
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_active_total($ymd){
        $sql = "SELECT QID as qid,{$ymd} as ymd,count(*) as `active_total` FROM `{$this->_realTableName}` group by QID";
        return $this->query($sql);
    }

    public function get_active($ymd){
        $sql = "SELECT QID as qid,{$ymd} as ymd,count(*) as `active` FROM `{$this->_realTableName}` where Ymd={$ymd} group by QID";
        return $this->query($sql);
    }
}

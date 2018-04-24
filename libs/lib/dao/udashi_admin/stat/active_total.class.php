<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Active_total extends \Dao\Udashi_admin\Udashi_admin {

    protected static $_instance = null;
    /**
     * @return Active_total
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_active_total($ymd){
        $sql = "SELECT count(*) as `active_total` FROM `{$this->_realTableName}`";
        return $this->query($sql);
    }
}

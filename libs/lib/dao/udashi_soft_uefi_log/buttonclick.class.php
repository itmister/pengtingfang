<?php
namespace Dao\Udashi_soft_uefi_log;
use \Dao;
class Buttonclick extends Udashi_soft_uefi_log {

    protected static $_instance = null;
    /**
     * @return Buttonclick
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function select_all($ymd,$name,$click_name){
        $sql = " SELECT count(distinct UID) as {$click_name} from `{$this->_realTableName}{$ymd}` where clt like '%".$name."%' ";
        return $this->query($sql);
    }

    public function select_all_num($ymd,$name,$click_name){
        $sql = "SELECT {$ymd} as ymd,Version as ver,count(distinct UID) as {$click_name} from `{$this->_realTableName}{$ymd}` where clt like '%".$name."%' group by Version";
        return $this->query($sql);
    }
}

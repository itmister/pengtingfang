<?php
namespace Dao\Qqpcmgr\Stat;
use \Dao;
class Other_nav_data extends \Dao\Qqpcmgr\Qqpcmgr {

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Other_nav_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function get_count_ymd($where){
        $sql = "select count(*) as num from `{$this->_realTableName}` where {$where}";
        return $this->query($sql);
    }

    public function select_data($where,$limit){
        $sql = "select * from `{$this->_realTableName}` where {$where} limit {$limit}";
        return $this->query($sql);
    }
}

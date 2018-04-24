<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Other_nav_data extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Other_nav_data
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

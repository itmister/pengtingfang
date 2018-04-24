<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Soft extends \Dao\Udashi_admin\Udashi_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Udashi_admin\Stat\Soft
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 获取信息
     * @return array
     */
    public function get_all($where=true,$field='*'){
        $sql = "SELECT {$field} FROM `{$this->_realTableName}` WHERE {$where}";
        return $this->query($sql);
    }
    /**
     * 获取总数
     * @param string $where
     * @return array
     */
    public function get_conlyount($where){
        $sql = "SELECT count(*) as count FROM `{$this->_realTableName}` WHERE {$where}";
        $result = $this->query($sql);
        return $result[0]['count'];
    }
    
    
    
    
    
}

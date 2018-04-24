<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao;
class Url extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin{

    protected static $_instance = null;
    
    /**
     * @return Dao\Huayangnianhua_admin\Stat\Url
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
    public function get_count($where){
        $sql = "SELECT count(*) as count FROM `{$this->_realTableName}` WHERE {$where}";
        $result = $this->query($sql);
        return $result[0]['count'];
    }
    

}

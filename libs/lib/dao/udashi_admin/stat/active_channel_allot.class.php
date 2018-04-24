<?php
namespace Dao\Udashi_admin\Stat;
use \Dao\Udashi_admin;
class Active_channel_allot extends \Dao\Udashi_admin\Udashi_admin {

    /**
     * @return Channel_data
     */
    public static function get_instance(){
        return parent::get_instance();
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

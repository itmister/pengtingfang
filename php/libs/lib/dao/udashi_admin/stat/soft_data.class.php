<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Soft_data extends \Dao\Udashi_admin\Udashi_admin {

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
    
    public function list_sum($where){
        $sql = "SELECT sum(apprun)apprun ,sum(apprun_zx)apprun_zx ,sum(apprun_lx)apprun_lx , sum(install)install ,sum(install_zx)install_zx ,sum(install_lx)install_lx , sum(download_error)download_error ,sum(download_error_zx)download_error_zx ,sum(download_error_lx)download_error_lx , sum(install_error_zx)install_error_zx ,sum(install_error_lx)install_error_lx,sum(install_error)install_error  from `{$this->_realTableName}` where {$where}";
        $result = $this->query($sql);
        return $result;
    }
    
    
}

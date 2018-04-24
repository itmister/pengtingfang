<?php
namespace Dao\Daohang_admin;
use \Dao;
class Cnzz extends Daohang_admin {

    protected static $_instance = null;


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
    
    /**
     * 查询data 汇总和cnzz汇总
     */
    public function get_data_cnzz_count($select_params){
        $sql = "select count(*) count from  (SELECT DATE_FORMAT(dt,'%Y%m%d') dt,SUM(rec_ip) rec_ip,SUM(rec_pv) rec_pv,SUM(rec_uv) rec_uv from `data_total` where 1=1  {$select_params['where']} GROUP BY `dt` 
) a LEFT JOIN cnzz b on a.dt = b.dt ORDER BY a.dt desc
    ";
        $result = $this->query($sql);
        return $result[0]['count'];
        
    }
    
    /**
     * 查询data 汇总和cnzz汇总
     */
    public function get_data_cnzz($select_params){
       
        $sql = "select a.*, b.dt dt2, b.cnzzPV,b.cnzzUV,b.cnzzIP,b.cnzzDL from  (SELECT DATE_FORMAT(dt,'%Y%m%d') dt,SUM(click_uv) click_uv,SUM(rec_ip) rec_ip,SUM(rec_pv) rec_pv,SUM(rec_uv) rec_uv from `data_total` where 1=1  {$select_params['where']} GROUP BY `dt`
) a LEFT JOIN cnzz b on a.dt = b.dt ORDER BY a.dt desc limit {$select_params['limit']};
    ";
       
        return $this->query($sql);
    }
}

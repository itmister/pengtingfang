<?php
namespace Dao\Kpzip_admin;
use \Dao;
class Gs_cost_benefit extends Kpzip_admin{

    protected static $_instance = null;
    /**
     * @return Gs_cost_benefit
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function lists($ymd = '',$limit = ''){
        $sql = "SELECT c.*,p.ymd,p.install FROM stat_product_data AS p LEFT JOIN gs_cost_benefit AS c ON p.ymd = c.ymd";
        if($ymd){
            $sql .= " WHERE p.ymd = {$ymd}";
        }
        $sql .=" ORDER BY p.dateline DESC";
        if($limit){
            $sql .= " LIMIT {$limit}";
        }
        $query_res = $this->query($sql);
        return $query_res ? $query_res : [];
    }
}

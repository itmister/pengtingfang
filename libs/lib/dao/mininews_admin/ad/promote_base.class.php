<?php
namespace Dao\Mininews_admin\Ad;
use \Dao\Mininews_admin\Mininews_admin;

class Promote_base extends Mininews_admin{

    protected static $_instance = null;
    /**
     * @return Promote_base
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function select_join($where = '',$limit = ''){
        $sql = "
            SELECT
                b.*,p.pid,p.type,b.type basetype
            FROM {$this->_get_table_name()} AS b
            LEFT JOIN {$this->_get_table_name('ad_promote')} AS p ON b.id = p.pid
        ";
        if($where){
            $sql .= " WHERE {$where}";
        }
        $sql .= " ORDER BY b.created DESC";
        if($limit){
            $sql .= " LIMIT {$limit}";
        }
        return $this->query($sql);
    }
}

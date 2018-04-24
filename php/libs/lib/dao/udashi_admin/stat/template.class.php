<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Template extends \Dao\Udashi_admin\Udashi_admin {

    protected static $_instance = null;
    /**
     * @return Template
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
            w.*,t.name,t.directory
            FROM {$this->_get_table_name('stat_website')} AS w 
            INNER JOIN {$this->_get_table_name()} AS t 
            ON w.tid = t.id
        ";
        if($where){
            $sql .= " WHERE {$where}";
        }
        $sql .= " ORDER BY w.update_time DESC";
        if($limit){
            $sql .= " LIMIT {$limit}";
        }
        return $this->query($sql);
    }
}

<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Template_log extends \Dao\Udashi_admin\Udashi_admin {

    protected static $_instance = null;
    /**
     * @return Template_log
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
            w.*,t.`name`,t.`directory`,l.created
            FROM {$this->_get_table_name()} AS l
            INNER JOIN {$this->_get_table_name('stat_template')} AS t ON t.id = l.tid
            INNER JOIN {$this->_get_table_name('stat_website')} AS w ON w.id = l.wid
        ";
        if($where){
            $sql .= " WHERE {$where}";
        }
        $sql .= " ORDER BY l.created DESC";
        if($limit){
            $sql .= " LIMIT {$limit}";
        }
        return $this->query($sql);
   }
}

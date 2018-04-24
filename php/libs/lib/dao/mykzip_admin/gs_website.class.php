<?php
namespace Dao\Mykzip_admin;
use \Dao;
class Gs_website extends Mykzip_admin {
    protected static $_instance = null;

    /**
     * @return Gs_website
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    /**
     * 获取
     * @param number $cid
     * @return \Dao\mixed
     */
    public function generate_html_data($cid = 0){
        /* $sql = "
            SELECT w.*,c.name,c.template_name FROM {$this->_get_table_name()} AS w 
            INNER JOIN {$this->_get_table_name('gs_website_category')} AS c ON w.cid = c.id 
            WHERE 6 >= (SELECT COUNT(*) FROM {$this->_get_table_name()} w1 WHERE w.cid = w1.cid AND w.id <= w1.id) 
            AND c.has_delete = 0 AND w.has_delete = 0
        ";
        if($cid){
            $sql .= " AND w.cid = ".$cid;
        }
        $sql .= " ORDER BY w.cid DESC,w.has_recommend DESC,w.sort ASC"; */
        $categorys_sql = "SELECT * FROM {$this->_get_table_name('gs_website_category')} WHERE has_delete = 0";
        if($cid){
            $categorys_sql .= " AND id = ".$cid;
        }
        $categorys = $this->query($categorys_sql);
        if($categorys){
            foreach ($categorys as $cid){
                $sql = "
                    SELECT w.*,c.name,c.template_name FROM {$this->_get_table_name()} AS w
                    INNER JOIN {$this->_get_table_name('gs_website_category')} AS c ON w.cid = c.id
                    AND c.id = {$cid['id']} AND w.has_delete = 0 ORDER BY w.cid DESC,w.has_recommend DESC,w.sort ASC LIMIT 6
                ";
                $query_result[$cid['template_name']] = $this->query($sql);
            }
        }
        return $query_result;
    }
}

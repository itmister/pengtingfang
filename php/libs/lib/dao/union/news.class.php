<?php
/**
 * 新闻表
 */
namespace Dao\Union;
use \Dao;

class News extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\News
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 新闻列表
     * @param string $field
     * @param string $where
     * @param integer $page_start
     * @param integer $page_end
     * @param string $orderby
     */
    public function lists($where,$field,$page_start,$page_end,$orderby){
        $query_sql    = "SELECT {$field} FROM {$this->_get_table_name()} WHERE {$where} ORDER BY {$orderby} LIMIT {$page_start},{$page_end}";
        $query_result = $this->query($query_sql);
        return $query_result;
    }
}

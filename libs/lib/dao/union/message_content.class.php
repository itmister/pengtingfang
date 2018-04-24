<?php
namespace Dao\Union;
use \Dao;

class Message_content extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Message_content
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 通过用户id获取问题
     * @param integer $c_id
     * @param integer $c_type
     * @return mixed
     */
    public function fetch_content_by_id($c_id,$c_type) {
        $query_sql    = "SELECT * FROM `{$this->_get_table_name()}` WHERE `c_id` = {$c_id} AND `c_type` = {$c_type}";
        $query_result = $this->query( $query_sql );
        return current( $query_result );
    }
    
}

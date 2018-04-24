<?php
namespace Dao\Union;
use \Dao;

class Message_question extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\message_question
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 通过用户id获取问题总记录数
     * @param integer $user_id 用户id
     */
    public function fetch_count_by_uid($user_id) {
        $user_id      = intval( $user_id );
        $query_sql    = "SELECT COUNT(*) AS tp_count FROM `{$this->_get_table_name()}` WHERE `q_uid` = {$user_id}";
        $query_result = $this->query( $query_sql );
        return current($query_result);
    }
    
    /**
     * 获取问题列表
     * @param string  $where         查询条件
     * @param string  $field         查询字段
     * @param number  $page_start    起始记录数
     * @param number  $page_end      结束记录数
     * @param string  $order         排序字段
     * @param string  $sort          排序
     * @return \Dao\mixed
     */
    public function fetch_message_list($where,$field,$page_start,$page_end,$order){
        $query_sql    = "SELECT {$field} FROM `{$this->_get_table_name()}` WHERE {$where} ORDER BY {$order}";
        if($page_start >= 0 && $page_end > 0){
            $query_sql.= " LIMIT {$page_start},{$page_end}";
        }
        $query_result = $this->query( $query_sql );
        return $query_result;
    }
    
    /**
     * 通过id获取问题
     * @param integer $id
     * @param string $field
     * @return \Dao\mixed
     */
    public function fetch_message_by_id($q_id,$field = '*'){
        $q_id         = intval($q_id);
        $query_sql    = "SELECT {$field} FROM `{$this->_get_table_name()}` WHERE `q_id` = {$q_id}";
        $query_result = $this->query( $query_sql );
        return current($query_result);
    }
    
}

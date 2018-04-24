<?php
/**
 * 站内信
 */
namespace Dao\Union;
use \Dao;

class Msg_data extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Msg_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 新增站内信
     * (non-PHPdoc)
     * @see \Dao\Dao::add()
     */
    public function add($batch_id,$user_id,$from_user_id) {
        if(!$batch_id || !$user_id || !$from_user_id){
            return false;
        }
        $time = time();
        $data = array(
            'batch_id'      => $batch_id,
            'user_id'       => $user_id,
            'from_user_id'  => $from_user_id,
            'inputtime'     => $time
        );
        return parent::add($data);
    }
    
   /**
    * 获取站内信信息
    * @param unknown $msg_data_id
    * @param string $filed
    * @return boolean|array
    */
    public function find($msg_data_id,$filed = "*"){
        if(!$msg_data_id){
            return false;
        }
        $where = "msg_data_id = {$msg_data_id}";
        $sql = "SELECT {$filed} FROM {$this->_get_table_name()} WHERE {$where}";
        
        $query_result = $this->query($sql);
        
        return $query_result ? current($query_result) : array();
    }
}

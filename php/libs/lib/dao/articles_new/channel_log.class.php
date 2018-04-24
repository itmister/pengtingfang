<?php
namespace Dao\Articles_new;
class Channel_log extends Articles_new {

    protected static $_instance = null;

    /**
     * @return Channel_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function count_channel_id($where){
        if(!$where){
            return false;
        }
        $sql ="SELECT channel_id,COUNT(*) AS download_num FROM {$this->_get_table_name()} WHERE {$where} GROUP BY channel_id";
        $result = $this->query($sql);
        return $result;
    }
}

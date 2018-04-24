<?php
/**
 * 单价记录表
 * @author caolei
 * 
 */
namespace Dao\Online_7654;

class Soft_price extends Online_7654 {

    protected static $_instance = null;

    /**
     * @return Soft_num
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_list($soft_id_arr,$channel_id_arr){
        $soft_id_str = implode(',',$soft_id_arr);
        $channel_id_str = implode(',',$channel_id_arr);
        if(!$soft_id_str) return false;
        $table_promotion = $this->_realTableName;
        $sql = "SELECT channel_id,price,soft_id FROM {$table_promotion}
        WHERE channel_id in ({$channel_id_str}) and soft_id in ({$soft_id_str})";
        $result = $this->query( $sql );
        return $result;
    }

    public function get_soft_id_price($channel_id,$soft_id){
        $table_promotion = $this->_realTableName;
        $sql = "SELECT price FROM {$table_promotion}
        WHERE channel_id={$channel_id} and soft_id='{$soft_id}'";
        $result = $this->query($sql);
        if($result){
            return $result[0];
        }
        return $result;
    }
}
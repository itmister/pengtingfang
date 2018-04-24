<?php
/**
 * @desc 厂商返回量数据表;
 * @author william
 */
namespace Dao\Online_7654;
use \Dao;
class Ad_product_record_fafang_log extends Online_7654 {
    
    
    protected static $_instance = null;

    /**
     * @return Dao\Online_7654\ad_product_record_fafang_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_info($soft_id,$ymd){
        $sql		= "select * from {$this->_realTableName} where ymd={$ymd} and soft_id='{$soft_id}'";
        $list		=  $this->query($sql);
        return $list[0];
    }

    public function get_list($soft_id,$ymd){
        $sql		= "select *,(select channel_id from user where id=uid) as channel_id from {$this->_realTableName} where ymd={$ymd} and soft_id='{$soft_id}' and stat=0 and zuobi=1";
        $list		=  $this->query($sql);
        return $list;
    }
}
?>

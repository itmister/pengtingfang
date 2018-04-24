<?php
/**
 * @desc 厂商返回量数据表;
 */
namespace Dao\Clt_7654;
use \Dao;
class Product_record_fafang_log extends Clt_7654 {
    
    
    protected static $_instance = null;

    /**
     * @return Product_record_fafang_log
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
        $sql		= "select * from {$this->_realTableName} where ymd={$ymd} and soft_id='{$soft_id}' and state=0 and zuobi=1";
        $list		=  $this->query($sql);
        return $list;
    }
}
?>

<?php
namespace Dao\Union;
use \Dao;
class Ad_product_record_fafang extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Ad_product_record_fafang
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 获取软件推广增量日志
     */
    public function get_soft_add_recode_daily($s_time,$e_time){
        $sql = "select  promotion_name,ymd,original_num from {$this->_realTableName} where add_time >=$s_time and add_time < $e_time";
        return $this->query($sql);
    }
}
?>

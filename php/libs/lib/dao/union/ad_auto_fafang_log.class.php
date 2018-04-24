<?php
/**
 * @desc 数据抓取日志记录表;
 * @author caolei
 */
namespace Dao\Union;
use \Dao;
class Ad_auto_fafang_log extends Union {
    
    
    protected static $_instance = null;

    /**
     * @return Dao\Union\ad_auto_fafang_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
   /**
    * 
    * @param varchar $software_name  软件soft_id
    * @param int $ymd 抓取数据时间 推广日期
    * @return
    */
    public function get_software_ymd_type_by_value($software_name,$ymd) {
        $sql = "SELECT id FROM {$this->_get_table_name()}
          where name='{$software_name}' AND ymd={$ymd} AND type=3";
        $arr_data = $this->query($sql);
        return $arr_data;
    }
}
?>

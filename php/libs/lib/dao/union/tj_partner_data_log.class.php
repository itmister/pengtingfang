<?php
/**
 * @desc 合作联盟数据记录明细表;
 * @author caolei
 */
namespace Dao\Union;
use \Dao;
class Tj_partner_data_log extends Union {
    
    
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
    * @param int $partner 合作联盟
    * @return array()
    */
    public function get_software_value_partner_by_ymd($software_name,$ymd,$partner) {
        $sql = "SELECT qid,ip_count,sy_count FROM {$this->_get_table_name()}
          where software_short_name='{$software_name}' AND ymd={$ymd} AND partner=$partner";
        $arr_data = $this->query($sql);
        if(empty($arr_data)) return false;
        return $arr_data;
    }
}
?>

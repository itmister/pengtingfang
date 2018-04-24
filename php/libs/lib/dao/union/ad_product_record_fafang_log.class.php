<?php
/**
 * @desc 厂商返回量数据表;
 * @author william
 */
namespace Dao\Union;
use \Dao;
class Ad_product_record_fafang_log extends Union {
    
    
    protected static $_instance = null;

    /**
     * @return Dao\Union\ad_product_record_fafang_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
   /**
    * 
    * @param int $soft_promotion_id  软件promotion_id
    * @param int $ymd_start 开始时间
    * @param int $ymd_end   结束时间
    * @param array $arr_uid_list 选中的用户id
    * @return array(0=>array(ymd,software_value_total),)
    */
    public function get_software_value_total_by_ymd( int $soft_promotion_id, int $ymd_start, int $ymd_end, array $arr_uid_list) {
        $soft_promotion_id = intval($soft_promotion_id);
        $ymd_start = intval($ymd_start);
        $ymd_end = intval($ymd_end);
        if ( $ymd_start < 20000000) $ymd_start += 20000000;
        if ( $ymd_end < 20000000) $ymd_end += 20000000;
        $str_uid_list = implode(',', $arr_uid_list);
        $sql = "SELECT f_ymd as ymd,SUM(f_num_original) as software_value_total FROM {$this->_get_table_name()}
          where f_ymd>={$ymd_start} AND f_ymd<={$ymd_end} f_promotion_id=11 AND f_uid in ({$str_uid_list})";
        $arr_data =  $this->query( $sql );
        return $arr_data ? $arr_data : array();
    }
    
    /**
     * @desc
     * @param type $soft_promotion_id
     * @param type $ymd
     * @param type $uid
     * @return int
     */
    public function get_org_total_by_promotion_id_ymd_uid ($soft_promotion_id,$ymd,$uid){
        $soft_promotion_id = intval($soft_promotion_id);
        $ymd = intval($ymd);
        $uid = intval($uid);
        $sql = "SELECT SUM(a.f_num_original) as org_total FROM ad_product_record_fafang_log a , channel_7654.user_marketer b , `user` c 
WHERE b.is_stat_manager > 0 AND c.invitecode = b.idcode AND a.f_uid = c.id AND a.f_promotion_id = {$soft_promotion_id} AND a.f_ymd = {$ymd} AND b.userid={$uid} ";
#echo $sql;
        $arr_data =  $this->query( $sql );
        return $arr_data ? $arr_data[0]['org_total'] : 0;
    }
    
}
?>

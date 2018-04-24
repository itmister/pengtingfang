<?php
/**
 * @desc 厂商返回量数据表;
 */
namespace Dao\Clt_7654;
use \Dao;
class Product_record_fafang extends Clt_7654 {
    
    
    protected static $_instance = null;

    /**
     * @return Product_record_fafang
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_list_for_date($param){
        $where		= '';
        $start      = $param['startDate'];
        $end        = $param['endDate'];
        $id	        = $param['PromotionId'];
        $selectSure = $param['selectSure'];
        if(in_array($param['selectSure'],array(1,2))){
            $where .= ' and status_sure='.$selectSure;
        }
        $sql		= "select ymd id,original_unum,original_num,status_upload,status_sure,status_fafang,actual_unum,actual_num,type,ymd,add_time,promotion_id,soft_id from {$this->_realTableName} where ymd>={$start} and ymd<={$end} and promotion_id={$id}";
        $sql		= $sql.$where;
        $list		=  $this->query($sql);
        return $list;
    }

    public function get_info($soft_id,$ymd){
        $sql		= "select * from {$this->_realTableName} where ymd={$ymd} and soft_id='{$soft_id}'";
        $list		=  $this->query($sql);
        return $list[0];
    }
}
?>

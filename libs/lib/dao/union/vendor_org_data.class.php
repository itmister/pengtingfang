<?php
namespace Dao\Union;
use \Dao;
class Vendor_Org_Data extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Vendor_Org_Data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_data($promotion,$ymd) {
        $sql = "select * from {$this->_realTableName} where promotion='{$promotion}' and ymd={$ymd}";
        return $this->query($sql);
    }

    public function add_data($promotion,$ymd,$data){
        $this->delete("promotion='{$promotion}' and ymd={$ymd}");
        foreach($data as &$val){
            $val['promotion'] = $promotion;
            $val['ymd'] = $ymd;
            $val['ctime'] = date("Y-m-d H:i:s");
        }
        return $this->add_all($data);
    }

    /*取winHome的tn某天的厂商返回量*/
    public function get_tn_num_winhome($soft_id,$tnList,$ymd){
        if(empty($tnList)) return false;
        $org_id_str = implode("','",$tnList);
        $org_id_str = "'".$org_id_str."'";
        $sql = "select org_id as tn,count as num from {$this->_realTableName} where promotion='{$soft_id}' and ymd={$ymd} and org_id in ({$org_id_str})";
        return $this->query($sql);
    }
}

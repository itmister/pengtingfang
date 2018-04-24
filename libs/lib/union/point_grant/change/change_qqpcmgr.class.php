<?php
namespace Union\Point_grant\Change;
/**
 * Created by JetBrains PhpStorm.
 * User: caolei
 * Date: 16-4-7
 * Time: 上午10:28
 * To change this template use File | Settings | File Templates.
 * 原始渠道号转换uid
 */
class Change_qqpcmgr extends Change{

    protected static $_instance = null;
    private static $_changeSoftId = "";//取渠道号的softId
    private static $_getDataSoftId = "";//取数据的softId

    /**
     * @return \Union\Point_grant\Change\Change_qqpcmgr
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    //取原始厂商返回的数据  渠道号=》有效量
    public function get_data_to_vendor_org($softId,$ymd){
        if(self::$_getDataSoftId){
            $softId = self::$_getDataSoftId;
        }
        $data = \Dao\Union\Vendor_Org_Data::get_instance()->select(
            array(
                "where"=>"promotion='{$softId}' and ymd={$ymd}",
                "field"=>"org_id,count,active_count"
            )
        );
        return $data;
    }
    //处理原始厂商返回的数据  渠道号 把渠道号 转换成我们存的渠道号
    public function org_id_change_my_org_id($array){
        $arr = array();
        $arr_original = array();
        $original_sum = 0;
        foreach($array as $_v){
            $arr_original[$_v['org_id']] = $_v['count'];
            if($_v['count']<=0){
                continue;
            }
            $arr[$_v['org_id']] = $_v['count'];
            $original_sum += $_v['count'];
        }
        return array('arr_original'=>$arr_original,'arr'=>$arr,'original_sum'=>$original_sum,'original_count'=>count($arr_original));
    }
    //取渠道号 对应的uid
    public function org_id_change_uid($arr,$softId,$ymd){
        if(self::$_changeSoftId){
            $softId = self::$_changeSoftId;
        }
        return \Union\Point_grant\Change\Change::get_instance()->org_id_change_uid($arr,$softId,$ymd);
    }
}

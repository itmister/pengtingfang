<?php
/**
 * 市场经理及下属软件安装明细信息模型
 */
namespace Dao\Stat;
use \Dao;

class Manager_subordinate_info extends Stat {

    protected static $_instance = null;

    /**
     * @return Manager_subordinate_info
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function get_list_by_ym($ym,$city_id,$type = 0){
        if(!$ym || !$city_id){
            return false;
        }
        $sql ="SELECT * FROM {$this->_get_table_name()} AS m LEFT JOIN user_base AS u ON m.uid = u.uid";
        $sql .= " WHERE m.ym = {$ym} AND m.city_id = {$city_id}";
        //普通技术员
        if($type == 1){
            $sql .= " AND m.puid > 0";
        }else{
            //市场经理
            $sql .= " AND m.puid = 0";
        }
        
        if(is_numeric($type) && $type > 0){
            $sql .=" AND u.type = {$type}";
        }elseif(!empty($type)){
            $sql .=" AND u.type IN ({$type})";
        }
        
        $result = $this->query($sql);
        return $result;
    }
}

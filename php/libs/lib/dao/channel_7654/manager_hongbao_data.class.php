<?php
/**
 * 市场经理红包数据
 */
namespace Dao\Channel_7654;
class Manager_hongbao_data extends Channel_7654 
{

    protected static $_instance = null;

    /**
     * @return Manager_hongbao_data
     */
    public static function get_instance() {
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    //获取前二次完成红包任务的市场经理
    public function get_twice_hongbao(){
        $sql = "
            SELECT parent_uid,type,count(DISTINCT ymd) AS day,count(uid) AS num,status FROM `channel_7654`.manager_hongbao_data 
            WHERE has_notice <= 2 GROUP BY parent_uid HAVING day <= 2
         ";
        $user_marketer_list = $this->query($sql);   
        return $user_marketer_list;
    }
    
    public function get_hongbao_data(){
        $sql = "
            SELECT parent_uid,type,count(uid) AS num,status,has_notice FROM `channel_7654`.manager_hongbao_data 
            WHERE status = 1 GROUP BY parent_uid
         ";
        $user_marketer_list = $this->query($sql);
        return $user_marketer_list;
    }
}
    
<?php
namespace Dao\Huayangnianhua_admin\Gs;
use \Dao\Huayangnianhua_admin\Huayangnianhua_admin;

class User_channel extends Huayangnianhua_admin {
    protected static $_instance = null;

    /**
     * @return User_channel
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

	public function main_qid($userid = ''){
        $sql = "SELECT distinct main_qid FROM {$this->_get_table_name()} WHERE status = 1";
        if($userid){
            $sql .=" AND userid = {$userid}";
        }
        $qid_prefix = $this->query($sql);
        
        return $qid_prefix;
    }
    
    public function qid_list($main_qid,$userid,$status = 1){
        if(!$main_qid){
            return false;
        }
        $sql = "SELECT id,qid FROM {$this->_get_table_name()} WHERE main_qid = '{$main_qid}' AND status = {$status}";
        if($userid){
            $sql .=" AND userid = {$userid}";
        }
        $qid_list = $this->query($sql);
        return $qid_list;
    }

	public function admin_list($where,$qid = '',$limit = ''){
        if(!$where)
        {
            return false;
        }
        $params = ['where' => $where];
        $admin_info = \Dao\Uxiake_admin\Ad_admin::get_instance()->find($params);
        if(!$admin_info)
        {
            return false;
        }
        
        //获取渠道账号渠道号列表
        $sql = "
            SELECT '{$admin_info['username']}' AS username,id AS channel_id,qid FROM {$this->_get_table_name()}
            WHERE status = 1 AND userid = {$admin_info['userid']}
        ";
        if($qid)
        {
            if(strstr($qid,'_'))
            {
                $sql .= " AND qid = '{$qid}'";
            }else
            {
                $sql .= " AND main_qid = '{$qid}'";
            }
        }
        if($limit)
        {
            $sql .= " LIMIT {$limit}";
        }
        
        $query_result = $this->query($sql);
        if($query_result)
        { 
            array_unshift($query_result, $admin_info); 
        }
        else
        {
            $query_result = [$admin_info];
        }
        
        return $query_result;
    }
}
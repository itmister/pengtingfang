<?php
namespace Dao\Kuaiya;
class Ad_admin extends  Kuaiya{

    protected static $_instance = null;

    /**
     * @return Ad_admin
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 获取运营账号下属渠道账号
     * @param string $parent_uid
     * @return boolean|array
     */
    public function qudao_list($parent_uid)
    {
        $params = [
            'field' => "username AS parent_username,userid AS parent_uid,0 AS userid",
            'where' => "userid = {$parent_uid} AND roleid = 2"
        ];
        $admin_info = $this->find($params);
        if(!$admin_info)
        {
            return false;
        }
        
        //获取运营下属渠道账号
        $sql = "SELECT *,'{$admin_info['parent_username']}' AS parent_username FROM {$this->_get_table_name()} WHERE roleid = 3 AND parent_uid = {$parent_uid}";
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
    
    /**
     * 获取渠道账号列表
     * @param integer $parent_uid 运营人员账号id
     * @return \Dao\mixed
     */
    public function get_qudao_list($parent_uid)
    {
       $sql = "SELECT userid,username FROM {$this->_get_table_name()} WHERE roleid = 3";
       if(is_numeric($parent_uid) && $parent_uid > 0)
       {
           $sql .= " AND parent_uid = {$parent_uid}";
       }
       else
       {
           $sql .= " AND parent_uid > 0";
       }
       $sql .= " ORDER BY username ASC";
       $query_result = $this->query($sql);
       return $query_result;
    }
    
    public function admin_list($where,$qid = '',$limit = ''){
        if(!$where){
            return false;
        }
        $params = ['where' => $where];
        $admin_info = Ad_admin::get_instance()->find($params);
        if(!$admin_info){
            return false;
        }
    
        //获取渠道账号渠道号列表
        $sql = "
            SELECT '{$admin_info['username']}' AS username,id AS channel_id,qid FROM user_channel
            WHERE status = 1 AND userid = {$admin_info['userid']}
        ";
        if($qid){
            if(strstr($qid,'_')){
                $sql .= " AND qid = '{$qid}'";
            }else{
                $sql .= " AND main_qid = '{$qid}'";
            }
        }
        if($limit){
            $sql .= " LIMIT {$limit}";
        }
    
        $query_result = $this->query($sql);
        if($query_result){
            array_unshift($query_result, $admin_info);
        }
        else{
            $query_result = [$admin_info];
        }
    
        return $query_result;
    }
}

<?php
namespace Dao\Uxiake_admin;
class Ad_admin extends  Uxiake_admin{

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
    public function qudao_list($parent_uid,$parent_roleid = 2,$roleid = 3,$username = '')
    {
        $params = [
            'field' => "username AS parent_username,userid AS parent_uid,0 AS userid",
            'where' => "userid = {$parent_uid} AND roleid = {$parent_roleid}"
        ];
        $admin_info = $this->find($params);
        if(!$admin_info)
        {
            return false;
        }
    
        //获取运营下属渠道账号
        $sql = "SELECT *,'{$admin_info['parent_username']}' AS parent_username FROM {$this->_get_table_name()} WHERE roleid = {$roleid} AND parent_uid = {$parent_uid}";
        if($username){
            $sql .= " AND username = '{$username}'";
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
    
    /**
     * 获取渠道账号列表
     * @param integer $parent_uid 运营人员账号id
     * @return \Dao\mixed
     */
    public function get_qudao_list($parent_uid,$roleid = 3)
    {
        $sql = "SELECT userid,username FROM {$this->_get_table_name()} WHERE roleid = {$roleid}";
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
    
    //获取广告商
    public function advertiser_list($customer_name,$limit){
        $sql = "
            SELECT 
                admin.userid,admin.username,admin.customer_name,COUNT(ad.id) AS promote_num 
            FROM `uxiake_admin`.`ad_admin` AS admin 
                LEFT JOIN `minninews_admin`.`ad_promote_base` AS ad ON admin.userid = ad.admin_uid
            WHERE 
                admin.sysname = 'ad'
        ";
       if($customer_name){
           $sql .= " AND admin.customer_name LIKE '%{$customer_name}%'";
       }
       $sql.="
           GROUP BY 
                admin.userid ORDER BY admin.userid DESC 
            LIMIT {$limit}
        ";
        $query_res = $this->query($sql);
        return $query_res;
    }
}

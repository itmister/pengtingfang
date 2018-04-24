<?php
namespace Dao\Huayangnianhua_admin\Gs;
use \Dao\Huayangnianhua_admin\Huayangnianhua_admin;

class User_xishu extends  Huayangnianhua_admin{

    protected static $_instance = null;

    /**
     * @return User_xishu
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 获取用户渠道号系数列表
     * @param string $qid
     * @param string $limit
     * @return \Dao\mixed
     */
    public function get_xishu_list($qid = '',$limit = ''){
        $sql = "SELECT * FROM gs_user_channel WHERE status = 1";
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
            $sql .=" LIMIT ".$limit;
        }
        $channel_list = $this->query($sql);
        if($channel_list)
        {
            foreach($channel_list AS $key => $channel)
            {
                $xinshu_data = $this->get_max_row($channel['id']);
                if(!$xinshu_data)
                {
                    unset($channel_list[$key]);
                }
                else
                {
                    $channel_list[$key] = array_merge($channel_list[$key], $xinshu_data);
                }
            }
        }
        return $channel_list;
    }
    
    /**
     * 通过渠道id获取最大记录
     * @param unknown $cid
     * @return mixed
     */
    public function get_max_row($cid)
    {
        $sql = "SELECT xishu,modify_time,effective_date FROM (SELECT * FROM {$this->_get_table_name()} WHERE cid = {$cid} ORDER BY effective_date DESC,modify_time DESC) {$this->_get_table_name()} WHERE cid = {$cid} GROUP BY cid";
        $xinshu_data = current($this->query($sql));
        return $xinshu_data;
    }
    
    /**
     * 渠道号系数
     * @param string $cid_string
     * @param string $start_date
     * @param string $end_date
     * @return boolean|array
     */
    public function user_xishu_data($cid_string,$start_date,$end_date)
    {
        if(!$cid_string)
        {
            return false;
        }
        $sql = "
            SELECT xishu,effective_date,(SELECT qid FROM gs_user_channel WHERE id = cid) AS qid 
            FROM (SELECT * FROM {$this->_get_table_name()} WHERE cid IN({$cid_string}) ORDER BY modify_time DESC) {$this->_get_table_name()}  WHERE cid IN({$cid_string})";
        if($start_date && $end_date)
        {
            $sql .= " AND (effective_date <= {$start_date} OR effective_date <= {$end_date})";
        }
        elseif($start_date)
        {
            $sql .= " AND effective_date <= {$start_date}";
        }
        $sql .= " GROUP BY cid,effective_date";
        $query_result = $this->query($sql);
        
        $user_xishu = [];
        if($query_result)
        {
            foreach ($query_result as $result)
            {
                $key = $result['qid'];
                $sub_key = $result['effective_date'];
                $user_xishu[$key][$sub_key] = $result['xishu'];
            }
        }
        return $user_xishu;
    }
}

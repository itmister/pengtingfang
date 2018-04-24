<?php
namespace Dao\Huayangnianhua_admin\Gs;
use \Dao\Huayangnianhua_admin\Huayangnianhua_admin;

class Formula_application extends Huayangnianhua_admin {

    protected static $_instance = null;

    /**
     * @return Formula_application
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function lists($qid = '',$limit = ''){
        $where = '';
        if($qid)
        {
            if(strstr($qid,'_'))
            {
                $where ="WHERE qid LIKE '{$qid}%'";
            }else
            {
                $where ="WHERE qid LIKE '{$qid}\_%'";
            }
        }
        $sql = "SELECT * FROM (SELECT * FROM {$this->_get_table_name()} {$where} ORDER BY effective_date DESC,modify_time DESC) {$this->_get_table_name()}";
        $sql .=" {$where} GROUP BY qid";
        if($limit)
        {
            $sql .=" LIMIT ".$limit;
        }
        $data_list = $this->query($sql);
        if($data_list)
        {
            foreach($data_list as $key => $val)
            {
                $data_list[$key]['username'] = \Dao\Huayangnianhua_admin\Gs\User_channel::get_instance()->get_one('username', "qid = '{$val['qid']}'");
            }
        }
        return $data_list;
    }
    
    /**
     * 渠道公式
     * @param string $qid_string
     * @param string $start_date
     * @param string $end_date
     * @return boolean|array
     */
    public function user_formula_data($qid,$start_date,$end_date)
    {
        if(!$qid)
        {
            return false;
        }
        $sql = "
            SELECT qid,fcid,effective_date FROM (SELECT * FROM {$this->_get_table_name()} 
            WHERE qid IN('{$qid}') ORDER BY modify_time DESC) {$this->_get_table_name()} WHERE qid IN('{$qid}')
        ";
        if($start_date && $end_date)
        {
            $sql .= " AND (effective_date <= {$start_date} OR effective_date <= {$end_date})";
        }
        elseif($end_date)
        {
            $sql .= " AND effective_date <= {$end_date}";
        }
        $sql .= " GROUP BY qid,effective_date";
        $query_result = $this->query($sql);

        $user_formula = [];
        if($query_result)
        {
            foreach ($query_result as $result)
            {
                $key = $result['qid'];
                $sub_key = $result['effective_date'];
                $user_formula[$key][$sub_key] = $result['fcid'];
            }
        }
        return $user_formula;
    }
}

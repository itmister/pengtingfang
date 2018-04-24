<?php
/**
 * 市场经理打卡签到
 */
namespace Dao\Channel_7654;
class Manager_clock_in extends Channel_7654 
{

    protected static $_instance = null;

    /**
     * @return Manager_clock_in
     */
    public static function get_instance()
    {
        if (empty(self::$_instance)) 
        {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function get_list($where = '',$limit = '')
    {
        $sql ="
            SELECT `ymd`,
            COUNT(`uid`) AS total_num,COUNT(CASE WHEN `status` = 1 THEN `uid` END) AS num1,
            COUNT(CASE WHEN `status` = 0 THEN `uid` END) AS num2 FROM {$this->_get_table_name()}
        ";
        //查询条件
        if($where)
        {
            $sql .= " WHERE ".$where;
        }
        $sql .=" GROUP BY `ymd` ORDER BY `ymd` DESC";
        
        //分页
        if($limit)
        {
            $sql .= " LIMIT ".$limit;
        }
        $query_result = $this->query($sql);
        return $query_result;
    }
}

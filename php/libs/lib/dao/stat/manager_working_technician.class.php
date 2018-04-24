<?php
/**
 * 见习市场经理下属
 */
namespace Dao\Stat;
use \Dao;

class Manager_working_technician extends Stat 
{

    protected static $_instance = null;

    /**
     * @return Manager_working_technician
     */
    public static function get_instance()
    {
        if (empty(self::$_instance)) 
        {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 查询下属列表
     * @param string $where
     * @param string $orderby
     * @return boolean|\Dao\mixed
     */
    public function select($puid,$info_is_complete ="",$orderby ="")
    {
        if(!$puid)
        {
            return false;
        }
        
        $sql ="SELECT u.*,m.ip_count FROM {$this->_realTableName} AS m INNER JOIN user_base AS u ON m.uid = u.uid";
        $sql .=" WHERE m.puid = {$puid}";
        if($info_is_complete == 1)
        {
            $sql .=" AND u.info_is_complete = {$info_is_complete} AND ip_count > 0";
        }
        else if(is_numeric($info_is_complete) && $info_is_complete == 0)
        {
            $sql .=" AND ip_count > 0";
        }
        
        if($orderby)
        {
            $sql .=" ORDER BY ".$orderby;
        }
        $result = $this->query($sql);
        return $result;
    }
}

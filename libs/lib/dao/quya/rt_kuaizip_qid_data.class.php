<?php
namespace Dao\Quya;
class Rt_kuaizip_qid_data extends Quya {

    protected static $_instance = null;

    /**
     * @return Rt_kuaizip_qid_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 统计渠道数据
     * @param string $qid_string
     * @param string $start_date
     * @param string $end_date
     * @return boolean|\Dao\mixed
     */
    public function statistics_main($qid_prefix,$qid_string,$start_date,$end_date){
        if(!$start_date || !$end_date || (!$qid_string && !$qid_prefix)){
            return false;
        }
        //510001
        $sql = "
            SELECT SUM(CASE WHEN `logtype` = 510006 THEN `cnt` END) AS install_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600119 THEN `cnt` END),0) AS install_xishu,
            IFNULL(SUM(CASE WHEN `logtype` = 600104 THEN `cnt` END),0) AS start_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600102 THEN `cnt` END),0) AS num,
            IFNULL(SUM(CASE WHEN `logtype` = 600108 THEN `cnt` END),0) AS uninstall_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600212 THEN `cnt` END),0) AS morrow_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600101 THEN `cnt` END),0) AS morrow_num2,
            IFNULL(SUM(CASE WHEN `logtype` = 699011 THEN `cnt` END),0) AS wangba_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600109 THEN `cnt` END),0) AS install_uninstall,
            logdt,qid,price,varprice,softid
            FROM {$this->_get_table_name()}
            WHERE price > 0
        ";
        if($qid_string){
            $sql .=" AND qid IN ({$qid_string})";
        }elseif($qid_prefix){
            $sql .=" AND qid LIKE '{$qid_prefix}\_%'";
        }
        $sql .=" AND logdt BETWEEN {$start_date} AND {$end_date} GROUP BY logdt,qid,softid HAVING install_num > 0 ORDER BY logdt DESC";
        $query_result = $this->query($sql);
        return $query_result;
    }
    
    /**
     * 统计子渠道数据
     * @param string $qid_string
     * @param string $start_date
     * @return boolean|\Dao\mixed
     */
    public function statistics_sub1($qid_prefix,$qid_string,$start_date,$end_date = 0){
        if(!$start_date || (!$qid_string && !$qid_prefix)){
            return false;
        }
    
        $sql = "
            SELECT SUM(CASE WHEN `logtype` = 510006 THEN `cnt` END) AS install_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600119 THEN `cnt` END),0) AS install_xishu,
            IFNULL(SUM(CASE WHEN `logtype` = 600104 THEN `cnt` END),0) AS start_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600102 THEN `cnt` END),0) AS num,
            IFNULL(SUM(CASE WHEN `logtype` = 600108 THEN `cnt` END),0) AS uninstall_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600212 THEN `cnt` END),0) AS morrow_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600101 THEN `cnt` END),0) AS morrow_num2,
            IFNULL(SUM(CASE WHEN `logtype` = 699011 THEN `cnt` END),0) AS wangba_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600109 THEN `cnt` END),0) AS install_uninstall,
            logdt,qid,price,varprice,softid
            FROM {$this->_get_table_name()}
            WHERE price > 0
        ";
        //渠道号
        if($qid_string)
        {
            $sql .=" AND qid IN ({$qid_string})";
        }
        elseif($qid_prefix)
        {
            $sql .=" AND qid LIKE '{$qid_prefix}\_%'";
        }
        if($start_date && $end_date)
        {
            $sql .= " AND logdt BETWEEN {$start_date} AND {$end_date}";
        }
        elseif($start_date)
        {
            $sql .= " AND logdt = {$start_date}";
        }
        
        $sql .= " GROUP BY qid,logdt,softid HAVING  install_num  > 0 ORDER BY logdt DESC";
        $query_result = $this->query($sql);
        return $query_result;
    }
    
    public function statistics_sub($params){
         if(!$params['ymd_start']){
            return false;
         }
    
        $sql = "
            SELECT SUM(CASE WHEN `logtype` = 510006 THEN `cnt` END) AS install_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600119 THEN `cnt` END),0) AS install_xishu,
            IFNULL(SUM(CASE WHEN `logtype` = 600104 THEN `cnt` END),0) AS start_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600102 THEN `cnt` END),0) AS num,
            IFNULL(SUM(CASE WHEN `logtype` = 600108 THEN `cnt` END),0) AS uninstall_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600212 THEN `cnt` END),0) AS morrow_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600101 THEN `cnt` END),0) AS morrow_num2,
            IFNULL(SUM(CASE WHEN `logtype` = 699011 THEN `cnt` END),0) AS wangba_num,
            IFNULL(SUM(CASE WHEN `logtype` = 600109 THEN `cnt` END),0) AS install_uninstall,
            logdt,qid,price,varprice,softid,qid
            FROM {$this->_get_table_name()}
            WHERE varprice IN(1,2)
        ";
        //渠道号
        if($params['qid']){
            $sql .=" AND qid = '{$params['qid']}'";
        }else if($params['main_qid']){
            $sql .= " AND qid LIKE '%{$params['main_qid']}%'";
        }
        if($params['ymd_start'] && $params['ymd_end']){
            $sql .= " AND logdt BETWEEN {$params['ymd_start']} AND {$params['ymd_end']}";
        }
        elseif($params['ymd_start']){
            $sql .= " AND logdt = {$params['ymd_start']}";
        }
    
        $sql .= " GROUP BY qid,logdt,softid HAVING  install_num  > 0 ORDER BY logdt DESC";
        
        if($params['limit']){
            $sql .=" LIMIT {$params['limit']}";
        }
        
        $query_result = $this->query($sql);
        return $query_result;
    }
    
    public function statistics_count($params){
        if(!$params['ymd_start']){
            return false;
        }
    
        $sql = "
            SELECT 
                COUNT(DISTINCT qid,logdt,softid) AS num FROM {$this->_get_table_name()}
            WHERE varprice IN(1,2) AND `logtype` = 510006
        ";
        //渠道号
        if($params['qid']){
            $sql .=" AND qid = '{$params['qid']}'";
        }else if($params['main_qid']){
            $sql .= " AND qid LIKE '%{$params['main_qid']}%'";
        }
        if($params['ymd_start'] && $params['ymd_end']){
            $sql .= " AND logdt BETWEEN {$params['ymd_start']} AND {$params['ymd_end']}";
        }
        elseif($params['ymd_start']){
            $sql .= " AND logdt = {$params['ymd_start']}";
        }
        $result = current($this->query($sql));
        return $result['num'] ? $result['num'] : 0;
    }
}

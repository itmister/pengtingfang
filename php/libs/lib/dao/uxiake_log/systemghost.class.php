<?php
namespace Dao\Uxiake_log;
use \Dao;
class Systemghost extends Uxiake_log {

    /**
     * @return Dao\Uxiake_log\Systemghost
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    
    public function get_system_data($ymd){
        $sql = "
            SELECT
            	{$ymd} AS ymd,`tuuid` AS uid,COUNT(DISTINCT `UUID`) AS `system`
            FROM
            	`{$this->_realTableName}{$ymd}`
            WHERE `UUID` NOT IN (SELECT DISTINCT `UUID` FROM `pepackageinstall{$ymd}`)
            GROUP BY
            	`tuuid`
            ";
       $query = $this->query($sql);
       return $query;
    }
    
    public function get_system_software_data($ymd){
        $sql = "
            SELECT
            	{$ymd} AS ymd,
            	sy.`tuuid` AS uid,
            	COUNT(DISTINCT sy.UUID) AS system_software
            FROM
            	`{$this->_realTableName}{$ymd}` AS sy
            INNER JOIN `pepackageinstall{$ymd}` AS pe ON sy.`UUID` = pe.`UUID`
            GROUP BY
            	sy.`tuuid`
        ";
        return $this->query($sql);
    }
    
    //使用次数（按照uuid统计）
    public function get_use_data($ymd){
       $time = time();
       $sql = "
           SELECT {$ymd} AS ymd,COUNT(DISTINCT a.`UUID`) AS use_num,a.uid,{$time} AS created FROM (
            SELECT `UUID`,`tuuid` AS uid FROM `{$this->_realTableName}{$ymd}` GROUP BY `UUID`
            UNION 
            SELECT `UUID`,`tuuid` AS uid FROM `pcpackageinstall{$ymd}` GROUP BY `UUID`
          ) AS a GROUP BY a.uid
       ";
       return $this->query($sql);
    }
}

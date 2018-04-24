<?php
namespace Dao\Uxiake_log;
use \Dao;
class Pcpackageinstall extends Uxiake_log {

    /**
     * @return Dao\Uxiake_log\Pcpackageinstall
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    
    //装软件次数
    public function get_install_software($ymd){
        $sql = "
            SELECT {$ymd} AS ymd,a.uid,COUNT(a.uid) AS install_software FROM (
                 SELECT
                     `tuuid` AS uid
                 FROM
                     `{$this->_realTableName}{$ymd}`
                 GROUP BY
                     UUID
            ) AS a GROUP BY a.uid
	   ";
        $query = $this->query($sql);
        return $query;
    }
    
    
    public function get_install_software_data($ymd){
        $time = time();
        $sql = "
            SELECT
                {$ymd} AS ymd,
            	`tuuid` AS uid,
            	`package_id` AS soft_id,
            	`populariz_channel` AS qid,
            	COUNT(DISTINCT `UUID`) AS `install` ,{$time} AS `created`
            FROM
            	`{$this->_realTableName}{$ymd}`
            GROUP BY
            	`tuuid`,
            	`soft_id`,
            	`qid`
        ";
        $query = $this->query($sql);
        return $query;
    }

}

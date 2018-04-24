<?php
namespace Dao\Huayangnianhua_log;
use \Dao;
class Userclick extends Huayangnianhua_log {
    /**
     * @return Dao\Huayangnianhua_log\Userclick
     */
    public static function get_instance(){
        return parent::get_instance();
    }

	public function create_index($ymd){
        $sql = "show keys from `userclick{$ymd}` WHERE key_name = 'qid'";
        $index = $this->query($sql);
        if(!$index){
            $sql = "
                    ALTER TABLE `userclick{$ymd}`
                    ADD INDEX `qid` USING BTREE (`clicknum`, `QID`, `btnname`);
                ";
            $this->query($sql);
        }
    }
    /**
     * @return array
     */
    public function get_data($ymd){
		//创建索引
		$this->create_index($ymd);

		$time = time();
        $sql = "
			SELECT 
				{$ymd} AS ymd,substring_index(trim(QID),'_',1) AS main_qid,QID AS qid,btnname,SUM(clicknum) AS clicknum,COUNT(DISTINCT UID) AS num,{$time} as dateline 
			FROM `{$this->_realTableName}{$ymd}` 
			GROUP BY QID,btnname
		";
        $data = $this->query($sql);
        return $data;
    }
}

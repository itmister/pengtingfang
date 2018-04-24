<?php
namespace Dao\Wplayer_log;
use \Dao;
class Kunbang_updatechecker_run_task extends Wplayer_log {

    protected static $_instance = null;
    /**
     * @return Kunbang_updatechecker_run_task
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

	public function create_index($ymd){
        $sql = "show keys from `kunbang_updatechecker_run_task{$ymd}` WHERE key_name = 'uid'";
        $index = $this->query($sql);
        if(!$index){
            $sql = "
                    ALTER TABLE `kunbang_updatechecker_run_task{$ymd}`
                    ADD INDEX `uid` (`is_show`, `UID`, `kunbang_software`) USING BTREE,
					ADD INDEX `source` (`UID`,`is_show`,`is_checked`,`is_installed`,`kunbang_software`,`kunbang_source`) USING BTREE,
					ADD INDEX `pos` (`UID`,`kunbang_software`,`show_position`) USING BTREE,
					ADD INDEX `ver` (`UID`,`is_show`,`is_checked`,`is_installed`,`kunbang_software`,`Version`) USING BTREE;
                ";
            $this->query($sql);

			$this->_kunbang_temp_data($ymd);
        }
    }

    public function get_kunbang_data($ymd){
        $sql = "SELECT {$ymd} as ymd,kunbang_software as kunbangid,count(*) as day_count,count(case when is_show=1 then UID end) as show_num,count(case when is_checked=1 then UID end) as select_num,count(DISTINCT case when is_show=1 then UID end) as show_num_user,count(DISTINCT case when is_checked=1 then UID end) as day_kunbang_install_user,count(DISTINCT case when is_checked=1 and is_installed=1 then UID end) as day_kunbang_install_success_user,count(DISTINCT case when is_checked=1 and is_installed=0 then UID end) as day_kunbang_install_fail_user,UNIX_TIMESTAMP() as dateline,kunbang_source from `{$this->_realTableName}{$ymd}` GROUP BY kunbang_source;
        ";
        return $this->query($sql);
    }
    public function get_kunbang_ver_data($ymd){
        $sql = "SELECT {$ymd} as ymd,Version as ver,kunbang_software as kunbangid,count(*) as day_count,count(case when is_show=1 then UID end) as show_num,count(case when is_checked=1 then UID end) as select_num,count(DISTINCT case when is_show=1 then UID end) as show_num_user,count(DISTINCT case when is_checked=1 then UID end) as day_kunbang_install_user,count(DISTINCT case when is_checked=1 and is_installed=1 then UID end) as day_kunbang_install_success_user,count(DISTINCT case when is_checked=1 and is_installed=0 then UID end) as day_kunbang_install_fail_user,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` GROUP BY Version;
        ";
        return $this->query($sql);
    }

    public function get_kunbang_pos_data($ymd){
        $sql = "SELECT {$ymd} as ymd,kunbang_software as kunbangid,show_position,count(*) as show_num,count(DISTINCT UID) as show_num_user,UNIX_TIMESTAMP() as dateline  FROM `{$this->_realTableName}{$ymd}` where show_position=0;";
        return $this->query($sql);
    }

    //创建临时表
    private function _kunbang_temp_data($ymd){
        //捆绑数据
        $temp_table_name = "kunbang_run_task_temp{$ymd}";
        $sql = "
            CREATE TABLE `{$temp_table_name}` (
            `UID` varchar(32) NOT NULL,
            `kunbang_software` varchar(255) NOT NULL,
            UNIQUE KEY `uid` (`UID`,`kunbang_software`) USING BTREE
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ";
        $this->query($sql);
		
		//分批添加
        $sql = "SELECT count(DISTINCT UID,kunbang_software) AS num FROM `kunbang_updatechecker_run_task{$ymd}` WHERE is_show = 1";
		$count = current($this->query($sql));
        $p_size = ceil($count['num']/100000);
        for($i=1;$i<=$p_size;$i++){
            $page_start = ($i-1)*100000;
            $page_end = 100000;

			$sql = "
				INSERT INTO `{$temp_table_name}` (UID,kunbang_software)
					SELECT 
						UID,kunbang_software FROM `kunbang_updatechecker_run_task{$ymd}` 
					WHERE 
						is_show = 1 
					GROUP BY 
						UID,kunbang_software 
					LIMIT ".$page_start.','.$page_end;
			$this->query($sql);
        }
    }
    
    /**
     * 捆绑竟品数据
     * @param int $ymd
     */
    public function get_rate_date($ymd){
        $time = time();
        $sql = "
            SELECT
                {$ymd} AS ymd,k.kunbang_software,j.software AS jpname,COUNT(distinct k.UID) AS install_num,{$time} AS dateline
            FROM `kunbang_run_task_temp{$ymd}` AS k
            LEFT JOIN `jingpin_temp{$ymd}` AS j ON k.UID=j.UID
            WHERE
                j.software IN('jsdb','qqgj','360sd')
            GROUP BY
                j.software,k.kunbang_software
        ";
        $rate_data = $this->query($sql);
    
        //删除临时表
        $drop_sql = "DROP TABLE `kunbang_run_task_temp{$ymd}`;";
        $this->query($drop_sql);
        
        return $rate_data;
    }
}

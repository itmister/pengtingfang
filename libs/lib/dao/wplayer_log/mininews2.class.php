<?php
namespace Dao\Wplayer_log;
use \Dao;
class Mininews2 extends Wplayer_log{

    protected static $_instance = null;
    /**
     * @return Mininews2
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function create_index($ymd){
        $sql = "show keys from `mininews2{$ymd}` WHERE key_name = 'ver'";
        $index = $this->query($sql);
        if(!$index){
            $sql = "
                ALTER TABLE `mininews2{$ymd}`
                ADD INDEX `ver` (`UID`,`name`,`Version`) USING BTREE,
            ";
            $this->query($sql);
        }
    }
    
    /**
     * 同步捆绑数据（mini）
     */
    public function sync_kunbang_mini_data($ymd){
        if(date('G') > 3){
            return false;
        }
        $filed = "`TimeStamp`,`SoftName`,`SoftID`,`Version`,`IP`,`UID`,`ClientTime`,`QID`,`InstallYmd`,`Os`,`DiskID`,`MacID`,`CpuID`,`UUID`,`package_md5`";
        $sql = "INSERT INTO `{$this->_realTableName}{$ymd}` ({$filed},`name`) SELECT {$filed},`kunbang_software` FROM `kunbang{$ymd}` WHERE kunbang_software LIKE '%mininewshn%'";
        $this->query($sql);
    }
    public function get_mininews2_install($ymd){
        $time = time();
        $sql = "
            SELECT {$ymd} as ymd,COUNT(*) AS original_install,COUNT(DISTINCT UID) AS `install`,`name`,{$time} AS dateline
            FROM (SELECT * FROM `{$this->_realTableName}{$ymd}` order by `TimeStamp` ASC) `{$this->_realTableName}{$ymd}` group by `name`
        ";
        return $this->query($sql);
    }
    
    //版本数据
    public function get_mininews2_ver_data($ymd){
        $time = time();
        $sql = "
            SELECT
                {$ymd} AS ymd,
            	`name`,
            	Version AS ver,
            	COUNT(1) AS original_install,
            	COUNT(DISTINCT UID) AS `install`,
            	{$time} AS dateline
            FROM
            	mininews2{$ymd}
            GROUP BY
            	`name`,
            	Version
        ";
        return $this->query($sql);
    }
    
    //时间点数据
    public function get_mininews2_time_data($ymd){
        $time = time();
        $sql = "
            SELECT
                {$ymd} AS ymd,
            	`name`,
            	FROM_UNIXTIME(ClientTime,'%k') AS h,
            	COUNT(1) AS original_install,
            	COUNT(DISTINCT UID) AS `install`,
            	{$time} AS dateline
            FROM
            	mininews2{$ymd}
            GROUP BY
            	`name`,h
        ";
        return $this->query($sql);
    }
    
    public function get_mininews2_time_qid_count($ymd){
        $sql = "
            SELECT sum(a.num) AS num FROM(
                SELECT COUNT(distinct `name`,QID) AS num FROM mininews2{$ymd} GROUP BY FROM_UNIXTIME(ClientTime,'%H')
            ) as a
         ";
        $query_res = current($this->query($sql));
        return $query_res['num'] ? $query_res['num'] : 0; 
    }
    
    //时间点数据
    public function get_mininews2_time_qid_data($ymd,$limit){
        $time = time();
        $sql = "
            SELECT
                {$ymd} AS ymd,
                `name`,
                QID AS qid,
                FROM_UNIXTIME(ClientTime,'%k') AS h,
                COUNT(1) AS original_install,
                COUNT(DISTINCT UID) AS `install`,
                {$time} AS dateline
            FROM
                mininews2{$ymd}
            GROUP BY
                `name`,h,QID
            LIMIT {$limit}
        ";
        return $this->query($sql);
    }
    
    public function get_data($ymd,$name,$limit){
        $sql = "SELECT * FROM mininews2{$ymd} WHERE name IN({$name}) ORDER BY name ASC LIMIT {$limit}";
        return $this->query($sql);
    }
    
    public function fetch_data_by_main_qid($ymd,$name,$field){
        $sql = "
            SELECT 
                {$ymd} AS ymd,substring_index(trim(QID),'_',1) AS qid,COUNT(UID) AS original_{$field},COUNT(DISTINCT UID) AS {$field}
            FROM mininews2{$ymd} 
            WHERE 
                name = '{$name}' 
            GROUP BY 
                substring_index(trim(QID),'_',1)
        ";
        return $this->query($sql);
    }
    
    public function fetch_data_by_qid($ymd,$name,$field){
        $sql = "SELECT {$ymd} AS ymd,QID AS qid,COUNT(UID) AS original_{$field},COUNT(DISTINCT UID) AS {$field} FROM mininews2{$ymd} WHERE name = '{$name}' GROUP BY QID";
        return $this->query($sql);
    }
}

<?php
namespace Dao\Udashi_soft_bibei_log;
use \Dao;
class Download_error extends Udashi_soft_bibei_log {

    protected static $_instance = null;
    /**
     * @return Download_error
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 必备：下载失败量+ 在线下载失败 + 离线下载失败
     * @return array
     */
    public function get_bibei_count($ymd,$appid){
        if($appid)
            $appid = " and appid in({$appid})";
            $sql = "SELECT * from
            (SELECT count(DISTINCT UID,appid) {$this->_realTableName}  from `{$this->_realTableName}{$ymd}` where 1 {$appid})a,
            (SELECT count(DISTINCT UID,appid) {$this->_realTableName}_zx  from `{$this->_realTableName}{$ymd}` where ONLINE=1 {$appid})b,
            (SELECT count(DISTINCT UID,appid) {$this->_realTableName}_lx  from `{$this->_realTableName}{$ymd}` where ONLINE=0 {$appid})c";
            $table = $this->query("SHOW TABLES LIKE '{$this->_realTableName}{$ymd}'");
            if(count($table)>0){
                return $this->query($sql)[0];
            }else{
               return array( "{$this->_realTableName}"=>0,"{$this->_realTableName}_zx"=>0,"{$this->_realTableName}_lx"=>0 );
            }
    }

    /**
     * 必备：安装量+ 在线安装 + 离线安装  详细
     * @return array
     */
    public function get_bibei_count_detail($ymd){
        $sql = "SELECT {$ymd} as ymd,appid,UNIX_TIMESTAMP() as dateline,COUNT(DISTINCT UID) as num,count(DISTINCT case when `online`=1 then id end) as num_zx, count(DISTINCT case when `online`=0 then id end) as num_lx FROM `{$this->_realTableName}{$ymd}` GROUP BY appid";
        return $this->query($sql);
    }

    public function get_bibei_ver_count($ymd){
        $sql = "SELECT version as ver,{$ymd} as ymd,count(distinct UID,appid) as download_fail,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` group by version";
        return $this->query($sql);
    }
    public function get_bibei_ver_detail_count($ymd){
        $sql = "SELECT version as ver,{$ymd} as ymd,count(distinct UID,appid) as download_stop,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` where errorcode=5 group by version";
        return $this->query($sql);
    }

    public function get_bibei_ver_detail_count_network($ymd){
        $sql = "SELECT version as ver,{$ymd} as ymd,count(distinct UID,appid) as download_network_fail,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` where errorcode=2 group by version";
        return $this->query($sql);
    }
    public function get_bibei_ver_detail_count_http($ymd){
        $sql = "SELECT version as ver,{$ymd} as ymd,count(distinct UID,appid) as download_http_fail,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` where errorcode=3 group by version";
        return $this->query($sql);
    }

    public function get_bibei_ver_detail_count_md5($ymd){
        $sql = "SELECT version as ver,{$ymd} as ymd,count(distinct UID,appid) as download_md5_fail,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` where errorcode=4 group by version";
        return $this->query($sql);
    }
    /**
     * 安装包失败的详情数据
     * get_bibei_ver_detail_count_data
     */
    public function get_bibei_ver_detail_count_data($ymd){
        $sql = "SELECT version as ver,{$ymd} as ymd,appid,count(distinct case when errorcode=2 then UID end) as download_network_fail,
        count(distinct case when errorcode=3 then UID end) as download_http_fail,
        count(distinct case when errorcode=4 then UID end) as download_md5_fail,
        count(distinct case when errorcode=5 then UID end) as download_stop,
        UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` group by version,appid";
        return $this->query($sql);
    }
}

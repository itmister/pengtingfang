<?php
namespace Dao\Udashi_soft_bibei_log;
use \Dao;
class Install_error extends Udashi_soft_bibei_log {

    protected static $_instance = null;
    /**
     * @return Install_error
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 获取信息 渠道数据的安装失败信息记录数
     * @return array
     */
    public function select_all($ymd,$name,$soft_id,$username){
        $time =time();
        $sql = " SELECT {$ymd} as ymd,'{$username}' as name,count(distinct UID) as num,'{$soft_id}' as soft_id,{$time} as dateline from `{$this->_realTableName}{$ymd}` where appid like '%{$name}%' ";
        return $this->query($sql);
    }
    
    
    /**
     * 必备：安装失败量+ 在线安装失败 + 离线安装失败
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
        $sql = "SELECT version as ver,{$ymd} as ymd,count(distinct UID,appid) as install_fail,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` group by version";
        return $this->query($sql);
    }

    public function get_bibei_ver_detail_count($ymd){
        $sql = "SELECT version as ver,{$ymd} as ymd,count(distinct UID,appid) as install_stop,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` where errorcode=2 group by version";
        return $this->query($sql);
    }

}

<?php
namespace Dao\Udashi_soft_bibei_log;
use \Dao;
class Install extends Udashi_soft_bibei_log{

    /**
     * @return Dao\Udashi_soft_bibei_log\Install
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function select_all($ymd,$name,$soft_id,$username){
        $time =time();
        $sql = " SELECT {$ymd} as ymd,'{$username}' as name,count(distinct UID) as num,'{$soft_id}' as soft_id,{$time} as dateline from `{$this->_realTableName}{$ymd}` where appid like '%{$name}%' ";
        return $this->query($sql);
    }
    
    
    /**
     * 获取信息 渠道数据的安装信息记录数
     * @return array
     */
    public function get_all_online_count($ymd){
        $sql = "SELECT count(DISTINCT UID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    
    
    /**
     * 必备：安装量+ 在线安装 + 离线安装  详细
     * @return array
     */
    public function get_bibei_count_detail($ymd,$param){
        $time =time();
        $sql = "select * from 
(SELECT  {$ymd} as ymd , '{$param['soft_id']}' as soft_id , '{$param['appid']}' as appid , '{$param['name']}' as name , {$time} as dateline , COUNT(DISTINCT UID) as num FROM `{$this->_realTableName}{$ymd}` WHERE appid = '{$param['appid']}' ) a ,
(SELECT  COUNT(DISTINCT UID) as num_zx FROM `{$this->_realTableName}{$ymd}` WHERE appid = '{$param['appid']}' and `online`=1) b ,
(SELECT  COUNT(DISTINCT UID) as num_lx FROM `{$this->_realTableName}{$ymd}` WHERE appid = '{$param['appid']}' and `online`=0) c ";
        return $this->query($sql);
    }
    
    /**
     * 必备：安装量+ 在线安装 + 离线安装
     * @return array
     */
    public function get_bibei_count($ymd,$appid){
        if($appid)
            $appid = " and appid in({$appid})";
        $sql = "SELECT * from
        (SELECT count(DISTINCT UID,appid) {$this->_realTableName}  from `{$this->_realTableName}{$ymd}` where 1 {$appid})a,
        (SELECT count(DISTINCT UID,appid) {$this->_realTableName}_zx  from `{$this->_realTableName}{$ymd}` where ONLINE=1 {$appid})b,
        (SELECT count(DISTINCT UID,appid) {$this->_realTableName}_lx  from `{$this->_realTableName}{$ymd}` where ONLINE=0 {$appid})c";
        return $this->query($sql)[0];
    }

    public function get_bibei_ver_count($ymd){
        $sql = "SELECT version as ver,{$ymd} as ymd,count(distinct UID,appid) as install,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}{$ymd}` group by version";
        return $this->query($sql);
    }

}

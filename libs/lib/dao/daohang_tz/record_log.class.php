<?php
namespace Dao\Daohang_tz;
use \Dao;
class Record_log extends Daohang_tz{

    /**
     * @return Record_log
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_all_url_count($ymd){
        $sql = "SELECT count(DISTINCT url) as num FROM `{$this->_realTableName}{$ymd}`;";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }

    public function get_all_url($ymd,$limit=''){
        $sql = "SELECT ymd,url,count(*) as pv,count(DISTINCT ip) as ip,UNIX_TIMESTAMP() as dateline FROM `{$this->_realTableName}{$ymd}` WHERE ymd = {$ymd} GROUP BY url";
        if($limit){
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }
    
    public function get_count_uds_ip($ymd){
        $sql = " select count(DISTINCT ip) num from `{$this->_realTableName}{$ymd}` where `host`='hao.1238756.com' or `host`='hao1.5678567.com' or `host`='ie.hao3607.com' or `host`='u.xiaoxiangbz.com'";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
        
    }
    
    public function get_count_uds_ip_n($ymd){
        $sql =" select count(a.ip) num from ( select DISTINCT ip from `{$this->_realTableName}{$ymd}` where `host`='hao.1238756.com' or `host`='hao1.5678567.com' or `host`='ie.hao3607.com' or `host`='u.xiaoxiangbz.com') a where not EXISTS(SELECT ip FROM bank_ip where ip=a.ip); ";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
    
    
    /**
     * 新增IP库
     * @return array
     */
    public function insert_bank_ip($ymd){
        $sql = "INSERT INTO bank_ip  SELECT {$ymd} ymd ,ip from  (select {$ymd} ymd ,ip FROM {$this->_realTableName}{$ymd} where `host`='hao.1238756.com' or `host`='hao1.5678567.com' or `host`='ie.hao3607.com' or `host`='u.xiaoxiangbz.com' GROUP BY ip) a where not EXISTS (SELECT ip FROM bank_ip where ip=a.ip);";
        $this->query($sql);
    }
    
    public function get_count_ip_cll($ymd,$uymd){
        $sql = " select count(a.ip) num from ( SELECT DISTINCT ip from `{$this->_realTableName}{$ymd}`  where `host`='hao.1238756.com' or `host`='hao1.5678567.com' or `host`='ie.hao3607.com' or `host`='u.xiaoxiangbz.com' ) a where  EXISTS( select ip from bank_ip where  ymd={$uymd} and  ip=a.ip  )";
        $data = $this->query($sql);
        return isset($data[0]['num'])&&$data[0]['num']>0 ? $data[0]['num'] : 0;
    }
}



<?php
namespace Dao\Udashi_log;
use \Dao;
class Record extends Udashi_log{

    /**
     * @return Record
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
}

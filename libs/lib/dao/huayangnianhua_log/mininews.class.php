<?php
namespace Dao\Huayangnianhua_log;
use \Dao;
class Mininews extends Huayangnianhua_log {

    protected static $_instance = null;
    /**
     * @return Mininews
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function get_data($ymd){
        $time = time();
        $sql = "select {$ymd} AS ymd,type AS news_type,count(1) AS click_num,{$time} AS dateline from `mininews{$ymd}` group by type";
        return $this->query($sql);
    }
    public function get_detail_data($ymd){
        $time = time();
        $sql = "select {$ymd} AS ymd,pos,type AS news_type,count(1) AS click_num,count(DISTINCT UID) AS click_user_num,{$time} AS dateline from `mininews{$ymd}` group by type,pos";
        return $this->query($sql);
    }
    
    public function get_data_qid($ymd){
        $time = time();
        $sql = "select {$ymd} AS ymd,type AS news_type,QID AS qid,count(1) AS click_num,{$time} AS dateline from `mininews{$ymd}` group by type,QID";
        return $this->query($sql);
    }
}

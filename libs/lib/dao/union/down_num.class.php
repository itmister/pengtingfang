<?php
namespace Dao\Union;
use \Dao;
class Down_num extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Down_num
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 下载次数加1
     * @param $softId = 'collection';
     * @param $softName = '合集包';
     * @param $ymd = date("Ymd");
     * @param $dateline = time();
     * @param $d_num = 1;
     */
    public function set_d_num($softId,$softName,$ymd,$dateline,$d_num){
        $sql = "INSERT INTO {$this->_realTableName} (soft_id,ymd,d_num,soft_name,dateline) VALUES ('{$softId}',{$ymd},{$d_num},'{$softName}',{$dateline}) ON DUPLICATE KEY UPDATE d_num = d_num+{$d_num},dateline = {$dateline}";
        //echo $sql;
        return  $this->exec($sql);
    }

    /**
     * 申请次数加1
     * @param $softId = 'collection';
     * @param $softName = '合集包';
     * @param $ymd = date("Ymd");
     * @param $dateline = time();
     * @param $a_num = 1;
     */
    public function set_a_num($softId,$softName,$ymd,$dateline,$a_num){
        $sql = "INSERT INTO {$this->_realTableName} (soft_id,ymd,a_num,soft_name,dateline) VALUES ('{$softId}',{$ymd},{$a_num},'{$softName}',{$dateline}) ON DUPLICATE KEY UPDATE a_num = a_num+{$a_num},dateline = {$dateline}";
        return  $this->exec($sql);
    }
}

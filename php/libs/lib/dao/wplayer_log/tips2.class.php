<?php
namespace Dao\Wplayer_log;
use \Dao;
class Tips2 extends Wplayer_log {
    /**
     * @return Tips2
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    
    public function get_data($ymd){
        $time = time();
        $sql = "
                SELECT
                	{$ymd} AS ymd,
                	`name`,
                	COUNT(DISTINCT UID) AS num,
                	{$time} AS created
                FROM
                	tips2{$ymd}
                GROUP BY
                	`name`
            ";
        $data = $this->query($sql);
        return $data;
    }
    
    public function get_time_qid_count($ymd){
        $sql = "
            SELECT sum(a.num) AS num FROM(
                SELECT COUNT(distinct `name`) AS num FROM tips2{$ymd} GROUP BY FROM_UNIXTIME(ClientTime,'%k')
            ) as a
        ";
        $query_res = current($this->query($sql));
        return $query_res['num'] ? $query_res['num'] : 0;
    }
    
    //时间点数据
    public function get_time_data($ymd,$limit){
        $time = time();
        $sql = "
            SELECT
                {$ymd} AS ymd,
                `name`,
                FROM_UNIXTIME(ClientTime,'%k') AS h,
                COUNT(DISTINCT UID) AS `install`,
                {$time} AS dateline
            FROM
                tips2{$ymd}
            GROUP BY
                `name`,h
            LIMIT {$limit}
        ";
        return $this->query($sql);
    }
    
}

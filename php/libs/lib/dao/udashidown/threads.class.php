<?php
namespace Dao\Udashidown;
use \Dao;
class Threads extends Udashidown{

    /**
     * @return Threads
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_article_num($ymd){
        $sql = "SELECT {$ymd} as ymd,count(*) as article_num,
        (SELECT a.username FROM pc_members as a where a.uid=pc_threads.uid) as username,UNIX_TIMESTAMP() as dateline
        FROM pc_threads where FROM_UNIXTIME(dateline,'%Y%m%d')={$ymd} GROUP BY uid";
        return $this->query($sql);
    }
}

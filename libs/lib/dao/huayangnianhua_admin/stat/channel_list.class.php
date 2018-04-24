<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao\Huayangnianhua_admin;
class Channel_list extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    /**
     * @return Channel_list
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    #取我们自己的渠道
    public function get_channel_list($ymd){
        $sql = "select {$ymd} as ymd,channel_name as qid,1 as is_me,UNIX_TIMESTAMP() as dateline from `{$this->_realTableName}` ;";
        return $this->query($sql);
    }
}
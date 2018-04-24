<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao\Wplayer_admin;
class Active_uid_only extends \Dao\Wplayer_admin\Wplayer_admin {

    /**
     * @return Active_uid_only
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function get_active_count($ymd){
        $sql = "SELECT count(case when Ymd = {$ymd} then Ymd end) as `active_first` FROM `{$this->_realTableName}`";
        return $this->query($sql);
    }



}

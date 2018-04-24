<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao\Huayangnianhua_admin;
class Active_uid_only extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

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

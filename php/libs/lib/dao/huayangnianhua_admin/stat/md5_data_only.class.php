<?php
namespace Dao\Huayangnianhua_admin\Stat;
class Md5_data_only extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    protected static $_instance = null;
    /**
     * @return Md5_data_only
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function get_install_data($ymd){
        $time = time();
        $sql = "SELECT {$ymd} AS ymd,`md5`,ver,count(UID) AS install_num,{$time} AS dateline FROM `{$this->_get_table_name()}` WHERE ymd = {$ymd} GROUP BY `md5`";
        return $this->query($sql);
    }
}

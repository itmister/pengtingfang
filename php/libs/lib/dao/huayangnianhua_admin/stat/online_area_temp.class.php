<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao;
class Online_area_temp extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    protected static $_instance = null;
    /**
     * @return Online_area_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function get_data(){
        $sql = "SELECT ymd,count(ymd) as online,province,city FROM `{$this->_realTableName}` GROUP BY province,city";
        return $this->query($sql);
    }

}

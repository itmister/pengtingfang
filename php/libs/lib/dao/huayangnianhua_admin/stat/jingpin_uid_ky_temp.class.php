<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao;
class Jingpin_uid_ky_temp extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    protected static $_instance = null;
    /**
     * @return Jingpin_uid_ky_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

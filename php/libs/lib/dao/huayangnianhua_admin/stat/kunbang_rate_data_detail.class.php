<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao;
class Kunbang_rate_data_detail extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    protected static $_instance = null;
    /**
     * @return Kunbang_rate_data_detail
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

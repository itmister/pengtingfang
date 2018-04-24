<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao;
class Mininews_detail_data extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Huayangnianhua_admin\Stat\Mininews_detail_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

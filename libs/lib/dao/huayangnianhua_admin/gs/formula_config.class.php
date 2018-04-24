<?php
namespace Dao\Huayangnianhua_admin\Gs;
use \Dao\Huayangnianhua_admin\Huayangnianhua_admin;

class Formula_config extends Huayangnianhua_admin {

    protected static $_instance = null;

    /**
     * @return Formula_config
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

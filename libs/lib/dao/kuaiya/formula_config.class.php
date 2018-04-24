<?php
namespace Dao\Kuaiya;
class Formula_config extends  Kuaiya{

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

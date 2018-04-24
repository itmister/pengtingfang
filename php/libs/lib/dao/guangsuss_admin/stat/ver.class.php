<?php
namespace Dao\Guangsuss_admin\Stat;
class Ver extends \Dao\Guangsuss_admin\Guangsuss_admin {

    protected static $_instance = null;
    /**
     * @return Ver
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

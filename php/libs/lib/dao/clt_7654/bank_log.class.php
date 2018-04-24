<?php
/**
 * @desc 用户收入表;
 */
namespace Dao\Clt_7654;
use \Dao;
class Bank_log extends Clt_7654 {
    
    
    protected static $_instance = null;

    /**
     * @return Bank_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
?>

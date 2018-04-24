<?php
/**
 * @desc 活动收入表;
 */
namespace Dao\Clt_7654;
use \Dao;
class Activity_income extends Clt_7654 {
    
    
    protected static $_instance = null;

    /**
     * @return Activity_income
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
?>

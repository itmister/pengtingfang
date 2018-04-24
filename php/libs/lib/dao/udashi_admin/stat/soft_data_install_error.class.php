<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Soft_data_install_error extends \Dao\Udashi_admin\Udashi_admin {

    protected static $_instance = null;
    /**
     * @return Soft_data_install_error
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
  
    
    
}

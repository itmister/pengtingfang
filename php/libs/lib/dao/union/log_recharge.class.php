<?php
namespace Dao\Union;
use \Dao;

class Log_recharge extends Union {
  
    protected static $_instance = null;
    
    /**
     * @return Dao\Union\Log_recharge
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
   
    
}

?>
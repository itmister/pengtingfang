<?php
/**
 * @desc 数据抓取日志记录表;
 * @author caolei
 */
namespace Dao\Union;
use \Dao;
class Jsdh_auto_data extends Union {
    
    
    protected static $_instance = null;

    /**
     * @return Dao\Union\Jsdh_auto_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
?>

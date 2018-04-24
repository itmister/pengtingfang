<?php
/**
 * @desc 数据抓取日志记录表;
 * @author caolei
 */
namespace Dao\Union;
use \Dao;
class Ad_fafang_time extends Union {
    
    
    protected static $_instance = null;

    /**
     * @return Dao\Union\Ad_fafang_time
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
?>

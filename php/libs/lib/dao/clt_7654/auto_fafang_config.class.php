<?php
/**
 * @desc 软件自动抓取配置
 * @author huxiaowei1238
 */
namespace Dao\Online_7654;
class Auto_fafang_config extends Online_7654 {
    protected static $_instance = null;
    /**
     * @return Auto_fafang_config
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
?>

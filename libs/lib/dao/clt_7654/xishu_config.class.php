<?php
/**
 * @desc 软件自动抓取配置
 * @author huxiaowei1238
 */
namespace Dao\Clt_7654;
class Xishu_config extends Clt_7654 {
    protected static $_instance = null;
    /**
     * @return Xishu_config
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
?>

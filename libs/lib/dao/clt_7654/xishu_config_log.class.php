<?php
/**
 * @desc 自动发放业绩日志
 * @author huxiaowei1238
 */
namespace Dao\Clt_7654;
class Xishu_config_log extends Clt_7654 {
    protected static $_instance = null;
    /**
     * @return Xishu_config_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
?>

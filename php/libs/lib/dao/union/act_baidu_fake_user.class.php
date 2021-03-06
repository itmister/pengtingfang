<?php
/**
 * 百度活动
 */
namespace Dao\Union;
class Act_baidu_fake_user extends Union {

    protected static $_instance = null;

    /**
     * @return Act_baidu_fake_user
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

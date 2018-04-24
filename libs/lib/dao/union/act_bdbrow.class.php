<?php
/**
 * 百度浏览器活动
 */
namespace Dao\Union;
class Act_bdbrow extends Union {

    protected static $_instance = null;

    /**
     * @return Act_bdbrow
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}
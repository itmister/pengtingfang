<?php
/**
 * winhome活动数据
 */
namespace Dao\Union;
class Act_winhome_data extends Union {

    protected static $_instance = null;

    /**
     * @return Act_winhome_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

<?php
namespace Dao\Union;
class Act_qq_delay extends Union {

    protected static $_instance = null;

    /**
     * @return Act_qq_delay
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}
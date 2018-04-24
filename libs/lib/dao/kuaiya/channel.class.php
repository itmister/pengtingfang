<?php
namespace Dao\Kuaiya;
class Channel extends  Kuaiya{

    protected static $_instance = null;

    /**
     * @return Channel
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

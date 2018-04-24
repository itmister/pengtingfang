<?php
namespace Dao\Kuaiya;
class Blacklist extends Kuaiya{

    protected static $_instance = null;

    /**
     * @return Blacklist
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

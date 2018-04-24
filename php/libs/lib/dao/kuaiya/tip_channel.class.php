<?php
namespace Dao\Kuaiya;
class Tip_channel extends  Kuaiya{

    protected static $_instance = null;

    /**
     * @return Tip_channel
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

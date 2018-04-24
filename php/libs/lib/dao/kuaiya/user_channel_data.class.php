<?php
namespace Dao\Kuaiya;
class User_channel_data extends  Kuaiya{

    protected static $_instance = null;

    /**
     * @return User_channel_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}

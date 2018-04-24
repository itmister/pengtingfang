<?php
namespace Dao\Winhome;
class Sub_channel_data_uuid_out extends Winhome {

    protected static $_instance = null;
    /**
     * @return Ver
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

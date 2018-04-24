<?php
namespace Dao\Mykzip_admin;
class Ad_admin extends  Mykzip_admin{

    protected static $_instance = null;

    /**
     * @return Ad_admin
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

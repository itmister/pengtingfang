<?php
namespace Dao\Guangsuss_admin;
use \Dao;
class Gs_website_category extends Guangsuss_admin {

    protected static $_instance = null;
    /**
     * @return Gs_website_category
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

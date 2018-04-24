<?php
namespace Dao\Bagua;
use \Dao;
class Posts extends Bagua {
    protected static $_instance = null;
    /**
     * @return Dao\Bagua\Posts
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}

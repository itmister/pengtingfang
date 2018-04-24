<?php
namespace Dao\Bagua;
use \Dao;
class Term_taxonomy extends Bagua {
    protected static $_instance = null;
    /**
     * @return Dao\Bagua\Term_taxonomy
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}

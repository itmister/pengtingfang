<?php
namespace Dao\Bagua;
use \Dao;
class Term_relationships extends Bagua {
    protected static $_instance = null;
    /**
     * @return Dao\Bagua\Term_relationships
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}

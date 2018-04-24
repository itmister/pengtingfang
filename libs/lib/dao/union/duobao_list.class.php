<?php
/**
 * duobao_list
 */
namespace Dao\Union;
use \Dao;

class Duobao_list extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Duobao_list
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}

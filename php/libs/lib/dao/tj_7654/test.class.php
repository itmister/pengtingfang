<?php
namespace Dao\Tj_7654;
use \Dao;
class Test extends Tj_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Tj_7654\Test
     */
    public static function instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

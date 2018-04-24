<?php
namespace Dao\Union;
use \Dao;
class Stat extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Stat
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function test() {

        return $this->query("select * from user limit 10");
    }
}

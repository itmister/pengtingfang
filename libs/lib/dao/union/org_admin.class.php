<?php

namespace Dao\Union;
use \Dao;
class Org_admin extends Union {

    protected static $_instance = null;

    /**
     * @return Org_admin
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

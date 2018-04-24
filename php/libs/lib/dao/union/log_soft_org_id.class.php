<?php

namespace Dao\Union;
use \Dao;
class Log_soft_org_id extends Union {
    protected static $_instance = null;
    /**
     * @return Log_soft_org_id
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}

<?php
namespace Dao\Union;
use Dao;

/**
 *  推Q管领话费名单管理
 * Class phone_bill
 * @package Dao\Union
 */
class Phone_bill extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Phone_bill
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}